<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingPlan;
use App\Services\MarketingPlanService;
use App\Services\MarketingScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->can('view-marketing')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $scope = MarketingScopeService::for(Auth::user());
        $year = (int) $request->get('year', now()->year);

        $plans = $scope->plansQuery()
            ->with(['manager:id,name', 'campaign:id,name'])
            ->withCount('activities')
            ->where('year', $year)
            ->orderByDesc('month')
            ->get();

        $activePlan = $plans->firstWhere('status', MarketingPlan::STATUS_ACTIVE)
            ?? $plans->firstWhere('month', now()->month);

        return view('marketing.plans.index', [
            'plans' => $plans,
            'year' => $year,
            'activePlan' => $activePlan,
            'isManager' => $scope->isManagerScope(),
        ]);
    }

    public function create()
    {
        $this->authorizeManage();

        return view('marketing.plans.form', array_merge(['plan' => null], $this->formData()));
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
        $data = $this->validated($request);

        $plan = MarketingPlan::create([
            ...$data,
            'manager_id' => Auth::id(),
            'created_by' => Auth::id(),
            'status' => $request->input('status', MarketingPlan::STATUS_DRAFT),
        ]);

        return redirect()->route('marketing.plans.show', $plan)
            ->with('success', 'تم إنشاء خطة التسويق الشهرية. يمكنك الآن توزيع المهام على الفريق.');
    }

    public function show(MarketingPlan $plan)
    {
        $this->authorizePlan($plan);
        $scope = MarketingScopeService::for(Auth::user());
        $planService = MarketingPlanService::for(Auth::user());

        $plan->load(['campaign:id,name', 'manager:id,name', 'activities.assignee:id,name']);

        $stats = [
            'total' => $plan->activities->count(),
            'completed' => $plan->activities->where('status', 'completed')->count(),
            'overdue' => $plan->activities->filter(fn ($a) => $a->isOverdue())->count(),
            'today' => $plan->activities->filter(fn ($a) => $a->due_at?->isToday())->count(),
        ];

        return view('marketing.plans.show', [
            'plan' => $plan,
            'calendar' => $planService->calendarByDay($plan),
            'stats' => $stats,
            'isManager' => $scope->isManagerScope(),
            'assignableUsers' => $scope->assignableUsers(),
            'types' => config('marketing.activity_types'),
            'priorities' => config('marketing.priorities'),
        ]);
    }

    public function edit(MarketingPlan $plan)
    {
        $this->authorizePlan($plan);
        $this->authorizeManage();

        return view('marketing.plans.form', array_merge(['plan' => $plan], $this->formData()));
    }

    public function update(Request $request, MarketingPlan $plan)
    {
        $this->authorizePlan($plan);
        $this->authorizeManage();

        $plan->update($this->validated($request));

        return redirect()->route('marketing.plans.show', $plan)->with('success', 'تم تحديث الخطة.');
    }

    public function storeTasks(Request $request, MarketingPlan $plan)
    {
        $this->authorizePlan($plan);
        $this->authorizeManage();

        $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.assigned_to' => 'required|exists:users,id',
            'tasks.*.due_day' => 'required|integer|min:1|max:31',
            'tasks.*.type' => 'nullable|in:' . implode(',', array_keys(config('marketing.activity_types'))),
            'tasks.*.priority' => 'nullable|in:' . implode(',', array_keys(config('marketing.priorities'))),
        ]);

        $count = MarketingPlanService::for(Auth::user())
            ->createTasksFromRows($plan, $request->input('tasks', []), Auth::id());

        return back()->with('success', "تم إضافة {$count} مهمة للخطة.");
    }

    public function distribute(Request $request, MarketingPlan $plan)
    {
        $this->authorizePlan($plan);
        $this->authorizeManage();

        $request->validate([
            'task_lines' => 'required|string',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:users,id',
        ]);

        $titles = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $request->task_lines)));
        $count = MarketingPlanService::for(Auth::user())
            ->distributeEvenly($plan, $titles, $request->employee_ids, Auth::id());

        return back()->with('success', "تم توزيع {$count} مهمة على الفريق خلال الشهر.");
    }

    public function activate(MarketingPlan $plan)
    {
        $this->authorizePlan($plan);
        $this->authorizeManage();

        MarketingPlan::query()
            ->where('year', $plan->year)
            ->where('month', $plan->month)
            ->where('id', '!=', $plan->id)
            ->where('status', MarketingPlan::STATUS_ACTIVE)
            ->update(['status' => MarketingPlan::STATUS_ARCHIVED]);

        $plan->update(['status' => MarketingPlan::STATUS_ACTIVE]);

        return back()->with('success', 'تم تفعيل الخطة الشهرية.');
    }

    protected function formData(): array
    {
        $scope = MarketingScopeService::for(Auth::user());

        return [
            'campaigns' => $scope->campaignsQuery()->orderBy('name')->get(['id', 'name']),
            'statuses' => config('marketing.plan_statuses'),
            'defaultMonth' => now()->month,
            'defaultYear' => now()->year,
        ];
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'status' => 'nullable|in:' . implode(',', array_keys(config('marketing.plan_statuses'))),
            'campaign_id' => 'nullable|exists:marketing_campaigns,id',
        ]);
    }

    protected function authorizeManage(): void
    {
        if (!Auth::user()->can('create-marketing') || !MarketingScopeService::for(Auth::user())->isManagerScope()) {
            abort(403);
        }
    }

    protected function authorizePlan(MarketingPlan $plan): void
    {
        $exists = MarketingScopeService::for(Auth::user())
            ->plansQuery()
            ->where('id', $plan->id)
            ->exists();

        if (!$exists) {
            abort(404);
        }
    }
}

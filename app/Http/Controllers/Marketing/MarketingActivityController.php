<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingActivity;
use App\Services\MarketingRecurrenceService;
use App\Services\MarketingScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingActivityController extends Controller
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
        $date = $request->filled('date') ? Carbon::parse($request->date) : now();
        $view = $request->get('view', 'week');

        $base = $scope->activitiesQuery()->with(['assignee:id,name', 'campaign:id,name', 'plan:id,title,month,year']);

        if ($view === 'day') {
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
        } elseif ($view === 'month') {
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
        } else {
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
        }

        $activities = (clone $base)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->marketing_plan_id, fn ($q) => $q->where('marketing_plan_id', $request->marketing_plan_id))
            ->when($request->assigned_to && $scope->isManagerScope(), fn ($q) => $q->where('assigned_to', $request->assigned_to))
            ->whereBetween('due_at', [$start, $end])
            ->orderBy('due_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'today' => (clone $base)->dueToday()->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'recurring' => (clone $base)->pending()->where('recurrence', '!=', 'none')->count(),
        ];

        $monthCalendar = [];
        if ($view === 'month') {
            $grouped = (clone $base)
                ->when($request->marketing_plan_id, fn ($q) => $q->where('marketing_plan_id', $request->marketing_plan_id))
                ->when($request->assigned_to && $scope->isManagerScope(), fn ($q) => $q->where('assigned_to', $request->assigned_to))
                ->whereBetween('due_at', [$start, $end])
                ->with(['assignee:id,name'])
                ->orderBy('due_at')
                ->get()
                ->groupBy(fn ($a) => $a->due_at?->format('j'));

            for ($d = 1; $d <= $end->day; $d++) {
                $monthCalendar[$d] = $grouped->get((string) $d, collect());
            }
        }

        return view('marketing.activities.index', [
            'activities' => $activities,
            'stats' => $stats,
            'date' => $date,
            'view' => $view,
            'monthCalendar' => $monthCalendar,
            'assignableUsers' => $scope->isManagerScope() ? $scope->assignableUsers() : [],
            'plans' => $scope->plansQuery()->orderByDesc('year')->orderByDesc('month')->limit(12)->get(['id', 'title', 'month', 'year']),
            'isManager' => $scope->isManagerScope(),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorizeCreate();

        return view('marketing.activities.create', $this->formData($request));
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        $data = $this->validated($request);
        $data['assigned_by'] = Auth::id();

        if (empty($data['assigned_to'])) {
            $data['assigned_to'] = Auth::id();
        }

        MarketingActivity::create($data);

        return redirect()->route('marketing.activities.index')->with('success', 'تم إنشاء المهمة التسويقية.');
    }

    public function edit(MarketingActivity $activity)
    {
        $this->authorizeActivity($activity);
        $this->authorizeEdit();

        return view('marketing.activities.edit', array_merge(['activity' => $activity], $this->formData()));
    }

    public function update(Request $request, MarketingActivity $activity)
    {
        $this->authorizeActivity($activity);
        $this->authorizeEdit();
        $activity->update($this->validated($request));

        return redirect()->route('marketing.activities.index')->with('success', 'تم تحديث المهمة.');
    }

    public function updateStatus(Request $request, MarketingActivity $activity, MarketingRecurrenceService $recurrence)
    {
        $this->authorizeActivity($activity);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'completion_notes' => 'nullable|string',
        ]);

        $activity->update([
            'status' => $request->status,
            'completion_notes' => $request->completion_notes,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        if ($request->status === 'completed') {
            $recurrence->scheduleNextOnComplete($activity);
        }

        return back()->with('success', 'تم تحديث حالة المهمة.');
    }

    public function destroy(MarketingActivity $activity)
    {
        $this->authorizeActivity($activity);

        if (!Auth::user()->can('delete-marketing')) {
            abort(403);
        }

        $activity->delete();

        return redirect()->route('marketing.activities.index')->with('success', 'تم حذف المهمة.');
    }

    protected function formData(?Request $request = null): array
    {
        $scope = MarketingScopeService::for(Auth::user());

        return [
            'campaigns' => $scope->campaignsQuery()->orderBy('name')->get(['id', 'name']),
            'assignableUsers' => $scope->assignableUsers(),
            'types' => config('marketing.activity_types'),
            'statuses' => config('marketing.activity_statuses'),
            'priorities' => config('marketing.priorities'),
            'recurrences' => config('marketing.recurrence'),
            'prefillCampaign' => $request?->get('campaign_id'),
            'prefillPlan' => $request?->get('marketing_plan_id'),
            'plans' => $scope->plansQuery()->orderByDesc('year')->orderByDesc('month')->get(['id', 'title', 'month', 'year']),
        ];
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', array_keys(config('marketing.activity_types'))),
            'status' => 'required|in:' . implode(',', array_keys(config('marketing.activity_statuses'))),
            'priority' => 'required|in:' . implode(',', array_keys(config('marketing.priorities'))),
            'campaign_id' => 'nullable|exists:marketing_campaigns,id',
            'marketing_plan_id' => 'nullable|exists:marketing_plans,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_at' => 'nullable|date',
            'recurrence' => 'required|in:' . implode(',', array_keys(config('marketing.recurrence'))),
            'recurrence_interval' => 'nullable|integer|min:1|max:12',
            'notes' => 'nullable|string',
        ]);
    }

    protected function authorizeCreate(): void
    {
        if (!Auth::user()->can('create-marketing')) {
            abort(403);
        }
    }

    protected function authorizeEdit(): void
    {
        if (!Auth::user()->can('edit-marketing')) {
            abort(403);
        }
    }

    protected function authorizeActivity(MarketingActivity $activity): void
    {
        $exists = MarketingScopeService::for(Auth::user())
            ->activitiesQuery()
            ->where('id', $activity->id)
            ->exists();

        if (!$exists) {
            abort(404);
        }
    }
}

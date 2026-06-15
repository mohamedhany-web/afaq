<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\DailySalesReport;
use App\Models\User;
use App\Services\Crm\CrmFilterService;
use App\Services\CrmEmployeeService;
use App\Services\CrmNotificationService;
use App\Services\CrmRoleResolver;
use App\Services\CrmScopeService;
use App\Services\DailySalesReportMetricsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmDailySalesReportController extends Controller
{
    public function __construct(
        protected DailySalesReportMetricsService $metricsService,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', DailySalesReport::class);

        $user = Auth::user();
        $role = CrmRoleResolver::for($user);
        $scope = CrmScopeService::for($user);

        $query = $this->visibleReportsQuery($scope, $user, $role);
        $this->applyIndexFilters($query, $request, $role);

        $stats = [
            'submitted' => (clone $query)->where('status', DailySalesReport::STATUS_SUBMITTED)->count(),
            'today' => (clone $query)->whereDate('report_date', today())->count(),
        ];

        $reports = (clone $query)
            ->with('author:id,name')
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $teamMembers = collect();
        if ($role->isAdmin() || $role->isManager()) {
            $memberIds = $role->isAdmin()
                ? User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)->pluck('id')
                : collect($scope->managedTeamMemberUserIds())
                    ->filter(fn ($id) => (int) $id !== (int) $user->id);

            $teamMembers = User::whereIn('id', $memberIds)->orderBy('name')->get(['id', 'name']);
        }

        $data = compact('reports', 'teamMembers', 'stats', 'role');

        return match ($role->workspace()) {
            CrmRoleResolver::WORKSPACE_ADMIN => view('crm.admin.daily-reports.index', $data),
            CrmRoleResolver::WORKSPACE_MANAGER => view('crm.manager.daily-reports.index', $data),
            default => view('crm.rep.daily-reports.index', $data),
        };
    }

    public function generate(Request $request)
    {
        $this->authorize('create', DailySalesReport::class);

        $validated = $request->validate([
            'report_date' => 'required|date|before_or_equal:today',
        ]);

        $user = Auth::user();
        $date = Carbon::parse($validated['report_date'])->toDateString();

        $report = DailySalesReport::firstOrNew([
            'user_id' => $user->id,
            'report_date' => $date,
        ]);

        if ($report->exists && $report->isSubmitted()) {
            return redirect()
                ->route('crm.daily-reports.show', $report)
                ->with('error', 'تم رفع هذا التقرير مسبقاً ولا يمكن تعديله.');
        }

        $report->status = DailySalesReport::STATUS_DRAFT;
        $report->metrics = $this->metricsService->build($user, $date);
        $report->save();

        return redirect()
            ->route('crm.daily-reports.show', $report)
            ->with('success', 'تم إنشاء التقرير وملء البيانات من النظام.');
    }

    public function show(DailySalesReport $dailySalesReport)
    {
        $this->authorize('view', $dailySalesReport);

        $dailySalesReport->load('author:id,name');

        return view('crm.daily-reports.show', [
            'report' => $dailySalesReport,
            'canEdit' => Auth::user()->can('update', $dailySalesReport),
            'role' => CrmRoleResolver::for(Auth::user()),
        ]);
    }

    public function update(Request $request, DailySalesReport $dailySalesReport)
    {
        $this->authorize('update', $dailySalesReport);

        $validated = $request->validate([
            'obstacles' => 'nullable|string|max:10000',
            'support_required' => 'nullable|string|max:10000',
            'tomorrow_planned_calls' => 'nullable|integer|min:0|max:9999',
            'tomorrow_planned_meetings' => 'nullable|integer|min:0|max:9999',
            'tomorrow_planned_visits' => 'nullable|integer|min:0|max:9999',
            'tomorrow_priority_leads' => 'nullable|string|max:10000',
        ]);

        $dailySalesReport->update($validated);

        return back()->with('success', 'تم حفظ التعديلات.');
    }

    public function refresh(DailySalesReport $dailySalesReport)
    {
        $this->authorize('update', $dailySalesReport);

        $author = User::findOrFail($dailySalesReport->user_id);
        $dailySalesReport->update([
            'metrics' => $this->metricsService->build($author, $dailySalesReport->report_date),
        ]);

        return back()->with('success', 'تم تحديث الأرقام من النظام.');
    }

    public function submit(Request $request, DailySalesReport $dailySalesReport)
    {
        $this->authorize('submit', $dailySalesReport);

        $validated = $request->validate([
            'obstacles' => 'nullable|string|max:10000',
            'support_required' => 'nullable|string|max:10000',
            'tomorrow_planned_calls' => 'nullable|integer|min:0|max:9999',
            'tomorrow_planned_meetings' => 'nullable|integer|min:0|max:9999',
            'tomorrow_planned_visits' => 'nullable|integer|min:0|max:9999',
            'tomorrow_priority_leads' => 'nullable|string|max:10000',
        ]);

        $dailySalesReport->fill($validated);
        $author = User::findOrFail($dailySalesReport->user_id);
        $dailySalesReport->metrics = $this->metricsService->build($author, $dailySalesReport->report_date);
        $dailySalesReport->status = DailySalesReport::STATUS_SUBMITTED;
        $dailySalesReport->submitted_at = now();
        $dailySalesReport->save();

        $this->notifyManagersAndAdmins($dailySalesReport);

        return redirect()
            ->route('crm.daily-reports.index')
            ->with('success', 'تم رفع التقرير بنجاح.');
    }

    protected function applyIndexFilters($query, Request $request, CrmRoleResolver $role): void
    {
        $filters = CrmFilterService::for($request->user());
        $salesRepId = $filters->resolveSalesRepId($request);

        if ($salesRepId && ($role->isAdmin() || $role->isManager())) {
            $query->where('user_id', $salesRepId);
        } elseif ($request->filled('user_id') && ($role->isAdmin() || $role->isManager())) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }
    }

    protected function visibleReportsQuery(CrmScopeService $scope, User $user, CrmRoleResolver $role)
    {
        if ($role->isAdmin()) {
            return DailySalesReport::query()->where('status', DailySalesReport::STATUS_SUBMITTED);
        }

        if ($role->isManager()) {
            $memberIds = $scope->managedTeamMemberUserIds();

            return DailySalesReport::query()
                ->whereIn('user_id', $memberIds)
                ->where('status', DailySalesReport::STATUS_SUBMITTED);
        }

        return DailySalesReport::query()->where('user_id', $user->id);
    }

    protected function notifyManagersAndAdmins(DailySalesReport $report): void
    {
        $report->load(['author:id,name', 'author.salesTeams:id,manager_id']);
        $authorName = $report->author->name;
        $dateLabel = $report->report_date->format('Y-m-d');
        $url = route('crm.daily-reports.show', $report);

        $recipientIds = collect();

        foreach ($report->author->salesTeams ?? [] as $team) {
            if ($team->manager_id) {
                $recipientIds->push($team->manager_id);
            }
        }

        $recipientIds = $recipientIds
            ->merge(User::role(['super_admin', 'admin'])->pluck('id'))
            ->unique()
            ->filter(fn ($id) => (int) $id !== (int) $report->user_id);

        foreach ($recipientIds as $userId) {
            CrmNotificationService::notify(
                $userId,
                'crm_daily_report',
                'تقرير مبيعات يومي',
                "رفع {$authorName} تقرير المبيعات ليوم {$dateLabel}",
                ['url' => $url, 'report_id' => $report->id],
            );
        }
    }
}

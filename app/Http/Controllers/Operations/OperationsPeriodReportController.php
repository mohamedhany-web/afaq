<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\OperationsPeriodReport;
use App\Models\User;
use App\Services\Operations\OperationsReportTeamStatusService;
use App\Services\OperationsReportMetricsService;
use App\Services\OperationsRoleResolver;
use App\Services\OperationsScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsPeriodReportController extends Controller
{
    public function __construct(
        protected OperationsReportMetricsService $metricsService,
        protected OperationsReportTeamStatusService $teamStatus,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', OperationsPeriodReport::class);

        $user = Auth::user();
        $resolver = OperationsRoleResolver::for($user);
        $scope = OperationsScopeService::for($user);
        $periodType = $request->get('period', OperationsPeriodReport::PERIOD_DAILY);

        if (!array_key_exists($periodType, config('operations_reports.period_types', []))) {
            $periodType = OperationsPeriodReport::PERIOD_DAILY;
        }

        $query = $scope->reportsQuery()->where('period_type', $periodType);
        $this->applyFilters($query, $request, $resolver);

        $stats = [
            'submitted' => (clone $query)->where('status', OperationsPeriodReport::STATUS_SUBMITTED)->count(),
            'draft' => (clone $query)->where('status', OperationsPeriodReport::STATUS_DRAFT)->count(),
        ];

        $reports = (clone $query)
            ->with('author:id,name')
            ->orderByDesc('period_start')
            ->paginate(20)
            ->withQueryString();

        $authors = collect();
        if ($resolver->isAdmin()) {
            $authors = User::role(\App\Services\OperationsEmployeeService::LEGACY_MANAGER_ROLES)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $anchor = $request->filled('anchor_date')
            ? Carbon::parse($request->anchor_date)
            : now();
        $salesRepRows = $this->teamStatus->salesRepsForPeriod($periodType, $anchor);

        return view('operations.reports.index', compact(
            'reports', 'authors', 'stats', 'resolver', 'periodType', 'salesRepRows'
        ));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'period_type' => 'required|in:daily,weekly,monthly',
            'anchor_date' => 'required|date|before_or_equal:today',
        ]);

        $user = Auth::user();
        $this->authorize('viewAny', OperationsPeriodReport::class);

        if (!$user->isOperationsManager() && !$user->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }

        $period = OperationsPeriodReport::resolvePeriod(
            $validated['period_type'],
            Carbon::parse($validated['anchor_date'])
        );

        $report = OperationsPeriodReport::firstOrNew([
            'user_id' => $user->id,
            'period_type' => $validated['period_type'],
            'period_start' => $period['start']->toDateString(),
        ]);

        if ($report->exists && $report->isSubmitted()) {
            return redirect()
                ->route('operations.reports.show', $report)
                ->with('error', 'تم رفع هذا التقرير مسبقاً ولا يمكن تعديله.');
        }

        $report->period_end = $period['end']->toDateString();
        $report->status = OperationsPeriodReport::STATUS_DRAFT;
        $report->metrics = $this->metricsService->build(
            $user,
            $validated['period_type'],
            $period['start'],
            $period['end']
        );
        $report->save();

        return redirect()
            ->route('operations.reports.show', $report)
            ->with('success', 'تم إنشاء التقرير وملء البيانات من النظام.');
    }

    public function show(OperationsPeriodReport $operationsPeriodReport)
    {
        $this->authorize('view', $operationsPeriodReport);
        $operationsPeriodReport->load('author:id,name');

        return view('operations.reports.show', [
            'report' => $operationsPeriodReport,
            'canEdit' => Auth::user()->can('update', $operationsPeriodReport),
            'canAnnotate' => Auth::user()->can('annotate', $operationsPeriodReport),
            'resolver' => OperationsRoleResolver::for(Auth::user()),
            'metricLabels' => config('operations_reports.metric_labels', []),
        ]);
    }

    public function update(Request $request, OperationsPeriodReport $operationsPeriodReport)
    {
        $this->authorize('update', $operationsPeriodReport);

        $validated = $request->validate([
            'operations_summary' => 'nullable|string|max:15000',
            'projects_progress' => 'nullable|string|max:15000',
            'team_coordination' => 'nullable|string|max:15000',
            'obstacles' => 'nullable|string|max:10000',
            'support_required' => 'nullable|string|max:10000',
            'next_period_plan' => 'nullable|string|max:10000',
            'notes' => 'nullable|string|max:10000',
        ]);

        $operationsPeriodReport->update($validated);

        return back()->with('success', 'تم حفظ التعديلات.');
    }

    public function annotate(Request $request, OperationsPeriodReport $operationsPeriodReport)
    {
        $this->authorize('annotate', $operationsPeriodReport);

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:10000',
        ]);

        $operationsPeriodReport->update($validated);

        return back()->with('success', 'تم حفظ ملاحظات الإدارة.');
    }

    public function refresh(OperationsPeriodReport $operationsPeriodReport)
    {
        $this->authorize('update', $operationsPeriodReport);

        $author = User::findOrFail($operationsPeriodReport->user_id);
        $operationsPeriodReport->update([
            'metrics' => $this->metricsService->build(
                $author,
                $operationsPeriodReport->period_type,
                $operationsPeriodReport->period_start,
                $operationsPeriodReport->period_end
            ),
        ]);

        return back()->with('success', 'تم تحديث الأرقام من النظام.');
    }

    public function submit(Request $request, OperationsPeriodReport $operationsPeriodReport)
    {
        $this->authorize('submit', $operationsPeriodReport);

        $validated = $request->validate([
            'operations_summary' => 'nullable|string|max:15000',
            'projects_progress' => 'nullable|string|max:15000',
            'team_coordination' => 'nullable|string|max:15000',
            'obstacles' => 'nullable|string|max:10000',
            'support_required' => 'nullable|string|max:10000',
            'next_period_plan' => 'nullable|string|max:10000',
            'notes' => 'nullable|string|max:10000',
        ]);

        $author = User::findOrFail($operationsPeriodReport->user_id);
        $operationsPeriodReport->fill($validated);
        $operationsPeriodReport->metrics = $this->metricsService->build(
            $author,
            $operationsPeriodReport->period_type,
            $operationsPeriodReport->period_start,
            $operationsPeriodReport->period_end
        );
        $operationsPeriodReport->status = OperationsPeriodReport::STATUS_SUBMITTED;
        $operationsPeriodReport->submitted_at = now();
        $operationsPeriodReport->save();

        return redirect()
            ->route('operations.reports.index', ['period' => $operationsPeriodReport->period_type])
            ->with('success', 'تم رفع التقرير للإدارة بنجاح.');
    }

    protected function applyFilters($query, Request $request, OperationsRoleResolver $resolver): void
    {
        if ($request->filled('user_id') && $resolver->isAdmin()) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }
}

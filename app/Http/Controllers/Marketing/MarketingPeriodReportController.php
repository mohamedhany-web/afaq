<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingPeriodReport;
use App\Models\User;
use App\Services\MarketingReportComplianceService;
use App\Services\MarketingReportMetricsService;
use App\Services\MarketingRoleResolver;
use App\Services\MarketingScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingPeriodReportController extends Controller
{
    public function __construct(
        protected MarketingReportMetricsService $metricsService,
        protected MarketingReportComplianceService $compliance,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', MarketingPeriodReport::class);

        $user = Auth::user();
        $resolver = MarketingRoleResolver::for($user);
        $scope = MarketingScopeService::for($user);
        $periodType = $request->get('period', MarketingPeriodReport::PERIOD_DAILY);

        if (!array_key_exists($periodType, config('marketing_reports.period_types', []))) {
            $periodType = MarketingPeriodReport::PERIOD_DAILY;
        }

        $query = $this->visibleReportsQuery($scope, $user, $resolver);
        $query->where('period_type', $periodType);
        $this->applyFilters($query, $request, $resolver);

        $stats = [
            'submitted' => (clone $query)->where('status', MarketingPeriodReport::STATUS_SUBMITTED)->count(),
            'draft' => (clone $query)->where('status', MarketingPeriodReport::STATUS_DRAFT)->count(),
        ];

        $reports = (clone $query)
            ->with('author:id,name')
            ->orderByDesc('period_start')
            ->paginate(20)
            ->withQueryString();

        $teamMembers = collect();
        if ($resolver->isAdmin() || $resolver->isManager()) {
            $teamMembers = User::whereIn('id', $scope->teamUserIds())
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $myPending = $this->compliance->pendingFor($user);
        $teamDailyStatus = $resolver->isManager() ? $this->compliance->teamDailyStatus($user) : [];

        return view('marketing.period-reports.index', compact(
            'reports', 'teamMembers', 'stats', 'resolver', 'periodType',
            'myPending', 'teamDailyStatus'
        ));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'period_type' => 'required|in:daily,weekly,monthly',
            'anchor_date' => 'required|date|before_or_equal:today',
        ]);

        $user = Auth::user();
        $this->authorize('viewAny', MarketingPeriodReport::class);

        $allowed = $this->compliance->mandatoryTypesFor($user);
        if (!$user->hasRole(['super_admin', 'admin'])
            && !in_array($validated['period_type'], $allowed, true)) {
            abort(403, 'هذا النوع من التقارير غير مطلوب لدورك.');
        }

        $period = MarketingPeriodReport::resolvePeriod(
            $validated['period_type'],
            Carbon::parse($validated['anchor_date'])
        );

        $report = MarketingPeriodReport::firstOrNew([
            'user_id' => $user->id,
            'period_type' => $validated['period_type'],
            'period_start' => $period['start']->toDateString(),
        ]);

        if ($report->exists && $report->isSubmitted()) {
            return redirect()
                ->route('marketing.reports.show', $report)
                ->with('error', 'تم رفع هذا التقرير مسبقاً ولا يمكن تعديله.');
        }

        $report->period_end = $period['end']->toDateString();
        $report->status = MarketingPeriodReport::STATUS_DRAFT;
        $report->metrics = $this->metricsService->build(
            $user,
            $validated['period_type'],
            $period['start'],
            $period['end']
        );
        $report->save();

        return redirect()
            ->route('marketing.reports.show', $report)
            ->with('success', 'تم إنشاء التقرير وملء البيانات من النظام.');
    }

    public function show(MarketingPeriodReport $marketingPeriodReport)
    {
        $this->authorize('view', $marketingPeriodReport);
        $marketingPeriodReport->load('author:id,name');

        return view('marketing.period-reports.show', [
            'report' => $marketingPeriodReport,
            'canEdit' => Auth::user()->can('update', $marketingPeriodReport),
            'resolver' => MarketingRoleResolver::for(Auth::user()),
        ]);
    }

    public function update(Request $request, MarketingPeriodReport $marketingPeriodReport)
    {
        $this->authorize('update', $marketingPeriodReport);

        $validated = $request->validate([
            'activities_summary' => 'nullable|string|max:15000',
            'campaigns_progress' => 'nullable|string|max:15000',
            'obstacles' => 'nullable|string|max:10000',
            'support_required' => 'nullable|string|max:10000',
            'next_period_plan' => 'nullable|string|max:10000',
            'team_summary' => 'nullable|string|max:15000',
        ]);

        $marketingPeriodReport->update($validated);

        return back()->with('success', 'تم حفظ التعديلات.');
    }

    public function refresh(MarketingPeriodReport $marketingPeriodReport)
    {
        $this->authorize('update', $marketingPeriodReport);

        $author = User::findOrFail($marketingPeriodReport->user_id);
        $marketingPeriodReport->update([
            'metrics' => $this->metricsService->build(
                $author,
                $marketingPeriodReport->period_type,
                $marketingPeriodReport->period_start,
                $marketingPeriodReport->period_end
            ),
        ]);

        return back()->with('success', 'تم تحديث الأرقام من النظام.');
    }

    public function submit(Request $request, MarketingPeriodReport $marketingPeriodReport)
    {
        $this->authorize('submit', $marketingPeriodReport);

        $validated = $request->validate([
            'activities_summary' => 'nullable|string|max:15000',
            'campaigns_progress' => 'nullable|string|max:15000',
            'obstacles' => 'nullable|string|max:10000',
            'support_required' => 'nullable|string|max:10000',
            'next_period_plan' => 'nullable|string|max:10000',
            'team_summary' => 'nullable|string|max:10000',
        ]);

        $author = User::findOrFail($marketingPeriodReport->user_id);
        $marketingPeriodReport->fill($validated);
        $marketingPeriodReport->metrics = $this->metricsService->build(
            $author,
            $marketingPeriodReport->period_type,
            $marketingPeriodReport->period_start,
            $marketingPeriodReport->period_end
        );
        $marketingPeriodReport->status = MarketingPeriodReport::STATUS_SUBMITTED;
        $marketingPeriodReport->submitted_at = now();
        $marketingPeriodReport->save();

        return redirect()
            ->route('marketing.reports.index', ['period' => $marketingPeriodReport->period_type])
            ->with('success', 'تم رفع التقرير بنجاح.');
    }

    protected function applyFilters($query, Request $request, MarketingRoleResolver $resolver): void
    {
        if ($request->filled('user_id') && ($resolver->isAdmin() || $resolver->isManager())) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }

    protected function visibleReportsQuery(MarketingScopeService $scope, User $user, MarketingRoleResolver $resolver)
    {
        if ($resolver->isAdmin()) {
            return MarketingPeriodReport::query();
        }

        if ($resolver->isManager()) {
            return MarketingPeriodReport::query()->whereIn('user_id', $scope->teamUserIds());
        }

        return MarketingPeriodReport::query()->where('user_id', $user->id);
    }
}

<?php

namespace App\Services\Operations;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\DailySalesReport;
use App\Models\Employee;
use App\Models\OperationsPeriodReport;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\RealEstateDeveloper;
use App\Models\Sale;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\EmployeeComplianceService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OperationsKpiService
{
    public function __construct(
        protected EmployeeComplianceService $compliance,
    ) {}

    /** @return array{groups: array, flat: array<string, float>, period: array} */
    public function collect(?Carbon $start = null, ?Carbon $end = null, ?User $opsUser = null): array
    {
        $start ??= now()->startOfMonth();
        $end ??= now()->endOfDay();
        $opsUser ??= auth()->user();

        $raw = $this->computeRawMetrics($start, $end, $opsUser);
        $groups = $this->buildGroups($raw);
        $flat = $this->flattenForCompensation($raw);

        return [
            'groups' => $groups,
            'flat' => $flat,
            'raw' => $raw,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
        ];
    }

    /** @return array<string, float|int> */
    protected function computeRawMetrics(Carbon $start, Carbon $end, ?User $opsUser): array
    {
        $salesRepIds = $this->salesRepUserIds();
        $salesEmployeeIds = Employee::query()
            ->whereIn('user_id', $salesRepIds)
            ->pluck('id');

        $leadsInPeriod = Client::query()
            ->whereBetween('created_at', [$start, $end]);

        $totalLeads = (clone $leadsInPeriod)->count();

        $contactedLeads = (clone $leadsInPeriod)
            ->where(function ($q) {
                $q->whereNotIn('lead_stage', ['lead'])
                    ->orWhereHas('timelineEvents', fn ($t) => $t->whereIn('event_type', ['interaction', 'stage_changed', 'assigned']));
            })
            ->count();

        $contactRate = $totalLeads > 0 ? round(($contactedLeads / $totalLeads) * 100, 1) : 100;

        $uncontacted = (clone $leadsInPeriod)
            ->where('lead_stage', 'lead')
            ->whereDoesntHave('timelineEvents', fn ($t) => $t->where('event_type', 'interaction'))
            ->count();
        $leadLeakageRate = $totalLeads > 0 ? round(($uncontacted / $totalLeads) * 100, 1) : 0;

        $responseTimes = $this->leadResponseTimes($start, $end);
        $distributionTimes = $this->leadDistributionTimes($start, $end);

        $activeSalesReps = max(1, $salesRepIds->count());
        $reportsSubmitted = DailySalesReport::query()
            ->whereIn('user_id', $salesRepIds)
            ->where('status', 'submitted')
            ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
            ->distinct()
            ->count('user_id');
        $expectedReportDays = max(1, $start->diffInWeekdays($end) + 1);
        $crmCompliance = min(100, round(($reportsSubmitted / $activeSalesReps) * (100 / min(22, $expectedReportDays)), 1));

        $clientsBase = Client::query()->whereBetween('created_at', [$start, $end]);
        $withPhone = (clone $clientsBase)->whereNotNull('phone')->where('phone', '!=', '')->count();
        $withName = (clone $clientsBase)->whereNotNull('name')->where('name', '!=', '')->count();
        $dataAccuracy = $totalLeads > 0
            ? round((($withPhone + $withName) / ($totalLeads * 2)) * 100, 1)
            : 100;

        $duplicateRate = $this->duplicatePhoneRate($start, $end);

        $activeDeals = Sale::query()
            ->whereIn('assigned_to', $salesRepIds)
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->count();
        $recentlyUpdated = Sale::query()
            ->whereIn('assigned_to', $salesRepIds)
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        $pipelineUpdateRate = $activeDeals > 0 ? round(($recentlyUpdated / $activeDeals) * 100, 1) : 100;

        $meetings = CrmFollowUp::query()
            ->whereIn('user_id', $salesRepIds)
            ->whereIn('interaction_type', ['meeting', 'viewing'])
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count();
        $leadToMeeting = $totalLeads > 0 ? round(($meetings / $totalLeads) * 100, 1) : 0;

        $reservations = ProjectUnit::query()
            ->where('status', ProjectUnit::STATUS_RESERVED)
            ->whereBetween('updated_at', [$start, $end])
            ->count();
        $meetingToReservation = $meetings > 0 ? round(($reservations / $meetings) * 100, 1) : 0;

        $contracts = Sale::query()
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$start, $end])
            ->count();
        $reservationToContract = $reservations > 0 ? round(($contracts / $reservations) * 100, 1) : 0;

        $cycleDays = $this->averageSalesCycleDays($start, $end);

        $prevStart = $start->copy()->subMonth();
        $prevEnd = $end->copy()->subMonth();
        $currentRevenue = $this->closedRevenue($start, $end);
        $prevRevenue = $this->closedRevenue($prevStart, $prevEnd);
        $revenueGrowth = $prevRevenue > 0
            ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($currentRevenue > 0 ? 100 : 0);

        $recovered = Client::query()
            ->where('lead_stage', 'prospect')
            ->whereNotNull('lost_at')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $marketingLeads = Client::query()
            ->whereNotNull('marketing_campaign_id')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $marketingWon = Sale::query()
            ->where('stage', 'closed_won')
            ->whereHas('client', fn ($c) => $c->whereNotNull('marketing_campaign_id'))
            ->whereBetween('actual_close_date', [$start, $end])
            ->count();
        $marketingRoi = $marketingLeads > 0 ? round(($marketingWon / $marketingLeads) * 100, 1) : 0;

        $units = ProjectUnit::query();
        $totalUnits = (clone $units)->count();
        $accurateUnits = (clone $units)
            ->whereNotNull('area_m2')
            ->where('area_m2', '>', 0)
            ->where(fn ($q) => $q->where('price_cash', '>', 0)->orWhere('price_installment', '>', 0))
            ->count();
        $inventoryAccuracy = $totalUnits > 0 ? round(($accurateUnits / $totalUnits) * 100, 1) : 100;

        $statusUnits = (clone $units)->whereIn('status', [
            ProjectUnit::STATUS_AVAILABLE,
            ProjectUnit::STATUS_RESERVED,
            ProjectUnit::STATUS_SOLD,
        ])->count();
        $unitAvailabilityAccuracy = $totalUnits > 0 ? round(($statusUnits / $totalUnits) * 100, 1) : 100;

        $doubleBooking = 0;

        $scheduled = CrmFollowUp::query()
            ->whereIn('user_id', $salesRepIds)
            ->whereBetween('scheduled_at', [$start, $end])
            ->count();
        $completedFollowUps = CrmFollowUp::query()
            ->whereIn('user_id', $salesRepIds)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count();
        $followUpCompliance = $scheduled > 0 ? round(($completedFollowUps / $scheduled) * 100, 1) : 100;

        $activitiesPerRep = $salesRepIds->count() > 0
            ? round($completedFollowUps / $salesRepIds->count(), 1)
            : 0;
        $productivityScore = min(100, round($activitiesPerRep * 4, 1));

        $today = Carbon::today();
        $activeProjects = Project::query()
            ->whereIn('listing_status', ['active', 'available', 'under_construction'])
            ->count();
        $onTrack = Project::query()
            ->whereIn('listing_status', ['active', 'available', 'under_construction'])
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $today))
            ->count();
        $projectsOnTrackPct = $activeProjects > 0 ? round(($onTrack / $activeProjects) * 100, 1) : 100;

        $employeeIds = Employee::query()->where('status', 'active')->pluck('id');
        $expectedDays = max(1, $start->diffInWeekdays($end) + 1);
        $attendanceRecords = Attendance::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('check_in')
            ->count();
        $teamAttendancePct = $employeeIds->count() > 0
            ? min(100, round(($attendanceRecords / ($employeeIds->count() * $expectedDays)) * 100, 1))
            : 0;

        $opsReports = OperationsPeriodReport::query()
            ->where('user_id', $opsUser?->id)
            ->where('status', OperationsPeriodReport::STATUS_SUBMITTED)
            ->whereBetween('period_start', [$start->toDateString(), $end->toDateString()])
            ->count();

        $mandatoryTypes = count(config('operations_reports.mandatory_for_manager', ['daily', 'weekly', 'monthly']));
        $reportDelivery = $mandatoryTypes > 0
            ? min(100, round(($opsReports / $mandatoryTypes) * 100, 1))
            : 100;

        return [
            'lead_response_time' => $responseTimes['avg_minutes'],
            'lead_distribution_time' => $distributionTimes['avg_minutes'],
            'lead_leakage_rate' => $leadLeakageRate,
            'contact_rate' => $contactRate,
            'crm_compliance_rate' => $crmCompliance,
            'data_accuracy_rate' => $dataAccuracy,
            'duplicate_records_rate' => $duplicateRate,
            'pipeline_update_rate' => $pipelineUpdateRate,
            'lead_to_meeting_conversion' => $leadToMeeting,
            'meeting_to_reservation_conversion' => $meetingToReservation,
            'reservation_to_contract_conversion' => $reservationToContract,
            'sales_cycle_duration' => $cycleDays,
            'revenue_growth_support' => max(0, $revenueGrowth),
            'lost_opportunity_recovery' => $recovered,
            'cost_per_sale_reduction' => $contracts > 0 ? min(100, round(100 - ($contracts / max(1, $totalLeads)) * 10, 1)) : 0,
            'marketing_roi_improvement' => $marketingRoi,
            'inventory_accuracy' => $inventoryAccuracy,
            'unit_availability_accuracy' => $unitAvailabilityAccuracy,
            'double_booking_incidents' => $doubleBooking,
            'active_inventory_units' => ProjectUnit::where('status', ProjectUnit::STATUS_AVAILABLE)->count(),
            'sales_activity_compliance' => $crmCompliance,
            'follow_up_compliance' => $followUpCompliance,
            'employee_productivity_score' => $productivityScore,
            'training_completion_rate' => 100,
            'report_accuracy' => $reportDelivery,
            'report_delivery_time' => $reportDelivery,
            'dashboard_freshness' => 100,
            'reports_submitted' => $opsReports,
            'projects_on_track_pct' => $projectsOnTrackPct,
            'team_attendance_pct' => $teamAttendancePct,
            'unassigned_leads' => Client::whereNull('assigned_to')->whereIn('lead_stage', ['lead', 'prospect'])->count(),
            'stale_leads' => Client::query()
                ->whereIn('lead_stage', ['lead', 'prospect'])
                ->where('updated_at', '<', now()->subDays(3))
                ->count(),
        ];
    }

    protected function salesRepUserIds(): Collection
    {
        return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            ->whereHas('employee', fn ($q) => $q
                ->where('department_id', CrmEmployeeService::salesDepartment()->id)
                ->where('status', 'active'))
            ->pluck('id');
    }

    /** @return array{avg_minutes: float, count: int} */
    protected function leadResponseTimes(Carbon $start, Carbon $end): array
    {
        $clients = Client::query()
            ->whereBetween('created_at', [$start, $end])
            ->with(['timelineEvents' => fn ($q) => $q->where('event_type', 'interaction')->orderBy('occurred_at')])
            ->get();

        $minutes = [];
        foreach ($clients as $client) {
            $first = $client->timelineEvents->first();
            if ($first) {
                $minutes[] = $client->created_at->diffInMinutes($first->occurred_at);
            } elseif (!in_array($client->lead_stage, ['lead'], true)) {
                $minutes[] = $client->created_at->diffInMinutes($client->updated_at);
            }
        }

        return [
            'avg_minutes' => count($minutes) > 0 ? round(collect($minutes)->avg(), 1) : 0,
            'count' => count($minutes),
        ];
    }

    /** @return array{avg_minutes: float, count: int} */
    protected function leadDistributionTimes(Carbon $start, Carbon $end): array
    {
        $clients = Client::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('assigned_to')
            ->get();

        if ($clients->isEmpty()) {
            return ['avg_minutes' => 0, 'count' => 0];
        }

        $minutes = $clients->map(fn (Client $c) => $c->created_at->diffInMinutes($c->updated_at))->all();

        return [
            'avg_minutes' => round(collect($minutes)->avg(), 1),
            'count' => $clients->count(),
        ];
    }

    protected function duplicatePhoneRate(Carbon $start, Carbon $end): float
    {
        $phones = Client::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('phone')
            ->pluck('phone')
            ->map(fn ($p) => preg_replace('/\D+/', '', $p))
            ->filter();

        if ($phones->isEmpty()) {
            return 0;
        }

        $dupes = $phones->count() - $phones->unique()->count();

        return round(($dupes / $phones->count()) * 100, 2);
    }

    protected function averageSalesCycleDays(Carbon $start, Carbon $end): float
    {
        $sales = Sale::query()
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$start, $end])
            ->with('client:id,created_at')
            ->get();

        if ($sales->isEmpty()) {
            return 0;
        }

        $days = $sales->map(function (Sale $sale) {
            if (!$sale->client) {
                return null;
            }

            return $sale->client->created_at->diffInDays($sale->actual_close_date ?? $sale->updated_at);
        })->filter();

        return $days->isEmpty() ? 0 : round($days->avg(), 1);
    }

    protected function closedRevenue(Carbon $start, Carbon $end): float
    {
        return (float) Sale::query()
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$start, $end])
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
    }

    /** @return array<string, array> */
    protected function buildGroups(array $raw): array
    {
        $groups = [];

        foreach (config('operations_kpis.groups', []) as $key => $meta) {
            $items = [];
            foreach ($meta['kpis'] as $slug => $kpi) {
                $value = (float) ($raw[$slug] ?? 0);
                $target = (float) $kpi['target'];
                $direction = $kpi['direction'] ?? 'higher';
                $items[] = [
                    'slug' => $slug,
                    'label' => $kpi['label'],
                    'unit' => $kpi['unit'],
                    'value' => $value,
                    'target' => $target,
                    'direction' => $direction,
                    'achievement' => $this->achievement($value, $target, $direction),
                    'status' => $this->statusBand($value, $target, $direction),
                ];
            }
            $groups[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'] ?? 'chart',
                'items' => $items,
                'score' => count($items) > 0
                    ? round(collect($items)->avg('achievement'), 1)
                    : 0,
            ];
        }

        return $groups;
    }

    protected function achievement(float $value, float $target, string $direction): float
    {
        if ($target <= 0) {
            return $value <= 0 ? 100 : 0;
        }

        if ($direction === 'lower') {
            if ($value <= $target) {
                return 100;
            }

            return max(0, round(($target / $value) * 100, 1));
        }

        return min(150, round(($value / $target) * 100, 1));
    }

    protected function statusBand(float $value, float $target, string $direction): string
    {
        $achievement = $this->achievement($value, $target, $direction);

        return match (true) {
            $achievement >= 95 => 'excellent',
            $achievement >= 80 => 'good',
            $achievement >= 60 => 'warning',
            default => 'critical',
        };
    }

    /** @return array<string, float> */
    protected function flattenForCompensation(array $raw): array
    {
        $flat = [];
        foreach (config('operations_kpis.groups', []) as $meta) {
            foreach ($meta['kpis'] as $slug => $kpi) {
                $value = (float) ($raw[$slug] ?? 0);
                $flat[$slug] = $this->achievement($value, (float) $kpi['target'], $kpi['direction'] ?? 'higher');
            }
        }

        $flat['projects_on_track_pct'] = (float) ($raw['projects_on_track_pct'] ?? 0);
        $flat['team_attendance_pct'] = (float) ($raw['team_attendance_pct'] ?? 0);
        $flat['reports_submitted'] = (float) ($raw['reports_submitted'] ?? 0);
        $flat['operational_efficiency'] = round(collect($flat)->avg(), 1);

        return $flat;
    }
}

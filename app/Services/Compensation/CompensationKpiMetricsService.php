<?php

namespace App\Services\Compensation;

use App\Models\Client;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\CrmFollowUp;
use App\Models\DailySalesReport;
use App\Models\Sale;
use App\Models\User;
use App\Services\CrmScopeService;
use App\Services\EmployeeComplianceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompensationKpiMetricsService
{
    public function __construct(
        protected CrmScopeService $scope,
        protected EmployeeComplianceService $compliance,
    ) {}

    public static function for(User $user): self
    {
        return new self(
            CrmScopeService::for($user),
            app(EmployeeComplianceService::class),
        );
    }

    public function collect(User $user, CompPayrollPeriod $period, string $targetRole): array
    {
        $start = $period->starts_at->copy()->startOfDay();
        $end = $period->ends_at->copy()->endOfDay();
        $userId = $user->id;

        if ($targetRole === 'manager') {
            return $this->collectManagerMetrics($user, $start, $end);
        }

        return $this->collectRepMetrics($user, $start, $end);
    }

    protected function collectRepMetrics(User $user, Carbon $start, Carbon $end): array
    {
        $userId = $user->id;
        $salesQuery = fn () => Sale::query()->where('assigned_to', $userId)->whereBetween('updated_at', [$start, $end]);
        $closed = Sale::query()
            ->where('assigned_to', $userId)
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$start, $end]);

        $leadsContacted = Client::query()
            ->where('created_by', $userId)
            ->whereBetween('updated_at', [$start, $end])
            ->whereIn('lead_stage', ['prospect', 'proposal', 'negotiation', 'closed_won'])
            ->count();

        $followUpsCompleted = CrmFollowUp::query()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count();

        $propertyVisits = CrmFollowUp::query()
            ->where('user_id', $userId)
            ->where('interaction_type', 'viewing')
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count();

        $qualifiedLeads = Client::query()
            ->where('created_by', $userId)
            ->whereIn('lead_stage', ['prospect', 'proposal', 'negotiation', 'closed_won'])
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $totalDeals = $salesQuery()->count();
        $closedCount = (clone $closed)->count();
        $conversionRate = $totalDeals > 0 ? round(($closedCount / $totalDeals) * 100, 2) : 0;

        $revenue = (float) (clone $closed)
            ->selectRaw('COALESCE(SUM(COALESCE(actual_value, estimated_value)), 0) as t')
            ->value('t');

        $reportsSubmitted = DailySalesReport::query()
            ->where('user_id', $userId)
            ->where('status', 'submitted')
            ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $complianceSnapshot = $this->compliance->evaluate($user, $start, $end);
        $expectedWorkDays = max(1, $complianceSnapshot['period']['expected_work_days']);
        $crmCompliance = min(100, round(($reportsSubmitted / $expectedWorkDays) * 100, 2));
        $attendanceCompliance = $complianceSnapshot['attendance_compliance'];

        return [
            'leads_contacted' => $leadsContacted,
            'follow_ups_completed' => $followUpsCompleted,
            'property_visits' => $propertyVisits,
            'qualified_leads' => $qualifiedLeads,
            'conversion_rate' => $conversionRate,
            'closed_deals' => $closedCount,
            'revenue_generated' => $revenue,
            'crm_compliance' => $crmCompliance,
            'attendance_compliance' => $attendanceCompliance,
            'report_compliance' => $crmCompliance,
        ];
    }

    protected function collectManagerMetrics(User $manager, Carbon $start, Carbon $end): array
    {
        $scope = CrmScopeService::for($manager);
        $memberIds = collect($scope->managedTeamMemberUserIds())->filter(fn ($id) => (int) $id !== (int) $manager->id)->values();

        if ($memberIds->isEmpty()) {
            return [
                'team_revenue' => 0,
                'team_conversion_rate' => 0,
                'team_target_achievement' => 0,
                'lead_distribution' => 0,
                'follow_up_compliance' => 0,
                'team_productivity' => 0,
                'team_retention' => 100,
            ];
        }

        $closed = Sale::query()
            ->whereIn('assigned_to', $memberIds)
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$start, $end]);

        $teamRevenue = (float) (clone $closed)
            ->selectRaw('COALESCE(SUM(COALESCE(actual_value, estimated_value)), 0) as t')
            ->value('t');

        $totalDeals = Sale::query()->whereIn('assigned_to', $memberIds)->whereBetween('updated_at', [$start, $end])->count();
        $closedCount = (clone $closed)->count();
        $teamConversion = $totalDeals > 0 ? round(($closedCount / $totalDeals) * 100, 2) : 0;

        $teamTarget = (float) $memberIds->count() * 500000;
        $teamTargetAchievement = $teamTarget > 0 ? min(100, round(($teamRevenue / $teamTarget) * 100, 2)) : 0;

        $unassigned = Client::query()
            ->whereNull('assigned_to')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $assigned = Client::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('assigned_to')
            ->count();
        $leadDistribution = ($assigned + $unassigned) > 0
            ? round(($assigned / ($assigned + $unassigned)) * 100, 2)
            : 100;

        $scheduled = CrmFollowUp::query()
            ->whereIn('user_id', $memberIds)
            ->whereBetween('scheduled_at', [$start, $end])
            ->count();
        $completed = CrmFollowUp::query()
            ->whereIn('user_id', $memberIds)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count();
        $followUpCompliance = $scheduled > 0 ? round(($completed / $scheduled) * 100, 2) : 100;

        $productivity = round($memberIds->avg(fn ($id) => CrmFollowUp::query()
            ->where('user_id', $id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count()) ?? 0, 2);

        $activeMembers = User::query()
            ->whereIn('id', $memberIds)
            ->whereHas('employee', fn ($q) => $q->where('status', 'active'))
            ->count();
        $teamRetention = $memberIds->count() > 0
            ? round(($activeMembers / $memberIds->count()) * 100, 2)
            : 100;

        return [
            'team_revenue' => $teamRevenue,
            'team_conversion_rate' => $teamConversion,
            'team_target_achievement' => $teamTargetAchievement,
            'lead_distribution' => $leadDistribution,
            'follow_up_compliance' => $followUpCompliance,
            'team_productivity' => $productivity,
            'team_retention' => $teamRetention,
        ];
    }
}

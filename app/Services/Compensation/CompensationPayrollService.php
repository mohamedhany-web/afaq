<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\Compensation\CompPayrollLineItem;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\Compensation\CompPayrollRun;
use App\Models\User;
use App\Services\CrmScopeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CompensationPayrollService
{
    public function __construct(
        protected CompensationKpiScoringService $kpiScoring,
        protected CompensationCommissionService $commission,
    ) {}

    public function currentPeriod(): CompPayrollPeriod
    {
        $now = now();

        return CompPayrollPeriod::firstOrCreate(
            ['year' => $now->year, 'month' => $now->month],
            [
                'starts_at' => $now->copy()->startOfMonth(),
                'ends_at' => $now->copy()->endOfMonth(),
                'status' => 'open',
            ],
        );
    }

    public function periodForMonth(int $year, int $month): CompPayrollPeriod
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();

        return CompPayrollPeriod::firstOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'starts_at' => $start,
                'ends_at' => $start->copy()->endOfMonth(),
                'status' => 'open',
            ],
        );
    }

    public function calculateRun(User $user, ?CompPayrollPeriod $period = null): CompPayrollRun
    {
        $period ??= $this->currentPeriod();
        $profile = CompEmployeeProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['base_salary' => $user->employee?->salary ?? 0, 'is_active' => true],
        );

        $kpi = $this->kpiScoring->evaluateUser($user, $period);
        $commissionTotal = 0;
        $commissionLines = [];

        if ($profile->commissionPlan) {
            $result = $this->commission->calculate($user, $profile->commissionPlan, $period);
            $commissionTotal = $result['total'];
            $commissionLines = $result['lines'];
        }

        $bonuses = $this->sumAdjustments($user, $period, 'bonus');
        $deductions = $this->sumAdjustments($user, $period, 'deduction');

        $base = (float) $profile->base_salary;
        $net = $base + $commissionTotal + $bonuses - $deductions;

        $teamScore = null;
        if (in_array($profile->kpiTemplate?->target_role, ['manager', 'team_leader'], true)) {
            $teamScore = $this->averageTeamKpi($user, $period);
        }

        $run = CompPayrollRun::updateOrCreate(
            ['user_id' => $user->id, 'period_id' => $period->id],
            [
                'base_salary' => $base,
                'commission_total' => $commissionTotal,
                'bonus_total' => $bonuses,
                'deduction_total' => $deductions,
                'kpi_score' => $kpi['overall_score'],
                'kpi_level' => $kpi['level']['key'] ?? null,
                'team_score' => $teamScore,
                'net_pay' => max(0, round($net, 2)),
                'status' => 'draft',
                'calculated_at' => now(),
                'breakdown' => ['kpi' => $kpi],
            ],
        );

        $run->lineItems()->delete();
        foreach ($commissionLines as $line) {
            $run->lineItems()->create($line);
        }
        $this->syncAdjustmentLines($run, $user, $period);

        CompensationAuditService::log('payroll.calculated', CompPayrollRun::class, $run->id, null, [
            'net_pay' => $run->net_pay,
            'kpi_score' => $run->kpi_score,
        ]);

        return $run->fresh(['lineItems', 'period', 'user']);
    }

    protected function sumAdjustments(User $user, CompPayrollPeriod $period, string $type): float
    {
        return (float) CompAdjustment::query()
            ->where('user_id', $user->id)
            ->where('period_id', $period->id)
            ->where('type', $type)
            ->where('status', 'approved')
            ->sum('amount');
    }

    protected function syncAdjustmentLines(CompPayrollRun $run, User $user, CompPayrollPeriod $period): void
    {
        $adjustments = CompAdjustment::query()
            ->where('user_id', $user->id)
            ->where('period_id', $period->id)
            ->where('status', 'approved')
            ->get();

        foreach ($adjustments as $adj) {
            $run->lineItems()->create([
                'category' => $adj->type,
                'label' => $adj->reason,
                'amount' => $adj->type === 'deduction' ? -abs($adj->amount) : abs($adj->amount),
                'reference_type' => CompAdjustment::class,
                'reference_id' => $adj->id,
                'approval_status' => 'approved',
            ]);
        }
    }

    protected function averageTeamKpi(User $manager, CompPayrollPeriod $period): float
    {
        $scope = CrmScopeService::for($manager);
        $ids = collect($scope->managedTeamMemberUserIds())
            ->filter(fn ($id) => (int) $id !== (int) $manager->id);

        if ($ids->isEmpty()) {
            return 0;
        }

        $scores = $ids->map(function ($id) use ($period) {
            $user = User::find($id);
            if (!$user) {
                return 0;
            }

            return $this->kpiScoring->evaluateUser($user, $period)['overall_score'] ?? 0;
        });

        return round($scores->avg(), 2);
    }

    public function approveRun(CompPayrollRun $run, User $approver): CompPayrollRun
    {
        $old = $run->only(['status', 'approved_by']);
        $run->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
        CompensationAuditService::log('payroll.approved', CompPayrollRun::class, $run->id, $old, $run->only(['status']));

        return $run;
    }

    public function scopedUserIds(User $actor): Collection
    {
        $scope = CrmScopeService::for($actor);
        if ($scope->hasFullAccess()) {
            return CompEmployeeProfile::where('is_active', true)->pluck('user_id');
        }
        if ($scope->isManagerScope()) {
            return collect($scope->managedTeamMemberUserIds());
        }

        return collect([$actor->id]);
    }
}

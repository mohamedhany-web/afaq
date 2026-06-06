<?php

namespace App\Services;

use App\Models\AutoPenaltyLog;
use App\Models\AutoPenaltyRule;
use App\Models\Compensation\CompAdjustment;
use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\DailySalesReport;
use App\Models\MarketingActivity;
use App\Models\MarketingPeriodReport;
use App\Models\User;
use App\Services\Compensation\CompensationPayrollService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AutoPenaltyService
{
    public function __construct(
        protected CompensationPayrollService $payroll,
        protected MarketingReportComplianceService $marketingCompliance,
    ) {}

    /** @return array{applied: int, skipped: int, errors: int} */
    public function processAll(): array
    {
        $stats = ['applied' => 0, 'skipped' => 0, 'errors' => 0];

        AutoPenaltyRule::active()->orderBy('id')->each(function (AutoPenaltyRule $rule) use (&$stats) {
            foreach ($this->candidatesForRule($rule) as $candidate) {
                try {
                    if ($this->applyIfEligible($rule, $candidate)) {
                        $stats['applied']++;
                    } else {
                        $stats['skipped']++;
                    }
                } catch (\Throwable) {
                    $stats['errors']++;
                }
            }
        });

        return $stats;
    }

    /** @return array<string, array{overdue: int, pending_penalty: int, applied_today: int}> */
    public function departmentSummary(): array
    {
        $summary = [];

        foreach (config('auto_penalties.departments', []) as $code => $label) {
            $summary[$code] = [
                'label' => $label,
                'overdue' => $this->countOverdueForDepartment($code),
                'applied_today' => AutoPenaltyLog::query()
                    ->whereDate('applied_at', today())
                    ->whereHas('rule', fn ($q) => $q->where('department_code', $code))
                    ->count(),
                'applied_month' => AutoPenaltyLog::query()
                    ->whereMonth('applied_at', now()->month)
                    ->whereYear('applied_at', now()->year)
                    ->whereHas('rule', fn ($q) => $q->where('department_code', $code))
                    ->sum('amount'),
            ];
        }

        return $summary;
    }

    protected function countOverdueForDepartment(string $code): int
    {
        return match ($code) {
            'SAL' => CrmTask::overdue()->whereNotNull('assigned_to')->count()
                + CrmFollowUp::query()->where('status', CrmFollowUp::STATUS_SCHEDULED)->where('scheduled_at', '<', now())->whereNotNull('user_id')->count()
                + $this->missingDailySalesReportUsers()->count(),
            'MKT' => MarketingActivity::overdue()->whereNotNull('assigned_to')->count()
                + $this->missingMarketingReportUsers('daily')->count(),
            default => 0,
        };
    }

    /** @return Collection<int, array{user: User, source_type: string, source_key: string, title: string, due_at: Carbon, department_code: ?string}> */
    protected function candidatesForRule(AutoPenaltyRule $rule): Collection
    {
        return match ($rule->source_type) {
            'crm_task' => $this->crmTaskCandidates($rule),
            'crm_follow_up' => $this->crmFollowUpCandidates($rule),
            'daily_sales_report' => $this->dailySalesReportCandidates($rule),
            'marketing_activity' => $this->marketingActivityCandidates($rule),
            'marketing_report' => $this->marketingReportCandidates($rule),
            default => collect(),
        };
    }

    protected function crmTaskCandidates(AutoPenaltyRule $rule): Collection
    {
        return CrmTask::query()
            ->overdue()
            ->whereNotNull('assigned_to')
            ->with('assignee')
            ->limit(500)
            ->get()
            ->map(fn (CrmTask $task) => [
                'user' => $task->assignee,
                'source_type' => 'crm_task',
                'source_key' => 'crm_task:' . $task->id,
                'title' => $task->title,
                'due_at' => $task->due_at,
                'department_code' => 'SAL',
            ])
            ->filter(fn ($c) => $c['user'] instanceof User);
    }

    protected function crmFollowUpCandidates(AutoPenaltyRule $rule): Collection
    {
        return CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->where('scheduled_at', '<', now())
            ->whereNotNull('user_id')
            ->with('user', 'client')
            ->limit(500)
            ->get()
            ->map(fn (CrmFollowUp $fu) => [
                'user' => $fu->user,
                'source_type' => 'crm_follow_up',
                'source_key' => 'crm_follow_up:' . $fu->id,
                'title' => 'متابعة: ' . ($fu->client?->name ?? 'عميل'),
                'due_at' => $fu->scheduled_at,
                'department_code' => 'SAL',
            ])
            ->filter(fn ($c) => $c['user'] instanceof User);
    }

    protected function dailySalesReportCandidates(AutoPenaltyRule $rule): Collection
    {
        $reportDate = now()->subDay()->toDateString();
        $deadline = $this->dailyReportDeadline(now()->subDay());

        if (now()->lt($deadline->copy()->addHours($rule->grace_hours))) {
            return collect();
        }

        return $this->missingDailySalesReportUsers()->map(fn (User $user) => [
            'user' => $user,
            'source_type' => 'daily_sales_report',
            'source_key' => 'daily_sales_report:' . $user->id . ':' . $reportDate,
            'title' => 'تقرير مبيعات يومي ' . $reportDate,
            'due_at' => $deadline,
            'department_code' => 'SAL',
        ]);
    }

    protected function marketingActivityCandidates(AutoPenaltyRule $rule): Collection
    {
        return MarketingActivity::query()
            ->overdue()
            ->whereNotNull('assigned_to')
            ->with('assignee')
            ->limit(500)
            ->get()
            ->map(fn (MarketingActivity $activity) => [
                'user' => $activity->assignee,
                'source_type' => 'marketing_activity',
                'source_key' => 'marketing_activity:' . $activity->id,
                'title' => $activity->title,
                'due_at' => $activity->due_at,
                'department_code' => 'MKT',
            ])
            ->filter(fn ($c) => $c['user'] instanceof User);
    }

    protected function marketingReportCandidates(AutoPenaltyRule $rule): Collection
    {
        $periodType = $rule->report_period_type ?: MarketingPeriodReport::PERIOD_DAILY;
        $anchor = $this->marketingReportAnchor($periodType);
        $period = MarketingPeriodReport::resolvePeriod($periodType, $anchor);
        $deadline = $this->marketingReportDeadline($periodType, $period['end']);

        if (now()->lt($deadline->copy()->addHours($rule->grace_hours))) {
            return collect();
        }

        return $this->missingMarketingReportUsers($periodType, $anchor)->map(fn (User $user) => [
            'user' => $user,
            'source_type' => 'marketing_report',
            'source_key' => 'marketing_report:' . $user->id . ':' . $periodType . ':' . $period['start']->toDateString(),
            'title' => 'تقرير تسويق ' . config('marketing_reports.period_types.' . $periodType, $periodType),
            'due_at' => $deadline,
            'department_code' => 'MKT',
        ]);
    }

    /** @return Collection<int, User> */
    protected function missingDailySalesReportUsers(): Collection
    {
        $reportDate = now()->subDay()->toDateString();

        $eligible = User::query()
            ->where(function ($q) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['sales_rep', 'sales_manager', 'crm_admin']))
                    ->orWhereHas('employee.department', fn ($d) => $d->where('code', 'SAL'));
            })
            ->get(['id', 'name']);

        $submittedIds = DailySalesReport::query()
            ->whereDate('report_date', $reportDate)
            ->where('status', DailySalesReport::STATUS_SUBMITTED)
            ->pluck('user_id');

        return $eligible->reject(fn (User $u) => $submittedIds->contains($u->id));
    }

    /** @return Collection<int, User> */
    protected function missingMarketingReportUsers(string $periodType, ?Carbon $anchor = null): Collection
    {
        $anchor = $anchor ?? $this->marketingReportAnchor($periodType);
        $period = MarketingPeriodReport::resolvePeriod($periodType, $anchor);

        $eligible = User::query()
            ->where(function ($q) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['marketing_rep', 'marketing_manager']))
                    ->orWhereHas('employee.department', fn ($d) => $d->where('code', 'MKT'));
            })
            ->get(['id', 'name']);

        $submittedIds = MarketingPeriodReport::query()
            ->where('period_type', $periodType)
            ->whereDate('period_start', $period['start']->toDateString())
            ->where('status', MarketingPeriodReport::STATUS_SUBMITTED)
            ->pluck('user_id');

        return $eligible->filter(function (User $user) use ($periodType) {
            $mandatory = $this->marketingCompliance->mandatoryTypesFor($user);

            return in_array($periodType, $mandatory, true);
        })->reject(fn (User $u) => $submittedIds->contains($u->id));
    }

    protected function applyIfEligible(AutoPenaltyRule $rule, array $candidate): bool
    {
        /** @var User $user */
        $user = $candidate['user'];

        if (!$this->ruleMatchesDepartment($rule, $candidate['department_code'])) {
            return false;
        }

        if (!$this->ruleMatchesRole($rule, $user)) {
            return false;
        }

        $dueAt = $candidate['due_at'];
        if ($dueAt && now()->lt($dueAt->copy()->addHours($rule->grace_hours))) {
            return false;
        }

        if (AutoPenaltyLog::query()->where('rule_id', $rule->id)->where('source_key', $candidate['source_key'])->exists()) {
            return false;
        }

        return (bool) DB::transaction(function () use ($rule, $candidate, $user) {
            if (AutoPenaltyLog::query()->where('rule_id', $rule->id)->where('source_key', $candidate['source_key'])->lockForUpdate()->exists()) {
                return false;
            }

            $period = $this->payroll->currentPeriod();
            $reason = sprintf(
                'عقوبة تلقائية: %s — %s',
                $rule->name,
                $candidate['title'],
            );

            $systemUserId = $this->systemActorId();

            $adjustment = CompAdjustment::create([
                'type' => 'deduction',
                'user_id' => $user->id,
                'period_id' => $period->id,
                'rule_id' => null,
                'amount' => $rule->amount,
                'reason' => $reason,
                'status' => 'approved',
                'requested_by' => $systemUserId,
                'reviewed_by' => $systemUserId,
                'reviewed_at' => now(),
                'review_notes' => 'خصم تلقائي من نظام العقوبات',
            ]);

            AutoPenaltyLog::create([
                'rule_id' => $rule->id,
                'user_id' => $user->id,
                'source_type' => $candidate['source_type'],
                'source_key' => $candidate['source_key'],
                'amount' => $rule->amount,
                'reason' => $reason,
                'adjustment_id' => $adjustment->id,
                'period_id' => $period->id,
                'metadata' => [
                    'title' => $candidate['title'],
                    'department_code' => $candidate['department_code'],
                ],
                'applied_at' => now(),
            ]);

            return true;
        });
    }

    protected function ruleMatchesDepartment(AutoPenaltyRule $rule, ?string $departmentCode): bool
    {
        if (!$rule->department_code) {
            return true;
        }

        return $rule->department_code === $departmentCode;
    }

    protected function ruleMatchesRole(AutoPenaltyRule $rule, User $user): bool
    {
        $isManager = $user->isSalesManager()
            || $user->isMarketingManager()
            || $user->hasRole(['admin', 'super_admin', 'department_manager']);

        return match ($rule->applies_to) {
            'manager' => $isManager,
            'employee' => !$isManager,
            default => true,
        };
    }

    protected function dailyReportDeadline(Carbon $reportDate): Carbon
    {
        $hour = (int) config('auto_penalties.daily_report_deadline_hour', 18);

        return $reportDate->copy()->setTime($hour, 0);
    }

    protected function marketingReportAnchor(string $periodType): Carbon
    {
        return match ($periodType) {
            MarketingPeriodReport::PERIOD_WEEKLY => now()->subWeek(),
            MarketingPeriodReport::PERIOD_MONTHLY => now()->subMonth(),
            default => now()->subDay(),
        };
    }

    protected function marketingReportDeadline(string $periodType, Carbon $periodEnd): Carbon
    {
        $hour = (int) config('auto_penalties.daily_report_deadline_hour', 18);

        return match ($periodType) {
            MarketingPeriodReport::PERIOD_WEEKLY => $periodEnd->copy()->addDay()->setTime($hour, 0),
            MarketingPeriodReport::PERIOD_MONTHLY => $periodEnd->copy()->addDay()->setTime($hour, 0),
            default => $periodEnd->copy()->setTime($hour, 0),
        };
    }

    protected function systemActorId(): int
    {
        static $id = null;

        if ($id !== null) {
            return $id;
        }

        $id = (int) (User::role(['super_admin', 'admin'])->value('id') ?? User::query()->value('id') ?? 1);

        return $id;
    }
}

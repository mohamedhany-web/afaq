<?php

namespace App\Services;

use App\Models\AutoPenaltyLog;
use App\Models\AutoPenaltyRule;
use App\Models\Attendance;
use App\Models\AttendanceAbsenceReview;
use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompDeductionRule;
use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\DailySalesReport;
use App\Models\MarketingActivity;
use App\Models\MarketingPeriodReport;
use App\Models\User;
use App\Services\Compensation\CompensationKpiScoringService;
use App\Services\Compensation\CompensationPayrollService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AutoPenaltyService
{
    public function __construct(
        protected CompensationPayrollService $payroll,
        protected CompensationKpiScoringService $kpiScoring,
        protected MarketingReportComplianceService $marketingCompliance,
        protected EmployeeWorkCalendarService $workCalendar,
        protected EmployeeScheduleService $employeeSchedule,
        protected WorkDayService $workDay,
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

    /** تطبيق فوري لعقوبة تأخر الحضور بعد تسجيل check-in */
    public function tryApplyAttendanceLate(Attendance $attendance): bool
    {
        $attendance->loadMissing('employee.user', 'employee.department');
        $employee = $attendance->employee;
        $user = $employee?->user;

        if (!$user || !$employee) {
            return false;
        }

        $date = Carbon::parse($attendance->date);
        if ($this->workCalendar->shouldSkipCompliancePenalty($user, $date)) {
            return false;
        }

        $lateMinutes = (int) ($attendance->late_minutes ?? 0);
        if ($lateMinutes <= 0 && $attendance->status !== 'late') {
            return false;
        }

        $rule = AutoPenaltyRule::active()->where('source_type', 'attendance_late')->first();
        if (!$rule) {
            return false;
        }

        return $this->applyIfEligible($rule, [
            'user' => $user,
            'source_type' => 'attendance_late',
            'source_key' => 'attendance_late:' . $attendance->id,
            'title' => 'تأخر حضور ' . $date->toDateString() . ($lateMinutes > 0 ? " ({$lateMinutes} د)" : ''),
            'due_at' => $this->employeeSchedule->scheduledCheckInAt($employee, $date),
            'department_code' => $employee->department?->code,
            'late_minutes' => max($lateMinutes, $this->employeeSchedule->lateMinutes($employee, Carbon::parse($attendance->check_in), $date)),
        ]);
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
            'attendance_late' => $this->attendanceLateCandidates($rule),
            'attendance_no_start' => $this->attendanceNoStartCandidates($rule),
            'attendance_short_hours' => $this->attendanceShortHoursCandidates($rule),
            'kpi_monthly' => $this->kpiMonthlyCandidates($rule),
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
            ->filter(fn ($c) => $c['user'] instanceof User)
            ->filter(fn ($c) => !$this->workCalendar->shouldSkipCompliancePenalty($c['user'], $c['due_at'] ?? now()));
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
            ->filter(fn ($c) => $c['user'] instanceof User)
            ->filter(fn ($c) => !$this->workCalendar->shouldSkipCompliancePenalty($c['user'], $c['due_at'] ?? now()));
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
            ->filter(fn ($c) => $c['user'] instanceof User)
            ->filter(fn ($c) => !$this->workCalendar->shouldSkipCompliancePenalty($c['user'], $c['due_at'] ?? now()));
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

        $reportDay = Carbon::parse($reportDate);

        return $eligible
            ->reject(fn (User $u) => $submittedIds->contains($u->id))
            ->reject(fn (User $u) => $this->workCalendar->shouldSkipCompliancePenalty($u, $reportDay));
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

        $periodEnd = $period['end'];

        return $eligible->filter(function (User $user) use ($periodType) {
            $mandatory = $this->marketingCompliance->mandatoryTypesFor($user);

            return in_array($periodType, $mandatory, true);
        })
            ->reject(fn (User $u) => $submittedIds->contains($u->id))
            ->reject(fn (User $u) => $this->workCalendar->shouldSkipCompliancePenalty($u, $periodEnd));
    }

    protected function attendanceLateCandidates(AutoPenaltyRule $rule): Collection
    {
        $date = now()->subDay()->toDateString();

        return Attendance::query()
            ->with(['employee.user', 'employee.department'])
            ->whereDate('date', $date)
            ->whereNotNull('check_in')
            ->where(function ($q) {
                $q->where('status', 'late')
                    ->orWhere('late_minutes', '>', 0);
            })
            ->limit(200)
            ->get()
            ->map(function (Attendance $a) use ($date) {
                $user = $a->employee?->user;
                $employee = $a->employee;
                if (!$user || !$employee) {
                    return null;
                }

                $day = Carbon::parse($date);

                return [
                    'user' => $user,
                    'source_type' => 'attendance_late',
                    'source_key' => 'attendance_late:' . $a->id,
                    'title' => 'تأخر حضور ' . $date,
                    'due_at' => $this->employeeSchedule->scheduledCheckInAt($employee, $day),
                    'department_code' => $employee->department?->code,
                    'late_minutes' => (int) ($a->late_minutes ?? $this->employeeSchedule->lateMinutes($employee, Carbon::parse($a->check_in), $day)),
                ];
            })
            ->filter()
            ->filter(fn ($c) => !$this->workCalendar->shouldSkipCompliancePenalty($c['user'], Carbon::parse($date)))
            ->filter(fn ($c) => !$this->workDay->isExempt($c['user']));
    }

    protected function attendanceNoStartCandidates(AutoPenaltyRule $rule): Collection
    {
        $date = now()->subDay()->startOfDay();
        $candidates = collect();

        User::query()
            ->whereHas('employee', fn ($q) => $q->where('status', 'active'))
            ->with(['employee.department'])
            ->chunkById(100, function ($users) use ($date, $rule, &$candidates) {
                foreach ($users as $user) {
                    if ($this->workDay->isExempt($user) || !$this->workCalendar->isExpectedWorkDay($user, $date)) {
                        continue;
                    }

                    $employee = $user->employee;
                    $deadline = $this->employeeSchedule->scheduledCheckInAt($employee, $date)
                        ->copy()
                        ->addMinutes($this->employeeSchedule->lateGraceMinutes($employee))
                        ->addHours((int) config('auto_penalties.no_start_grace_hours_after_shift', 2));

                    if (now()->lt($deadline->copy()->addHours($rule->grace_hours))) {
                        continue;
                    }

                    $attendance = Attendance::query()
                        ->where('employee_id', $employee->id)
                        ->whereDate('date', $date->toDateString())
                        ->first();

                    if ($attendance?->check_in) {
                        continue;
                    }

                    if (!$this->absencePenaltyAllowed($employee->id, $date)) {
                        continue;
                    }

                    $candidates->push([
                        'user' => $user,
                        'source_type' => 'attendance_no_start',
                        'source_key' => 'attendance_no_start:' . $user->id . ':' . $date->toDateString(),
                        'title' => 'لم يبدأ يوم العمل / غياب ' . $date->toDateString(),
                        'due_at' => $deadline,
                        'department_code' => $employee->department?->code,
                    ]);
                }
            });

        return $candidates;
    }

    protected function attendanceShortHoursCandidates(AutoPenaltyRule $rule): Collection
    {
        $date = now()->subDay()->toDateString();

        return Attendance::query()
            ->with(['employee.user', 'employee.department'])
            ->whereDate('date', $date)
            ->whereNotNull('check_in')
            ->where(function ($q) {
                $q->whereNotNull('check_out')->orWhere('work_day_locked', true);
            })
            ->limit(200)
            ->get()
            ->filter(function (Attendance $a) {
                if (!$a->employee || !$a->total_hours) {
                    return false;
                }
                $required = (float) ($a->required_hours ?? $this->employeeSchedule->requiredDailyHours($a->employee));

                return (float) $a->total_hours < ($required - 0.25);
            })
            ->map(function (Attendance $a) use ($date) {
                $user = $a->employee?->user;
                $employee = $a->employee;
                if (!$user || !$employee) {
                    return null;
                }

                return [
                    'user' => $user,
                    'source_type' => 'attendance_short_hours',
                    'source_key' => 'attendance_short_hours:' . $a->id,
                    'title' => 'ساعات عمل ناقصة ' . $date,
                    'due_at' => $this->employeeSchedule->scheduledCheckOutAt($employee, Carbon::parse($date)),
                    'department_code' => $employee->department?->code,
                ];
            })
            ->filter()
            ->filter(fn ($c) => !$this->workCalendar->shouldSkipCompliancePenalty($c['user'], Carbon::parse($date)))
            ->filter(fn ($c) => !$this->workDay->isExempt($c['user']))
            ->filter(fn ($c) => $this->absencePenaltyAllowed($c['user']->employee->id, Carbon::parse($date)));
    }

    protected function absencePenaltyAllowed(int $employeeId, Carbon $date): bool
    {
        $review = AttendanceAbsenceReview::query()
            ->where('employee_id', $employeeId)
            ->whereDate('review_date', $date->toDateString())
            ->first();

        return $review && $review->isConfirmedAbsent();
    }

    protected function kpiMonthlyCandidates(AutoPenaltyRule $rule): Collection
    {
        if (now()->day > (int) config('auto_penalties.kpi_penalty_until_day', 7)) {
            return collect();
        }

        $prev = now()->subMonth();
        $period = $this->payroll->periodForMonth($prev->year, $prev->month);
        $threshold = (float) config('auto_penalties.kpi_penalty_threshold', 60);
        $deadline = $period->ends_at->copy()->addDay()->setTime(
            (int) config('auto_penalties.daily_report_deadline_hour', 18),
            0,
        );

        if (now()->lt($deadline->copy()->addHours($rule->grace_hours))) {
            return collect();
        }

        return User::query()
            ->whereHas('compensationProfile', fn ($q) => $q->whereNotNull('kpi_template_id'))
            ->with(['compensationProfile.kpiTemplate', 'employee.department'])
            ->get()
            ->map(function (User $user) use ($period, $threshold, $deadline) {
                $kpi = $this->kpiScoring->evaluateUser($user, $period);
                $score = (float) ($kpi['overall_score'] ?? 0);

                if ($score >= $threshold) {
                    return null;
                }

                return [
                    'user' => $user,
                    'source_type' => 'kpi_monthly',
                    'source_key' => 'kpi_monthly:' . $user->id . ':' . $period->id,
                    'title' => 'KPI ' . $period->starts_at->format('Y-m') . " ({$score}%)",
                    'due_at' => $deadline,
                    'department_code' => $user->employee?->department?->code,
                    'kpi_score' => $score,
                ];
            })
            ->filter()
            ->filter(fn ($c) => $this->ruleMatchesDepartment($rule, $c['department_code']));
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

            $amount = $this->resolvePenaltyAmount($rule, $candidate);

            $adjustment = CompAdjustment::create([
                'type' => 'deduction',
                'user_id' => $user->id,
                'period_id' => $period->id,
                'rule_id' => $this->deductionRuleIdFor($rule->source_type),
                'amount' => $amount,
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
                'amount' => $amount,
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

        // قواعد الحضور (HR) تنطبق على كل الموظفين النشطين
        if ($rule->department_code === 'HR') {
            return true;
        }

        if (!$departmentCode) {
            return false;
        }

        return $rule->department_code === $departmentCode;
    }

    protected function resolvePenaltyAmount(AutoPenaltyRule $rule, array $candidate): float
    {
        $amount = (float) $rule->amount;

        if ($rule->source_type === 'attendance_late') {
            $lateMinutes = (int) ($candidate['late_minutes'] ?? 0);
            $block = max(1, (int) config('auto_penalties.late_penalty_minutes_per_block', 30));

            return round($amount * max(1, (int) ceil($lateMinutes / $block)), 2);
        }

        if ($rule->source_type === 'kpi_monthly') {
            $score = (float) ($candidate['kpi_score'] ?? 0);
            $threshold = (float) config('auto_penalties.kpi_penalty_threshold', 60);
            if ($score < ($threshold / 2)) {
                return round($amount * 1.5, 2);
            }
        }

        return $amount;
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

    protected function deductionRuleIdFor(string $sourceType): ?int
    {
        $map = [
            'crm_task' => 'missed_followups',
            'crm_follow_up' => 'missed_followups',
            'daily_sales_report' => 'crm_incomplete',
            'attendance_late' => 'late_attendance',
            'attendance_no_start' => 'late_attendance',
            'attendance_short_hours' => 'late_attendance',
            'marketing_activity' => 'policy_violation',
            'marketing_report' => 'crm_incomplete',
            'kpi_monthly' => 'kpi_underperformance',
        ];

        $code = $map[$sourceType] ?? null;
        if (!$code) {
            return null;
        }

        return CompDeductionRule::query()->where('code', $code)->value('id');
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

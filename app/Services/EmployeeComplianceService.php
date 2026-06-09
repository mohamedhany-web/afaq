<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AutoPenaltyLog;
use App\Models\Compensation\CompAdjustment;
use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\DailySalesReport;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeComplianceService
{
    public function __construct(
        protected EmployeeWorkCalendarService $calendar,
        protected WorkDayService $workDay,
    ) {}

    /** @return array<string, mixed> */
    public function evaluate(User $user, ?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= now()->startOfMonth();
        $end ??= now()->endOfDay();

        $period = $this->calendar->periodSummary($user, $start, $end);
        $expected = max(1, $period['expected']);

        $reportsSubmitted = DailySalesReport::query()
            ->where('user_id', $user->id)
            ->where('status', DailySalesReport::STATUS_SUBMITTED)
            ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $reportCompliance = min(100, round(($reportsSubmitted / $expected) * 100, 1));

        $attendance = $this->attendanceBreakdown($user, $start, $end);
        $attendanceCompliance = $attendance['compliance_percent'];

        $overdueTasks = $this->isSalesUser($user)
            ? CrmTask::overdue()->where('assigned_to', $user->id)->count()
            : 0;

        $overdueFollowUps = $this->isSalesUser($user)
            ? CrmFollowUp::query()
                ->where('user_id', $user->id)
                ->where('status', CrmFollowUp::STATUS_SCHEDULED)
                ->where('scheduled_at', '<', now())
                ->count()
            : 0;

        $taskScore = $overdueTasks === 0 ? 100 : max(0, 100 - ($overdueTasks * 15));
        $followUpScore = $overdueFollowUps === 0 ? 100 : max(0, 100 - ($overdueFollowUps * 10));

        $penaltiesMonth = (float) AutoPenaltyLog::query()
            ->where('user_id', $user->id)
            ->whereBetween('applied_at', [$start, $end])
            ->sum('amount');

        $deductionsMonth = (float) CompAdjustment::query()
            ->where('user_id', $user->id)
            ->where('type', 'deduction')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $weights = config('employee_compliance.score_weights', []);
        $overall = round(
            ($reportCompliance * ($weights['reports'] ?? 35) / 100)
            + ($attendanceCompliance * ($weights['attendance'] ?? 30) / 100)
            + ($taskScore * ($weights['tasks'] ?? 20) / 100)
            + ($followUpScore * ($weights['follow_ups'] ?? 15) / 100),
            1,
        );

        return [
            'user_id' => $user->id,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'expected_work_days' => $period['expected'],
                'leave_days' => $period['leave_days'],
            ],
            'reports' => [
                'submitted' => $reportsSubmitted,
                'expected' => $expected,
                'percent' => $reportCompliance,
            ],
            'attendance' => $attendance,
            'attendance_compliance' => $attendanceCompliance,
            'crm_compliance' => $reportCompliance,
            'overdue_tasks' => $overdueTasks,
            'overdue_follow_ups' => $overdueFollowUps,
            'penalties_total' => $penaltiesMonth,
            'deductions_total' => $deductionsMonth,
            'overall_score' => $overall,
            'status' => $this->statusBand($overall),
            'flags' => $this->buildFlags($reportCompliance, $attendanceCompliance, $overdueTasks, $overdueFollowUps),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function teamOverview(Collection $users, ?Carbon $start = null, ?Carbon $end = null): Collection
    {
        return $users->map(fn (User $u) => array_merge(
            ['user' => $u, 'name' => $u->name],
            $this->evaluate($u, $start, $end),
        ))->sortBy('overall_score');
    }

    /** @return array<string, mixed> */
    protected function attendanceBreakdown(User $user, Carbon $start, Carbon $end): array
    {
        $employee = $user->employee;
        if (!$employee || $this->workDay->isExempt($user)) {
            return [
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'short_hours' => 0,
                'on_leave' => 0,
                'compliance_percent' => 100,
            ];
        }

        $expected = $this->calendar->expectedWorkDaysCount($user, $start, $end);
        if ($expected <= 0) {
            return [
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'short_hours' => 0,
                'on_leave' => $this->calendar->periodSummary($user, $start, $end)['leave_days'],
                'compliance_percent' => 100,
            ];
        }

        $records = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $present = $records->whereIn('status', ['present', 'late'])->whereNotNull('check_in')->count();
        $late = $records->where('status', 'late')->count();
        $shortHours = $records->filter(function (Attendance $a) use ($employee) {
            if (!$a->check_in || !$a->total_hours) {
                return false;
            }
            $required = (float) ($a->required_hours ?? $this->workDay->requiredDailyHours($employee));

            return (float) $a->total_hours < ($required - 0.25);
        })->count();

        $onLeave = $this->calendar->periodSummary($user, $start, $end)['leave_days'];
        $absent = max(0, $expected - $present);

        $compliance = min(100, round((($present - ($late * 0.5) - ($shortHours * 0.5)) / $expected) * 100, 1));

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'short_hours' => $shortHours,
            'on_leave' => $onLeave,
            'compliance_percent' => max(0, $compliance),
        ];
    }

    /** @return array{key:string, label:string, color:string} */
    protected function statusBand(float $score): array
    {
        $bands = collect(config('employee_compliance.status_labels', []))
            ->sortByDesc(fn ($band) => $band['min'] ?? 0);

        foreach ($bands as $key => $band) {
            if ($score >= ($band['min'] ?? 0)) {
                return ['key' => $key, 'label' => $band['label'], 'color' => $band['color']];
            }
        }

        return ['key' => 'critical', 'label' => 'غير ملتزم', 'color' => 'red'];
    }

    /** @return list<string> */
    protected function buildFlags(float $reports, float $attendance, int $tasks, int $followUps): array
    {
        $flags = [];
        if ($reports < 80) {
            $flags[] = 'تقارير يومية ناقصة';
        }
        if ($attendance < 80) {
            $flags[] = 'مشاكل حضور أو ساعات عمل';
        }
        if ($tasks > 0) {
            $flags[] = $tasks . ' مهمة CRM متأخرة';
        }
        if ($followUps > 0) {
            $flags[] = $followUps . ' متابعة فائتة';
        }

        return $flags;
    }

    protected function isSalesUser(User $user): bool
    {
        return $user->canAccessCrm() && !$user->usesMarketingWorkspace();
    }

    /** إجازات قادمة ومعتمدة للعرض */
    public function upcomingLeaves(User $user, int $limit = 5): Collection
    {
        $employee = $user->employee;
        if (!$employee) {
            return collect();
        }

        return Leave::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('end_date', '>=', today())
            ->orderBy('start_date')
            ->limit($limit)
            ->get();
    }
}

<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeWorkCalendarService
{
    public function __construct(
        protected WorkDayService $workDay,
        protected EmployeeScheduleService $schedule,
    ) {}

    public function isWorkDayFor(Employee $employee, Carbon $date): bool
    {
        return !$this->schedule->isWeeklyOffDay($employee, $date);
    }

    public function isOnApprovedLeave(Employee $employee, Carbon $date): bool
    {
        return $this->workDay->hasApprovedLeaveOnDate($employee, $date);
    }

    /** هل يُتوقع من الموظف العمل في هذا اليوم؟ */
    public function isExpectedWorkDay(User $user, Carbon $date): bool
    {
        if ($this->workDay->isExempt($user)) {
            return false;
        }

        $employee = $user->employee;
        if (!$employee || $employee->status !== 'active') {
            return false;
        }

        if (!$this->isWorkDayFor($employee, $date)) {
            return false;
        }

        if ($date->isFuture()) {
            return false;
        }

        return !$this->isOnApprovedLeave($employee, $date);
    }

    /** @return array{expected:int, leave_days:int, weekdays:int} */
    public function periodSummary(User $user, Carbon $start, Carbon $end): array
    {
        $expected = 0;
        $leaveDays = 0;
        $weekdays = 0;

        $employee = $user->employee;

        foreach (CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()) as $day) {
            if (!$employee || !$this->isWorkDayFor($employee, $day)) {
                continue;
            }
            $weekdays++;

            if ($this->isOnApprovedLeave($employee, $day)) {
                $leaveDays++;
                continue;
            }

            if ($this->isExpectedWorkDay($user, $day)) {
                $expected++;
            }
        }

        return [
            'expected' => max(0, $expected),
            'leave_days' => $leaveDays,
            'weekdays' => $weekdays,
        ];
    }

    public function expectedWorkDaysCount(User $user, Carbon $start, Carbon $end): int
    {
        return $this->periodSummary($user, $start, $end)['expected'];
    }

    public function shouldSkipCompliancePenalty(User $user, Carbon $date): bool
    {
        if ($this->workDay->isExempt($user)) {
            return true;
        }

        $employee = $user->employee;
        if (!$employee) {
            return true;
        }

        if ($this->isOnApprovedLeave($employee, $date)) {
            return true;
        }

        if ($this->schedule->isWeeklyOffDay($employee, $date)) {
            return true;
        }

        return false;
    }
}

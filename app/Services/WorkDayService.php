<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WorkDayService
{
    public function isExempt(User $user): bool
    {
        return $user->hasRole(config('work_day.exempt_roles', ['super_admin', 'admin']));
    }

    public function requiresWorkDayButton(User $user): bool
    {
        if ($this->isExempt($user)) {
            return false;
        }

        $employee = $user->employee;

        return $employee && $employee->status === 'active';
    }

    public function requiredDailyHours(Employee $employee): float
    {
        return (float) ($employee->daily_hours ?: config('work_day.default_daily_hours', 8));
    }

    public function hasApprovedLeaveOnDate(Employee $employee, Carbon $date): bool
    {
        $day = $date->toDateString();

        return Leave::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $day)
            ->whereDate('end_date', '>=', $day)
            ->exists();
    }

    public function todayAttendance(Employee $employee): ?Attendance
    {
        return Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', Carbon::today())
            ->first();
    }

    public function applyCheckInSchedule(Attendance $attendance, Employee $employee, ?Carbon $checkInAt = null): void
    {
        $checkInAt ??= Carbon::parse($attendance->check_in);
        $hours = $this->requiredDailyHours($employee);

        $attendance->update([
            'required_hours' => $hours,
            'scheduled_checkout_at' => $checkInAt->copy()->addMinutes((int) round($hours * 60)),
            'auto_checkout' => false,
            'work_day_locked' => false,
        ]);
    }

    public function performAutoCheckout(Attendance $attendance, Employee $employee, string $reason = 'auto'): bool
    {
        if (!$attendance->check_in || $attendance->check_out || $attendance->work_day_locked) {
            return false;
        }

        $checkOutTime = $attendance->scheduled_checkout_at
            ? Carbon::parse($attendance->scheduled_checkout_at)
            : Carbon::parse($attendance->check_in)->addMinutes((int) round($this->requiredDailyHours($employee) * 60));

        if ($checkOutTime->isFuture()) {
            return false;
        }

        $checkInTime = Carbon::parse($attendance->check_in);
        $totalMinutes = max(0, $checkOutTime->diffInMinutes($checkInTime));

        if ($attendance->break_duration_minutes) {
            $totalMinutes -= (int) $attendance->break_duration_minutes;
        }

        $note = $reason === 'auto'
            ? 'إيقاف تلقائي عند اكتمال ساعات العمل اليومية'
            : 'إنهاء يوم العمل';

        $attendance->update([
            'check_out' => $checkOutTime,
            'total_hours' => round($totalMinutes / 60, 2),
            'current_status' => 'completed',
            'status' => 'present',
            'auto_checkout' => $reason === 'auto',
            'work_day_locked' => true,
            'notes' => trim(($attendance->notes ? $attendance->notes . ' | ' : '') . $note),
        ]);

        return true;
    }

    /** @return int عدد الجلسات التي أُغلقت تلقائياً */
    public function processExpiredSessions(): int
    {
        $count = 0;
        $now = Carbon::now();

        Attendance::query()
            ->with('employee')
            ->whereDate('date', Carbon::today())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->where('work_day_locked', false)
            ->whereNotNull('scheduled_checkout_at')
            ->where('scheduled_checkout_at', '<=', $now)
            ->chunkById(50, function ($rows) use (&$count) {
                foreach ($rows as $attendance) {
                    if (!$attendance->employee) {
                        continue;
                    }
                    if ($this->performAutoCheckout($attendance, $attendance->employee)) {
                        $count++;
                    }
                }
            });

        return $count;
    }

    /** @return array<string, mixed> */
    public function statusFor(User $user, ?Carbon $date = null): array
    {
        $date ??= Carbon::today();
        $employee = $user->employee;

        if (!$this->requiresWorkDayButton($user)) {
            return [
                'show_button' => false,
                'required' => false,
                'on_leave' => false,
                'must_start' => false,
                'daily_hours' => 0,
            ];
        }

        $onLeave = $this->hasApprovedLeaveOnDate($employee, $date);
        $dailyHours = $this->requiredDailyHours($employee);
        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->first();

        $isWorking = $attendance
            && $attendance->check_in
            && !$attendance->check_out
            && !$attendance->work_day_locked;

        $isCompleted = $attendance && ($attendance->check_out || $attendance->work_day_locked);

        $mustStart = !$onLeave && !$isWorking && !$isCompleted;

        return [
            'show_button' => true,
            'required' => true,
            'on_leave' => $onLeave,
            'leave_label' => $onLeave ? 'إجازة معتمدة' : null,
            'must_start' => $mustStart,
            'is_working' => $isWorking,
            'is_completed' => (bool) $isCompleted,
            'work_day_locked' => (bool) ($attendance?->work_day_locked),
            'daily_hours' => $dailyHours,
            'check_in_time' => $attendance?->check_in?->format('H:i'),
            'check_out_time' => $attendance?->check_out?->format('H:i'),
            'scheduled_checkout_at' => $attendance?->scheduled_checkout_at?->toIso8601String(),
            'auto_checkout' => (bool) ($attendance?->auto_checkout),
            'total_hours' => $attendance?->total_hours,
        ];
    }

    /** @return array<string, mixed> */
    public function workDayMetricsForReport(User $user, Carbon $date): array
    {
        $employee = $user->employee;

        if (!$employee) {
            return ['tracked' => false];
        }

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->first();

        $onLeave = $this->hasApprovedLeaveOnDate($employee, $date);
        $required = $this->requiredDailyHours($employee);

        return [
            'tracked' => true,
            'on_leave' => $onLeave,
            'required_hours' => $required,
            'day_started' => (bool) ($attendance?->check_in),
            'check_in' => $attendance?->check_in?->format('H:i'),
            'check_out' => $attendance?->check_out?->format('H:i'),
            'scheduled_end' => $attendance?->scheduled_checkout_at?->format('H:i'),
            'total_hours' => $attendance?->total_hours,
            'auto_checkout' => (bool) ($attendance?->auto_checkout),
            'day_completed' => (bool) ($attendance?->check_out || $attendance?->work_day_locked),
            'met_required_hours' => $attendance && $attendance->total_hours
                ? (float) $attendance->total_hours >= ($attendance->required_hours ?? $required) - 0.1
                : false,
        ];
    }

    public function enrichWorkTimePayload(array $payload, User $user, ?Attendance $attendance): array
    {
        $status = $this->statusFor($user);
        $payload['work_day'] = $status;
        $payload['required_daily_hours'] = $status['daily_hours'] ?? 0;

        if ($attendance && $attendance->scheduled_checkout_at) {
            $payload['scheduled_checkout_at'] = $attendance->scheduled_checkout_at->toIso8601String();
            $payload['should_auto_checkout'] = !$attendance->check_out
                && !$attendance->work_day_locked
                && $attendance->scheduled_checkout_at->lte(now());
        } else {
            $payload['scheduled_checkout_at'] = null;
            $payload['should_auto_checkout'] = false;
        }

        return $payload;
    }
}

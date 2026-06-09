<?php

namespace App\Services;

use App\Models\Employee;
use Carbon\Carbon;

class EmployeeScheduleService
{
    /** @return list<int> */
    public function weeklyOffDays(Employee $employee): array
    {
        $days = $employee->weekly_off_days;

        if (is_array($days) && count($days) > 0) {
            return array_values(array_unique(array_map('intval', $days)));
        }

        return config('employee_schedule.default_weekly_off_days', [5, 6]);
    }

    public function isWeeklyOffDay(Employee $employee, Carbon $date): bool
    {
        return in_array($date->dayOfWeek, $this->weeklyOffDays($employee), true);
    }

    public function workStartTime(Employee $employee): string
    {
        return $this->normalizeTime($employee->work_start_time)
            ?? config('employee_schedule.default_work_start', '09:00');
    }

    public function workEndTime(Employee $employee): string
    {
        return $this->normalizeTime($employee->work_end_time)
            ?? config('employee_schedule.default_work_end', '17:00');
    }

    public function lateGraceMinutes(Employee $employee): int
    {
        return max(0, (int) ($employee->late_grace_minutes
            ?? config('employee_schedule.default_late_grace_minutes', 15)));
    }

    public function scheduledCheckInAt(Employee $employee, Carbon $date): Carbon
    {
        [$h, $m] = $this->timeParts($this->workStartTime($employee));

        return $date->copy()->startOfDay()->setTime($h, $m);
    }

    public function scheduledCheckOutAt(Employee $employee, Carbon $date): Carbon
    {
        [$h, $m] = $this->timeParts($this->workEndTime($employee));

        return $date->copy()->startOfDay()->setTime($h, $m);
    }

    public function requiredDailyHours(Employee $employee): float
    {
        $start = $this->scheduledCheckInAt($employee, Carbon::today());
        $end = $this->scheduledCheckOutAt($employee, Carbon::today());
        $fromSchedule = max(0, $end->diffInMinutes($start) / 60);

        if ($fromSchedule > 0) {
            return round($fromSchedule, 1);
        }

        return (float) ($employee->daily_hours ?: config('work_day.default_daily_hours', 8));
    }

    public function lateMinutes(Employee $employee, Carbon $checkIn, ?Carbon $date = null): int
    {
        $date ??= $checkIn->copy()->startOfDay();
        $scheduled = $this->scheduledCheckInAt($employee, $date);
        $grace = $this->lateGraceMinutes($employee);

        if ($checkIn->lte($scheduled->copy()->addMinutes($grace))) {
            return 0;
        }

        return (int) $scheduled->diffInMinutes($checkIn);
    }

    public function isLate(Employee $employee, Carbon $checkIn, ?Carbon $date = null): bool
    {
        return $this->lateMinutes($employee, $checkIn, $date) > 0;
    }

    public function isEarlyDeparture(Employee $employee, Carbon $checkOut, ?Carbon $date = null): bool
    {
        $date ??= $checkOut->copy()->startOfDay();
        $scheduled = $this->scheduledCheckOutAt($employee, $date);

        return $checkOut->lt($scheduled);
    }

    public function scheduleLabel(Employee $employee): string
    {
        return $this->workStartTime($employee) . ' — ' . $this->workEndTime($employee);
    }

    public function offDaysLabel(Employee $employee): string
    {
        $labels = config('employee_schedule.weekdays', []);
        $names = collect($this->weeklyOffDays($employee))
            ->map(fn (int $d) => $labels[$d] ?? (string) $d)
            ->all();

        return $names ? implode('، ', $names) : '—';
    }

    /** @return array<string, mixed> */
    public function scheduleAttributesFromRequest(array $input): array
    {
        $offDays = $input['weekly_off_days'] ?? [];
        if (!is_array($offDays)) {
            $offDays = [];
        }

        $start = $this->normalizeTime($input['work_start_time'] ?? null)
            ?? config('employee_schedule.default_work_start', '09:00');
        $end = $this->normalizeTime($input['work_end_time'] ?? null)
            ?? config('employee_schedule.default_work_end', '17:00');

        return [
            'work_start_time' => $start . ':00',
            'work_end_time' => $end . ':00',
            'weekly_off_days' => array_values(array_unique(array_map('intval', $offDays))),
            'late_grace_minutes' => max(0, min(60, (int) ($input['late_grace_minutes'] ?? 15))),
        ];
    }

    protected function normalizeTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('H:i');
        }

        $str = (string) $value;
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $str, $m)) {
            return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }

        return null;
    }

    /** @return array{0:int,1:int} */
    protected function timeParts(string $time): array
    {
        $normalized = $this->normalizeTime($time) ?? '09:00';
        [$h, $m] = explode(':', $normalized);

        return [(int) $h, (int) $m];
    }
}

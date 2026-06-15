<?php

namespace App\Services\Hr;

use App\Models\Attendance;
use App\Models\AttendanceAbsenceReview;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ExitPermit;
use App\Models\Leave;
use App\Services\EmployeeWorkCalendarService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HrMonthlyAttendanceReportService
{
    public function __construct(
        protected EmployeeWorkCalendarService $calendar,
    ) {}

    public function build(
        Carbon $month,
        ?int $departmentId = null,
        ?int $employeeId = null,
    ): array {
        $start = $month->copy()->startOfMonth()->startOfDay();
        $end = $month->copy()->endOfMonth()->startOfDay();
        $today = Carbon::today();

        if ($end->gt($today)) {
            $end = $today;
        }

        $employeesQuery = Employee::query()
            ->where('status', 'active')
            ->with(['department', 'user'])
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        if ($employeeId) {
            $employeesQuery->where('id', $employeeId);
        }

        $employees = $employeesQuery->get();
        $employeeIds = $employees->pluck('id');

        $attendances = Attendance::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('employee_id');

        $absences = AttendanceAbsenceReview::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('review_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', [
                AttendanceAbsenceReview::STATUS_CONFIRMED_ABSENT,
                AttendanceAbsenceReview::STATUS_AUTO_CONFIRMED,
            ])
            ->get()
            ->groupBy('employee_id');

        $leaves = Leave::query()
            ->approved()
            ->whereIn('employee_id', $employeeIds)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->get();

        $permits = ExitPermit::query()
            ->approved()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('permit_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('employee_id');

        $rows = $employees->map(function (Employee $employee) use (
            $start,
            $end,
            $attendances,
            $absences,
            $leaves,
            $permits,
        ) {
            $records = $attendances->get($employee->id, collect());
            $absenceRecords = $absences->get($employee->id, collect());
            $permitRecords = $permits->get($employee->id, collect());

            $expectedDays = 0;
            $user = $employee->user;

            if ($user) {
                foreach (CarbonPeriod::create($start, $end) as $day) {
                    if ($this->calendar->isExpectedWorkDay($user, $day)) {
                        $expectedDays++;
                    }
                }
            }

            $leaveDays = $this->countLeaveDaysInPeriod($leaves->where('employee_id', $employee->id), $start, $end);

            return [
                'employee' => $employee,
                'expected_days' => $expectedDays,
                'present_days' => $records->whereIn('status', ['present'])->count(),
                'late_days' => $records->where('status', 'late')->count(),
                'absent_days' => max(
                    $records->where('status', 'absent')->count(),
                    $absenceRecords->count()
                ),
                'leave_days' => $leaveDays,
                'permit_count' => $permitRecords->count(),
                'total_hours' => round((float) $records->sum('total_hours'), 1),
                'attendance_rate' => $expectedDays > 0
                    ? round((($records->whereIn('status', ['present', 'late'])->count()) / $expectedDays) * 100, 1)
                    : 0,
            ];
        });

        $summary = [
            'employees_count' => $rows->count(),
            'total_present' => $rows->sum('present_days'),
            'total_late' => $rows->sum('late_days'),
            'total_absent' => $rows->sum('absent_days'),
            'total_leave_days' => $rows->sum('leave_days'),
            'total_permits' => $rows->sum('permit_count'),
            'total_hours' => round($rows->sum('total_hours'), 1),
            'avg_attendance_rate' => $rows->count() > 0
                ? round($rows->avg('attendance_rate'), 1)
                : 0,
        ];

        return [
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'rows' => $rows,
            'summary' => $summary,
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    protected function countLeaveDaysInPeriod($leaves, Carbon $start, Carbon $end): int
    {
        $days = 0;

        foreach ($leaves as $leave) {
            $leaveStart = $leave->start_date->lt($start) ? $start->copy() : $leave->start_date->copy();
            $leaveEnd = $leave->end_date->gt($end) ? $end->copy() : $leave->end_date->copy();

            if ($leaveStart->lte($leaveEnd)) {
                $days += $leaveStart->diffInDays($leaveEnd) + 1;
            }
        }

        return $days;
    }
}

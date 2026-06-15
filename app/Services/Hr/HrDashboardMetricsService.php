<?php

namespace App\Services\Hr;

use App\Models\Attendance;
use App\Models\AttendanceAbsenceReview;
use App\Models\CustodyAssignment;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeDocument;
use App\Models\ExitPermit;
use App\Models\Leave;
use Carbon\Carbon;

class HrDashboardMetricsService
{
    public function snapshot(): array
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();

        $attendanceToday = Attendance::whereDate('date', $today);
        $presentToday = (clone $attendanceToday)->whereIn('status', ['present', 'late'])->count();
        $absentToday = (clone $attendanceToday)->where('status', 'absent')->count();
        $checkedInToday = (clone $attendanceToday)->whereNotNull('check_in')->count();

        return [
            'active_employees' => Employee::where('status', 'active')->count(),
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'checked_in_today' => $checkedInToday,
            'pending_leaves' => Leave::where('status', 'pending')->count(),
            'pending_permits' => ExitPermit::pending()->count(),
            'pending_absences' => AttendanceAbsenceReview::where('status', 'pending')
                ->whereDate('review_date', '>=', $today->copy()->subDays(7))
                ->count(),
            'leaves_this_month' => Leave::approved()
                ->where('start_date', '>=', $monthStart)
                ->where('start_date', '<=', $today)
                ->count(),
            'permits_this_month' => ExitPermit::approved()
                ->where('permit_date', '>=', $monthStart)
                ->where('permit_date', '<=', $today)
                ->count(),
            'active_contracts' => EmployeeContract::active()->count(),
            'expiring_contracts' => EmployeeContract::active()
                ->whereNotNull('end_date')
                ->whereBetween('end_date', [$today, $today->copy()->addDays(30)])
                ->count(),
            'active_custody' => CustodyAssignment::active()->count(),
            'employee_documents' => EmployeeDocument::count(),
        ];
    }
}

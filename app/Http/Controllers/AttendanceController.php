<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use App\Services\AttendanceCheckoutReviewService;
use App\Services\AttendanceScopeService;
use App\Services\AutoPenaltyService;
use App\Services\EmployeeScheduleService;
use App\Services\WorkDayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct(
        protected WorkDayService $workDay,
        protected EmployeeScheduleService $schedule,
        protected AutoPenaltyService $autoPenalties,
        protected AttendanceCheckoutReviewService $checkoutReviews,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $scope = AttendanceScopeService::for($currentUser);
        $employee = $scope->employee();
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()->toDateString()))->startOfDay();
        $isToday = $selectedDate->isToday();

        $employeesQuery = $scope->visibleEmployeesQuery()
            ->when($request->department_id, fn ($q) => $q->where('department_id', $request->department_id))
            ->when($request->employee_id, fn ($q) => $q->where('id', $request->employee_id))
            ->orderBy('first_name')
            ->orderBy('last_name');

        $visibleEmployees = $employeesQuery->get();
        $employeeIds = $visibleEmployees->pluck('id');

        $attendancesByEmployee = Attendance::query()
            ->whereDate('date', $selectedDate)
            ->whereIn('employee_id', $employeeIds)
            ->get()
            ->keyBy('employee_id');

        $roster = $visibleEmployees->map(function (Employee $emp) use ($attendancesByEmployee, $selectedDate) {
            $attendance = $attendancesByEmployee->get($emp->id);
            $row = $this->buildRosterRow($emp, $attendance, $selectedDate);

            return $row;
        });

        if ($request->filled('status')) {
            $roster = $roster->filter(fn ($row) => $row['filter_key'] === $request->status)->values();
        }

        $stats = $this->buildRosterStats($visibleEmployees, $attendancesByEmployee, $selectedDate);

        $todayAttendance = null;
        if ($employee && $isToday) {
            $todayAttendance = $attendancesByEmployee->get($employee->id);
        }

        $personalHistory = collect();
        if ($employee) {
            $personalHistory = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', '<=', $selectedDate)
                ->orderByDesc('date')
                ->limit(14)
                ->get();
        }

        return view('attendances.index', [
            'employee' => $employee,
            'canClockIn' => $this->workDay->requiresWorkDayButton($currentUser),
            'todayAttendance' => $todayAttendance,
            'selectedDate' => $selectedDate,
            'isToday' => $isToday,
            'stats' => $stats,
            'roster' => $roster,
            'personalHistory' => $personalHistory,
            'departments' => $scope->departmentsForFilter(),
            'employeesList' => $visibleEmployees,
            'canViewRoster' => $scope->canViewRoster(),
            'canViewAll' => $scope->canViewAllEmployees(),
            'scopeMode' => $scope->mode(),
        ]);
    }

    /** @return array<string, mixed> */
    protected function buildRosterRow(Employee $employee, ?Attendance $attendance, Carbon $date): array
    {
        $isOffDay = $this->schedule->isWeeklyOffDay($employee, $date);
        $onLeave = $this->workDay->hasApprovedLeaveOnDate($employee, $date);
        $scheduledIn = $this->schedule->workStartTime($employee);
        $scheduledOut = $this->schedule->workEndTime($employee);

        $filterKey = 'absent';
        $statusLabel = 'غائب';
        $statusColor = 'red';

        if ($onLeave) {
            $filterKey = 'on_leave';
            $statusLabel = 'في إجازة';
            $statusColor = 'blue';
        } elseif ($isOffDay) {
            $filterKey = 'off_day';
            $statusLabel = 'إجازة أسبوعية';
            $statusColor = 'gray';
        } elseif ($attendance?->check_in) {
            if (!$attendance->check_out) {
                if ($attendance->current_status === 'checkout_pending') {
                    $filterKey = 'checkout_pending';
                    $statusLabel = 'انصراف بانتظار العمليات';
                    $statusColor = 'amber';
                } elseif ($attendance->current_status === 'on_break') {
                    $filterKey = 'on_break';
                    $statusLabel = 'في استراحة';
                    $statusColor = 'amber';
                } else {
                    $filterKey = 'working';
                    $statusLabel = 'يعمل الآن';
                    $statusColor = 'green';
                }
            } elseif ($attendance->status === 'late' || ($attendance->late_minutes ?? 0) > 0) {
                $filterKey = 'late';
                $statusLabel = 'متأخر';
                $statusColor = 'orange';
            } elseif ($attendance->status === 'half_day') {
                $filterKey = 'half_day';
                $statusLabel = 'ناقص';
                $statusColor = 'red';
            } else {
                $filterKey = 'present';
                $statusLabel = 'مكتمل';
                $statusColor = 'green';
            }
        }

        $isLate = $attendance?->check_in
            ? $this->schedule->isLate($employee, Carbon::parse($attendance->check_in), $date)
            : false;
        $lateMinutes = $attendance?->check_in
            ? ($attendance->late_minutes ?? $this->schedule->lateMinutes($employee, Carbon::parse($attendance->check_in), $date))
            : 0;
        $isEarly = $attendance?->check_out
            ? $this->schedule->isEarlyDeparture($employee, Carbon::parse($attendance->check_out), $date)
            : false;

        return [
            'employee' => $employee,
            'attendance' => $attendance,
            'filter_key' => $filterKey,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'scheduled_in' => $scheduledIn,
            'scheduled_out' => $scheduledOut,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'is_early' => $isEarly,
            'on_leave' => $onLeave,
            'is_off_day' => $isOffDay,
        ];
    }

    /** @return array<string, int|float> */
    protected function buildRosterStats($employees, $attendancesByEmployee, Carbon $date): array
    {
        $total = $employees->count();
        $present = 0;
        $late = 0;
        $absent = 0;
        $working = 0;
        $checkoutPending = 0;
        $onBreak = 0;
        $onLeave = 0;
        $offDay = 0;
        $completed = 0;
        $totalHours = 0;
        $hoursCount = 0;

        foreach ($employees as $emp) {
            $row = $this->buildRosterRow($emp, $attendancesByEmployee->get($emp->id), $date);
            $key = $row['filter_key'];

            if ($key === 'on_leave') {
                $onLeave++;
            } elseif ($key === 'off_day') {
                $offDay++;
            } elseif ($key === 'working') {
                $working++;
                $present++;
            } elseif ($key === 'on_break') {
                $onBreak++;
                $present++;
            } elseif ($key === 'checkout_pending') {
                $checkoutPending++;
                $present++;
            } elseif ($key === 'absent') {
                $absent++;
            } else {
                $present++;
                $completed++;
            }

            if ($key === 'late' || ($row['is_late'] && $row['attendance']?->check_in)) {
                $late++;
            }

            $att = $row['attendance'];
            if ($att?->total_hours) {
                $totalHours += (float) $att->total_hours;
                $hoursCount++;
            }
        }

        $expected = max(0, $total - $offDay - $onLeave);
        $attendanceRate = $expected > 0 ? round(($present / $expected) * 100, 1) : 0;

        return [
            'total_employees' => $total,
            'present_today' => $present,
            'absent_today' => $absent,
            'late_today' => $late,
            'working_now' => $working,
            'on_break' => $onBreak,
            'on_leave' => $onLeave,
            'off_day' => $offDay,
            'completed' => $completed,
            'early_departures' => $employees->filter(function ($emp) use ($attendancesByEmployee, $date) {
                $att = $attendancesByEmployee->get($emp->id);
                return $att?->check_out && $this->schedule->isEarlyDeparture($emp, Carbon::parse($att->check_out), $date);
            })->count(),
            'average_hours' => $hoursCount > 0 ? round($totalHours / $hoursCount, 1) : 0,
            'attendance_rate' => $attendanceRate,
        ];
    }
    
    /**
     * Get attendance statistics for a specific date
     */
    private function getAttendanceStats($date)
    {
        $totalEmployees = Employee::where('status', 'active')->count();
        
        $presentToday = Attendance::whereDate('date', $date)
            ->whereNotNull('check_in')
            ->count();
            
        $lateToday = Attendance::whereDate('date', $date)
            ->whereNotNull('check_in')
            ->with('employee')
            ->get()
            ->filter(function ($attendance) {
                if (!$attendance->check_in || !$attendance->employee) {
                    return false;
                }
                if ($attendance->status === 'late' || ($attendance->late_minutes ?? 0) > 0) {
                    return true;
                }

                return $this->schedule->isLate(
                    $attendance->employee,
                    Carbon::parse($attendance->check_in),
                    Carbon::parse($attendance->date),
                );
            })
            ->count();

        $earlyDepartures = Attendance::whereDate('date', $date)
            ->whereNotNull('check_out')
            ->with('employee')
            ->get()
            ->filter(function ($attendance) {
                if (!$attendance->check_out || !$attendance->employee) {
                    return false;
                }

                return $this->schedule->isEarlyDeparture(
                    $attendance->employee,
                    Carbon::parse($attendance->check_out),
                    Carbon::parse($attendance->date),
                );
            })
            ->count();
            
        // Calculate average hours manually for SQLite compatibility
        $attendancesWithHours = Attendance::whereDate('date', $date)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->get();
            
        $totalHours = 0;
        $count = 0;
        
        foreach ($attendancesWithHours as $attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $hours = $checkOut->diffInMinutes($checkIn) / 60;
                $totalHours += $hours;
                $count++;
            }
        }
        
        $averageHours = $count > 0 ? $totalHours / $count : 0;
            
        return [
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'late_today' => $lateToday,
            'early_departures' => $earlyDepartures,
            'average_hours' => round($averageHours, 1),
            'attendance_rate' => $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0
        ];
    }
    
    /**
     * Check in employee
     */
    public function checkIn(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $employee = Employee::where('user_id', $currentUser->id)->first();
            
            if (!$employee) {
                return response()->json([
                    'error' => 'لم يتم العثور على سجل موظف. يرجى التأكد من ربط حسابك ببيانات موظف.',
                    'success' => false
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            if ($this->workDay->requiresWorkDayButton($currentUser) && $this->workDay->hasApprovedLeaveOnDate($employee, Carbon::today())) {
                return response()->json([
                    'error' => 'أنت في إجازة معتمدة اليوم — لا يلزم بدء يوم العمل.',
                    'success' => false,
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }

            if ($this->schedule->isWeeklyOffDay($employee, Carbon::today())) {
                return response()->json([
                    'error' => 'اليوم إجازة أسبوعية حسب جدول دوامك — لا يلزم تسجيل الحضور.',
                    'success' => false,
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }
            
            $today = Carbon::today();
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();

            if ($existingAttendance && ($existingAttendance->work_day_locked || $existingAttendance->check_out)) {
                return response()->json([
                    'error' => 'تم إنهاء يوم العمل اليوم ولا يمكن البدء مجدداً.',
                    'success' => false,
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
                
            if ($existingAttendance && $existingAttendance->check_in) {
                return response()->json([
                    'error' => 'تم تسجيل الحضور مسبقاً اليوم',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            $checkInTime = Carbon::now();
            $isLate = $this->schedule->isLate($employee, $checkInTime, $today);
            $lateMinutes = $this->schedule->lateMinutes($employee, $checkInTime, $today);
            $requiredHours = $this->workDay->requiredDailyHours($employee);

            if ($existingAttendance) {
                $existingAttendance->update([
                    'check_in' => $checkInTime,
                    'status' => $isLate ? 'late' : 'present',
                    'current_status' => 'working',
                ]);
                $attendance = $existingAttendance->fresh();
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $today,
                    'check_in' => $checkInTime,
                    'status' => $isLate ? 'late' : 'present',
                    'current_status' => 'working',
                ]);
            }

            $this->workDay->applyCheckInSchedule($attendance, $employee, $checkInTime);
            $attendance->refresh();

            if ($isLate) {
                $this->autoPenalties->tryApplyAttendanceLate($attendance);
            }

            return response()->json([
                'success' => true,
                'message' => $isLate
                    ? 'تم بدء يوم العمل (متأخر ' . $lateMinutes . ' دقيقة)'
                    : 'تم بدء يوم العمل بنجاح',
                'check_in_time' => $checkInTime->format('H:i:s'),
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'scheduled_check_in' => $attendance->scheduled_check_in_at?->format('H:i'),
                'required_daily_hours' => $requiredHours,
                'scheduled_checkout_at' => $attendance->scheduled_checkout_at?->format('H:i'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error in checkIn: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء تسجيل الحضور: ' . $e->getMessage(),
                'success' => false
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Check out employee
     */
    public function checkOut(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $employee = Employee::where('user_id', $currentUser->id)->first();
            
            if (!$employee) {
                return response()->json([
                    'error' => 'لم يتم العثور على سجل موظف. يرجى التأكد من ربط حسابك ببيانات موظف.',
                    'success' => false
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }
            
            $today = Carbon::today();
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
                
            if (!$attendance || !$attendance->check_in) {
                return response()->json([
                    'error' => 'يجب تسجيل الحضور أولاً',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            if ($attendance->check_out) {
                return response()->json([
                    'error' => 'تم تسجيل الانصراف مسبقاً اليوم',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            $review = $this->checkoutReviews->submit($attendance, $employee);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلب الانصراف لمدير العمليات — سيُسجَّل بعد الموافقة',
                'pending_review' => true,
                'requested_at' => $review->requested_check_out_at->format('H:i:s'),
                'total_hours_preview' => $review->total_hours_preview,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json([
                'error' => $e->getMessage() ?: 'تعذر إرسال طلب الانصراف',
                'success' => false,
            ], $e->getStatusCode(), [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error in checkOut: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء تسجيل الانصراف: ' . $e->getMessage(),
                'success' => false
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function cancelCheckout(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $employee = Employee::where('user_id', $currentUser->id)->first();

            if (!$employee) {
                return response()->json(['error' => 'لم يتم العثور على سجل موظف', 'success' => false], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $request->validate(['notes' => 'required|string|max:1000']);

            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', Carbon::today())
                ->first();

            if (!$attendance || $attendance->current_status !== 'checkout_pending') {
                return response()->json(['error' => 'لا يوجد طلب انصراف معلّق', 'success' => false], 400, [], JSON_UNESCAPED_UNICODE);
            }

            $review = \App\Models\AttendanceCheckoutReview::query()
                ->where('attendance_id', $attendance->id)
                ->where('status', \App\Models\AttendanceCheckoutReview::STATUS_PENDING)
                ->latest('id')
                ->first();

            if (!$review) {
                return response()->json(['error' => 'لا يوجد طلب انصراف معلّق', 'success' => false], 400, [], JSON_UNESCAPED_UNICODE);
            }

            $this->checkoutReviews->cancelPending($review, $currentUser, $request->input('notes'));

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء طلب الانصراف — يمكنك إعادة الإرسال',
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'success' => false], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Start break for employee
     */
    public function startBreak(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $employee = Employee::where('user_id', $currentUser->id)->first();
            
            if (!$employee) {
                return response()->json([
                    'error' => 'لم يتم العثور على سجل موظف. يرجى التأكد من ربط حسابك ببيانات موظف.',
                    'success' => false
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }
            
            $today = Carbon::today();
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
                
            if (!$attendance || !$attendance->check_in) {
                return response()->json([
                    'error' => 'يجب تسجيل الحضور أولاً',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            if ($attendance->check_out) {
                return response()->json([
                    'error' => 'تم تسجيل الانصراف مسبقاً اليوم',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            if ($attendance->current_status === 'on_break') {
                return response()->json([
                    'error' => 'أنت في الاستراحة بالفعل',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            if ($attendance->current_status === 'checkout_pending') {
                return response()->json([
                    'error' => 'طلب الانصراف قيد المراجعة — لا يمكن بدء استراحة',
                    'success' => false,
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            $breakStartTime = Carbon::now();
            
            $attendance->update([
                'break_start' => $breakStartTime,
                'current_status' => 'on_break'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم بدء الاستراحة',
                'break_start_time' => $breakStartTime->format('H:i:s')
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error in startBreak: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء بدء الاستراحة: ' . $e->getMessage(),
                'success' => false
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * End break for employee
     */
    public function endBreak(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $employee = Employee::where('user_id', $currentUser->id)->first();
            
            if (!$employee) {
                return response()->json([
                    'error' => 'لم يتم العثور على سجل موظف. يرجى التأكد من ربط حسابك ببيانات موظف.',
                    'success' => false
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }
            
            $today = Carbon::today();
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
                
            if (!$attendance || !$attendance->check_in) {
                return response()->json([
                    'error' => 'يجب تسجيل الحضور أولاً',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            if ($attendance->check_out) {
                return response()->json([
                    'error' => 'تم تسجيل الانصراف مسبقاً اليوم',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            if ($attendance->current_status !== 'on_break') {
                return response()->json([
                    'error' => 'أنت لست في استراحة حالياً',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            $breakEndTime = Carbon::now();
            $breakStartTime = Carbon::parse($attendance->break_start);
            
            // Calculate break duration in minutes (ensure it's positive)
            $breakDurationMinutes = max(0, $breakEndTime->diffInMinutes($breakStartTime));
            
            // Ensure break_end is after break_start
            if ($breakEndTime->lt($breakStartTime)) {
                return response()->json([
                    'error' => 'وقت انتهاء الاستراحة يجب أن يكون بعد وقت بدايتها',
                    'success' => false
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            
            $attendance->update([
                'break_end' => $breakEndTime,
                'break_duration_minutes' => (int)$breakDurationMinutes, // Ensure it's an integer
                'current_status' => 'working'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم انتهاء الاستراحة',
                'break_end_time' => $breakEndTime->format('H:i:s'),
                'break_duration' => $breakDurationMinutes
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error in endBreak: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء انتهاء الاستراحة: ' . $e->getMessage(),
                'success' => false
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Get current work time for employee
     * This returns the work time for TODAY only, starting from check_in
     * Each day starts fresh from zero
     */
    public function autoCheckOut(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $employee = Employee::where('user_id', $currentUser->id)->first();

            if (!$employee) {
                return response()->json(['error' => 'لم يتم العثور على سجل موظف', 'success' => false], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $attendance = $this->workDay->todayAttendance($employee);

            if (!$attendance || !$attendance->check_in) {
                return response()->json(['error' => 'لا توجد جلسة عمل نشطة', 'success' => false], 400, [], JSON_UNESCAPED_UNICODE);
            }

            if (!$this->workDay->performAutoCheckout($attendance, $employee)) {
                return response()->json(['error' => 'لم يحن وقت الإيقاف التلقائي بعد', 'success' => false], 400, [], JSON_UNESCAPED_UNICODE);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إيقاف يوم العمل تلقائياً عند اكتمال المدة المطلوبة',
                'auto_checkout' => true,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error in autoCheckOut: ' . $e->getMessage());

            return response()->json(['error' => 'حدث خطأ أثناء الإيقاف التلقائي', 'success' => false], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getCurrentWorkTime()
    {
        $currentUser = Auth::user();
        $employee = Employee::where('user_id', $currentUser->id)->first();
        
        if (!$employee) {
            return response()->json(['error' => 'Employee record not found'], 404);
        }

        $this->workDay->processExpiredSessions();
        
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();
            
        // If no attendance record for today or no check_in, return zero time
        if (!$attendance || !$attendance->check_in) {
            $payload = [
                'is_working' => false,
                'work_time' => '00:00:00',
                'current_status' => 'not_started',
                'check_in_time' => null,
                'date' => $today->format('Y-m-d'),
            ];

            return response()->json(
                $this->workDay->enrichWorkTimePayload($payload, $currentUser, null),
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
        
        // If employee has already checked out today, return the final time
        if ($attendance->check_out) {
            // Calculate total working time from check_in to check_out
            $checkInTime = Carbon::parse($attendance->check_in);
            $checkOutTime = Carbon::parse($attendance->check_out);
            $totalSeconds = $checkOutTime->diffInSeconds($checkInTime);
            
            // Subtract break time if exists
            if ($attendance->break_duration_minutes) {
                $totalSeconds -= ($attendance->break_duration_minutes * 60);
            }
            
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = $totalSeconds % 60;
            
            $payload = [
                'is_working' => false,
                'work_time' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
                'total_hours' => $attendance->total_hours,
                'current_status' => 'completed',
                'check_in_time' => $attendance->check_in->format('H:i:s'),
                'check_out_time' => $attendance->check_out->format('H:i:s'),
                'date' => $today->format('Y-m-d'),
                'work_day_locked' => (bool) $attendance->work_day_locked,
                'auto_checkout' => (bool) $attendance->auto_checkout,
            ];

            return response()->json(
                $this->workDay->enrichWorkTimePayload($payload, $currentUser, $attendance),
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
        
        // Employee is currently working - calculate from check_in to now
        $currentTime = Carbon::now();
        $checkInTime = Carbon::parse($attendance->check_in);
        
        // Calculate total seconds from check_in to now (ensure positive)
        $totalSeconds = abs($currentTime->diffInSeconds($checkInTime));
        
        // If employee is on break, don't count break time
        if ($attendance->current_status === 'on_break' && $attendance->break_start) {
            // Calculate seconds from check_in to break_start
            $breakStartTime = Carbon::parse($attendance->break_start);
            $totalSeconds = abs($breakStartTime->diffInSeconds($checkInTime));
        } else {
            // Subtract completed break time if exists
            if ($attendance->break_duration_minutes) {
                $breakSeconds = (int)$attendance->break_duration_minutes * 60;
                $totalSeconds = max(0, $totalSeconds - $breakSeconds);
            }
        }
        
        // Ensure totalSeconds is positive
        $totalSeconds = max(0, $totalSeconds);
        
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        if ($attendance->scheduled_checkout_at && $attendance->scheduled_checkout_at->lte(now()) && !$attendance->check_out) {
            $this->workDay->performAutoCheckout($attendance, $employee);
            $attendance->refresh();

            return $this->getCurrentWorkTime();
        }

        $payload = [
            'is_working' => true,
            'work_time' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
            'work_time_seconds' => $totalSeconds,
            'check_in_time' => $attendance->check_in->format('H:i:s'),
            'check_in_datetime' => $attendance->check_in->toIso8601String(),
            'current_status' => $attendance->current_status ?? 'working',
            'break_start_time' => $attendance->break_start ? $attendance->break_start->format('H:i:s') : null,
            'date' => $today->format('Y-m-d'),
            'required_hours' => (float) ($attendance->required_hours ?? $this->workDay->requiredDailyHours($employee)),
        ];

        return response()->json(
            $this->workDay->enrichWorkTimePayload($payload, $currentUser, $attendance),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

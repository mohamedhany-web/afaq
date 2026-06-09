<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
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
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::today();
        $currentUser = Auth::user();
        
        // Get user's employee record
        $employee = Employee::where('user_id', $currentUser->id)->first();
        
        // Get today's attendance for current user
        $todayAttendance = null;
        if ($employee) {
            $todayAttendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
        }
        
        // Get attendance statistics for today
        $stats = $this->getAttendanceStats($today);
        
        // Get recent attendance records for current user
        $recentAttendances = collect();
        if ($employee) {
            $recentAttendances = Attendance::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->limit(10)
                ->with('employee')
                ->get();
        }
        
        // Get all employees for admin view
        $employees = collect();
        if ($currentUser->hasRole(['admin', 'hr', 'super_admin'])) {
            $employees = Employee::with(['user', 'department'])
                ->where('status', 'active')
                ->get();
        }
        
        // Get all attendance records for today (for admin view)
        $allTodayAttendances = collect();
        if ($currentUser->hasRole(['admin', 'hr', 'super_admin'])) {
            $allTodayAttendances = Attendance::whereDate('date', $today)
                ->with(['employee.user', 'employee.department'])
                ->get();
        }
        
        return view('attendances.index', compact(
            'todayAttendance',
            'stats',
            'recentAttendances',
            'employees',
            'allTodayAttendances',
            'employee'
        ));
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
            
            $checkOutTime = Carbon::now();
            $checkInTime = Carbon::parse($attendance->check_in);
            
            // Calculate total hours
            $totalMinutes = $checkOutTime->diffInMinutes($checkInTime);
            
            // Subtract break time if exists
            if ($attendance->break_duration_minutes) {
                $totalMinutes -= $attendance->break_duration_minutes;
            }
            
            $totalHours = round($totalMinutes / 60, 2);
            $isEarlyDeparture = $this->schedule->isEarlyDeparture($employee, $checkOutTime, $today);
            $wasLate = ($attendance->late_minutes ?? 0) > 0 || $attendance->status === 'late';

            $requiredHours = (float) ($attendance->required_hours ?? $this->workDay->requiredDailyHours($employee));
            $metRequired = $totalHours >= ($requiredHours - 0.1);

            $finalStatus = 'present';
            if ($wasLate) {
                $finalStatus = 'late';
            } elseif ($isEarlyDeparture && !$metRequired) {
                $finalStatus = 'half_day';
            }

            $attendance->update([
                'check_out' => $checkOutTime,
                'total_hours' => $totalHours,
                'status' => $finalStatus,
                'current_status' => 'completed',
                'work_day_locked' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => $metRequired
                    ? ($wasLate ? 'تم إنهاء اليوم (مع تأخير في الحضور)' : 'تم إنهاء يوم العمل بنجاح')
                    : ($isEarlyDeparture ? 'تم إنهاء اليوم (انصراف مبكر — لم تكتمل الساعات)' : 'تم إنهاء يوم العمل'),
                'check_out_time' => $checkOutTime->format('H:i:s'),
                'total_hours' => $totalHours,
                'is_early' => $isEarlyDeparture,
                'scheduled_checkout' => $attendance->scheduled_checkout_at?->format('H:i'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error in checkOut: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء تسجيل الانصراف: ' . $e->getMessage(),
                'success' => false
            ], 500, [], JSON_UNESCAPED_UNICODE);
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

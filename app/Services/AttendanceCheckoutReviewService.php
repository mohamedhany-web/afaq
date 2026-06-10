<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceCheckoutReview;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceCheckoutReviewService
{
    public function __construct(
        protected EmployeeScheduleService $schedule,
        protected WorkDayService $workDay,
    ) {}

    public function submit(Attendance $attendance, Employee $employee): AttendanceCheckoutReview
    {
        if ($attendance->check_out) {
            abort(422, 'تم تسجيل الانصراف مسبقاً اليوم');
        }

        if (!$attendance->check_in) {
            abort(422, 'يجب تسجيل الحضور أولاً');
        }

        if ($attendance->current_status === 'on_break') {
            abort(422, 'يجب إنهاء الاستراحة قبل تسجيل الانصراف');
        }

        if ($attendance->current_status === 'checkout_pending') {
            abort(422, 'طلب الانصراف قيد المراجعة لدى العمليات');
        }

        $existing = AttendanceCheckoutReview::query()
            ->where('attendance_id', $attendance->id)
            ->where('status', AttendanceCheckoutReview::STATUS_PENDING)
            ->exists();

        if ($existing) {
            abort(422, 'يوجد طلب انصراف معلّق بالفعل');
        }

        $checkOutTime = Carbon::now();
        $preview = $this->calculateCheckoutMetrics($attendance, $employee, $checkOutTime);

        return DB::transaction(function () use ($attendance, $employee, $checkOutTime, $preview) {
            $review = AttendanceCheckoutReview::create([
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'review_date' => Carbon::today(),
                'requested_check_out_at' => $checkOutTime,
                'total_hours_preview' => $preview['total_hours'],
                'is_early_departure' => $preview['is_early'],
                'met_required_hours' => $preview['met_required'],
                'status' => AttendanceCheckoutReview::STATUS_PENDING,
            ]);

            $attendance->update(['current_status' => 'checkout_pending']);

            $this->notifyOperations($review);

            return $review;
        });
    }

    public function approve(AttendanceCheckoutReview $review, User $reviewer, ?string $notes = null): void
    {
        if (!$review->isPending()) {
            abort(422, 'تمت معالجة هذا الطلب مسبقاً');
        }

        DB::transaction(function () use ($review, $reviewer, $notes) {
            $attendance = $review->attendance()->lockForUpdate()->firstOrFail();
            $employee = $review->employee;

            if ($attendance->check_out) {
                abort(422, 'تم تسجيل الانصراف مسبقاً');
            }

            $checkOutTime = Carbon::parse($review->requested_check_out_at);
            $metrics = $this->calculateCheckoutMetrics($attendance, $employee, $checkOutTime);

            $attendance->update([
                'check_out' => $checkOutTime,
                'total_hours' => $metrics['total_hours'],
                'status' => $metrics['final_status'],
                'current_status' => 'completed',
                'work_day_locked' => true,
            ]);

            $review->update([
                'status' => AttendanceCheckoutReview::STATUS_APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);

            $this->notifyEmployee(
                $employee,
                'checkout_approved',
                'تم اعتماد انصرافك',
                'وافقت العمليات على تسجيل انصرافك — ' . $checkOutTime->format('H:i'),
            );
        });
    }

    public function reject(AttendanceCheckoutReview $review, User $reviewer, ?string $notes = null): void
    {
        if (!$review->isPending()) {
            abort(422, 'تمت معالجة هذا الطلب مسبقاً');
        }

        DB::transaction(function () use ($review, $reviewer, $notes) {
            $attendance = $review->attendance;
            if ($attendance && !$attendance->check_out) {
                $attendance->update(['current_status' => 'working']);
            }

            $review->update([
                'status' => AttendanceCheckoutReview::STATUS_REJECTED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);

            $this->notifyEmployee(
                $review->employee,
                'checkout_rejected',
                'تم رفض طلب الانصراف',
                $notes ?: 'راجع مدير العمليات لمزيد من التفاصيل',
            );
        });
    }

    /** @return array{total_hours: float, is_early: bool, met_required: bool, final_status: string} */
    public function calculateCheckoutMetrics(Attendance $attendance, Employee $employee, Carbon $checkOutTime): array
    {
        $checkInTime = Carbon::parse($attendance->check_in);
        $totalMinutes = $checkOutTime->diffInMinutes($checkInTime);

        if ($attendance->break_duration_minutes) {
            $totalMinutes -= $attendance->break_duration_minutes;
        }

        $totalHours = round($totalMinutes / 60, 2);
        $date = Carbon::parse($attendance->date);
        $isEarly = $this->schedule->isEarlyDeparture($employee, $checkOutTime, $date);
        $wasLate = ($attendance->late_minutes ?? 0) > 0 || $attendance->status === 'late';
        $requiredHours = (float) ($attendance->required_hours ?? $this->workDay->requiredDailyHours($employee));
        $metRequired = $totalHours >= ($requiredHours - 0.1);

        $finalStatus = 'present';
        if ($wasLate) {
            $finalStatus = 'late';
        } elseif ($isEarly && !$metRequired) {
            $finalStatus = 'half_day';
        }

        return [
            'total_hours' => $totalHours,
            'is_early' => $isEarly,
            'met_required' => $metRequired,
            'final_status' => $finalStatus,
        ];
    }

    protected function notifyOperations(AttendanceCheckoutReview $review): void
    {
        $review->loadMissing(['employee.user']);

        $opsUsers = User::role('operation_manager')->get()
            ->merge(User::role(['super_admin', 'admin'])->get())
            ->unique('id');

        $employeeName = trim(($review->employee->first_name ?? '') . ' ' . ($review->employee->last_name ?? ''));

        foreach ($opsUsers as $ops) {
            CrmNotificationService::notify(
                $ops->id,
                'attendance_checkout_pending',
                'طلب انصراف بانتظار المراجعة',
                $employeeName . ' — ' . $review->requested_check_out_at->format('H:i'),
                [
                    'review_id' => $review->id,
                    'url' => route('operations.checkout-reviews.index'),
                ],
                'attendance_checkout_pending:' . $review->id,
            );
        }
    }

    protected function notifyEmployee(?Employee $employee, string $type, string $title, string $body): void
    {
        if (!$employee?->user_id) {
            return;
        }

        CrmNotificationService::notify(
            $employee->user_id,
            $type,
            $title,
            $body,
            ['url' => route('attendances.index')],
        );
    }
}

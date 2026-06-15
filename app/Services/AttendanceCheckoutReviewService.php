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
        protected AutoPenaltyService $penalties,
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

            $deduction = $this->applyApprovalPenalties($review, $attendance, $employee, $metrics, $notes);
            if ($deduction) {
                $review->update([
                    'deduction_amount' => $deduction['amount'],
                    'deduction_reason' => $deduction['reason'],
                ]);
            }

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

            $deduction = $this->applyRejectPenalties($review, $attendance, $notes);
            if ($deduction) {
                $review->update([
                    'deduction_amount' => $deduction['amount'],
                    'deduction_reason' => $deduction['reason'],
                ]);
            }

            $this->notifyEmployee(
                $review->employee,
                'checkout_rejected',
                'تم رفض طلب الانصراف',
                $notes ?: 'راجع مدير العمليات لمزيد من التفاصيل',
            );
        });
    }

    public function revoke(AttendanceCheckoutReview $review, User $reviewer, string $notes): void
    {
        if (!$review->isApproved()) {
            abort(422, 'يمكن إلغاء الاعتماد للطلبات المعتمدة فقط');
        }

        DB::transaction(function () use ($review, $reviewer, $notes) {
            $attendance = $review->attendance()->lockForUpdate()->firstOrFail();

            $this->penalties->reverseBySourceKey(
                $review->penaltySourceKey(),
                $notes,
            );

            $attendance->update([
                'check_out' => null,
                'total_hours' => null,
                'work_day_locked' => false,
                'current_status' => 'working',
                'notes' => trim(($attendance->notes ? $attendance->notes . ' | ' : '') . 'إلغاء اعتماد الانصراف — ' . $notes),
            ]);

            $review->update([
                'status' => AttendanceCheckoutReview::STATUS_REVOKED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
                'deduction_amount' => null,
                'deduction_reason' => null,
            ]);

            $this->notifyEmployee(
                $review->employee,
                'checkout_revoked',
                'تم إلغاء اعتماد الانصراف',
                $notes,
            );
        });
    }

    public function cancelPending(AttendanceCheckoutReview $review, User $actor, string $notes): void
    {
        if (!$review->isPending()) {
            abort(422, 'لا يمكن إلغاء هذا الطلب');
        }

        DB::transaction(function () use ($review, $actor, $notes) {
            $attendance = $review->attendance;
            if ($attendance && !$attendance->check_out) {
                $attendance->update(['current_status' => 'working']);
            }

            $review->update([
                'status' => AttendanceCheckoutReview::STATUS_CANCELLED,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);
        });
    }

    /** @return array{amount: float, reason: string}|null */
    protected function applyApprovalPenalties(
        AttendanceCheckoutReview $review,
        Attendance $attendance,
        Employee $employee,
        array $metrics,
        ?string $notes,
    ): ?array {
        $user = $employee->user;
        if (!$user) {
            return null;
        }

        $sourceKey = $review->penaltySourceKey();
        $total = 0.0;
        $reasons = [];

        $lateMinutes = (int) ($attendance->late_minutes ?? 0);
        if ($lateMinutes > 0 || $attendance->status === 'late') {
            $amount = $this->penalties->applyReviewPenalty(
                $user,
                'attendance_late',
                $sourceKey . ':late',
                'تأخر حضور ' . Carbon::parse($attendance->date)->format('Y-m-d'),
                $notes,
                ['late_minutes' => max($lateMinutes, 1)],
            );
            if ($amount) {
                $total += $amount;
                $reasons[] = 'تأخر حضور';
            }
        }

        if (!$metrics['met_required'] || $metrics['is_early']) {
            $amount = $this->penalties->applyReviewPenalty(
                $user,
                'attendance_short_hours',
                $sourceKey . ':short',
                'ساعات عمل ناقصة ' . Carbon::parse($attendance->date)->format('Y-m-d'),
                $notes,
            );
            if ($amount) {
                $total += $amount;
                $reasons[] = 'ساعات ناقصة / انصراف مبكر';
            }
        }

        if ($total <= 0) {
            return null;
        }

        return [
            'amount' => $total,
            'reason' => implode(' + ', $reasons),
        ];
    }

    /** @return array{amount: float, reason: string}|null */
    protected function applyRejectPenalties(
        AttendanceCheckoutReview $review,
        ?Attendance $attendance,
        string $notes,
    ): ?array {
        $user = $review->employee?->user;
        if (!$user || !$attendance) {
            return null;
        }

        if (!$review->is_early_departure) {
            return null;
        }

        $amount = $this->penalties->applyReviewPenalty(
            $user,
            'attendance_short_hours',
            $review->penaltySourceKey() . ':reject',
            'رفض انصراف مبكر ' . Carbon::parse($attendance->date)->format('Y-m-d'),
            $notes,
        );

        if (!$amount) {
            return null;
        }

        return [
            'amount' => $amount,
            'reason' => 'انصراف مبكر مرفوض',
        ];
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

    public function pendingCount(?Carbon $date = null): int
    {
        $date ??= Carbon::today();

        return AttendanceCheckoutReview::query()
            ->whereDate('review_date', $date)
            ->where('status', AttendanceCheckoutReview::STATUS_PENDING)
            ->count();
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

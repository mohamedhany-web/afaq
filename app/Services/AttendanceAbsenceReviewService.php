<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceAbsenceReview;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceAbsenceReviewService
{
    public function __construct(
        protected EmployeeWorkCalendarService $calendar,
        protected WorkDayService $workDay,
        protected OrganizationalHierarchyService $hierarchy,
        protected EmployeeScheduleService $schedule,
        protected AutoPenaltyService $penalties,
    ) {}

    /** @return int عدد السجلات المُنشأة */
    public function flagAbsencesForDate(Carbon $date): int
    {
        $date = $date->copy()->startOfDay();
        $created = 0;

        User::query()
            ->whereHas('employee', fn ($q) => $q->where('status', 'active'))
            ->with(['employee.department', 'employee.user'])
            ->chunkById(100, function ($users) use ($date, &$created) {
                foreach ($users as $user) {
                    if ($this->workDay->isExempt($user)) {
                        continue;
                    }

                    $employee = $user->employee;
                    if (!$employee || !$this->calendar->isExpectedWorkDay($user, $date)) {
                        continue;
                    }

                    $attendance = Attendance::query()
                        ->where('employee_id', $employee->id)
                        ->whereDate('date', $date)
                        ->first();

                    $reason = $this->detectReason($user, $employee, $date, $attendance);
                    if (!$reason) {
                        continue;
                    }

                    $review = AttendanceAbsenceReview::firstOrNew([
                        'employee_id' => $employee->id,
                        'review_date' => $date->toDateString(),
                    ]);

                    if ($review->exists && !$review->isPending()) {
                        continue;
                    }

                    $review->fill([
                        'attendance_id' => $attendance?->id,
                        'flag_reason' => $reason,
                        'status' => AttendanceAbsenceReview::STATUS_PENDING,
                        'has_approved_leave' => $this->workDay->hasApprovedLeaveOnDate($employee, $date),
                        'reports_to_user_id' => $this->hierarchy->resolveReportsTo($employee),
                    ]);
                    $review->save();
                    $created++;
                }
            });

        return $created;
    }

    public function autoConfirmOverduePending(Carbon $date): int
    {
        $deadlineHour = (int) config('organizational_hierarchy.absence_review_deadline_hour', 12);
        $cutoff = $date->copy()->addDay()->setTime($deadlineHour, 0);

        if (now()->lt($cutoff)) {
            return 0;
        }

        $count = 0;
        AttendanceAbsenceReview::query()
            ->whereDate('review_date', $date)
            ->where('status', AttendanceAbsenceReview::STATUS_PENDING)
            ->where('has_approved_leave', false)
            ->with('employee')
            ->chunkById(50, function ($reviews) use (&$count) {
                foreach ($reviews as $review) {
                    $this->applyAbsentStatus($review, null, 'تأكيد تلقائي — لم تُراجع من مدير العمليات');
                    $review->update([
                        'status' => AttendanceAbsenceReview::STATUS_AUTO_CONFIRMED,
                        'reviewed_at' => now(),
                        'review_notes' => 'تأكيد تلقائي بعد انتهاء مهلة المراجعة',
                    ]);
                    $this->applyAbsencePenalty($review, 'تأكيد تلقائي — غياب');
                    $count++;
                }
            });

        return $count;
    }

    public function confirmAbsent(AttendanceAbsenceReview $review, User $reviewer, ?string $notes = null): void
    {
        DB::transaction(function () use ($review, $reviewer, $notes) {
            $this->applyAbsentStatus($review, $reviewer, $notes);
            $review->update([
                'status' => AttendanceAbsenceReview::STATUS_CONFIRMED_ABSENT,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);

            $this->applyAbsencePenalty($review, $notes ?? 'غياب مؤكد');
        });
    }

    public function confirmPresent(AttendanceAbsenceReview $review, User $reviewer, ?string $notes = null): void
    {
        DB::transaction(function () use ($review, $reviewer, $notes) {
            $employee = $review->employee;
            $date = $review->review_date;

            $attendance = Attendance::firstOrNew([
                'employee_id' => $employee->id,
                'date' => $date->toDateString(),
            ]);

            if (!$attendance->check_in) {
                $checkIn = $this->schedule->scheduledCheckInAt($employee, $date);
                $attendance->check_in = $checkIn;
                $attendance->status = 'present';
                $attendance->current_status = 'completed';
                $attendance->check_out = $this->schedule->scheduledCheckOutAt($employee, $date);
                $attendance->total_hours = $this->workDay->requiredDailyHours($employee);
                $attendance->work_day_locked = true;
                $attendance->notes = trim('حضور مؤكد من مدير العمليات' . ($notes ? " — {$notes}" : ''));
                $attendance->save();
                $this->workDay->applyCheckInSchedule($attendance, $employee, Carbon::parse($attendance->check_in));
            } else {
                $attendance->update([
                    'status' => 'present',
                    'notes' => trim(($attendance->notes ? $attendance->notes . ' | ' : '') . 'حضور مؤكد — عمليات'),
                ]);
            }

            $review->update([
                'attendance_id' => $attendance->id,
                'status' => AttendanceAbsenceReview::STATUS_CONFIRMED_PRESENT,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);
        });
    }

    public function excuse(AttendanceAbsenceReview $review, User $reviewer, string $notes): void
    {
        $this->penalties->reverseBySourceKey(
            'absence_review:' . $review->id,
            $notes,
        );

        $review->update([
            'status' => AttendanceAbsenceReview::STATUS_EXCUSED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function revokeConfirmation(AttendanceAbsenceReview $review, User $reviewer, string $notes): void
    {
        if (! in_array($review->status, [
            AttendanceAbsenceReview::STATUS_CONFIRMED_ABSENT,
            AttendanceAbsenceReview::STATUS_AUTO_CONFIRMED,
        ], true)) {
            abort(422, 'لا يمكن إلغاء هذا القرار');
        }

        DB::transaction(function () use ($review, $reviewer, $notes) {
            $this->penalties->reverseBySourceKey('absence_review:' . $review->id, $notes);

            if ($review->attendance) {
                $review->attendance->update([
                    'status' => 'present',
                    'current_status' => 'working',
                    'work_day_locked' => false,
                    'notes' => trim(($review->attendance->notes ? $review->attendance->notes . ' | ' : '') . 'إلغاء قرار الغياب — ' . $notes),
                ]);
            }

            $review->update([
                'status' => AttendanceAbsenceReview::STATUS_PENDING,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);
        });
    }

    protected function applyAbsencePenalty(AttendanceAbsenceReview $review, string $notes): void
    {
        $user = $review->employee?->user;
        if (!$user) {
            return;
        }

        $sourceType = match ($review->flag_reason) {
            AttendanceAbsenceReview::REASON_SHORT_HOURS => 'attendance_short_hours',
            default => 'attendance_no_start',
        };

        $this->penalties->applyReviewPenalty(
            $user,
            $sourceType,
            'absence_review:' . $review->id,
            'مراجعة غياب ' . $review->review_date->format('Y-m-d'),
            $notes,
        );
    }

    protected function applyAbsentStatus(AttendanceAbsenceReview $review, ?User $reviewer, ?string $notes): void
    {
        $employee = $review->employee;
        $date = $review->review_date;

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $date->toDateString(),
        ]);

        $attendance->fill([
            'status' => 'absent',
            'current_status' => 'completed',
            'notes' => trim('غياب مؤكد' . ($notes ? " — {$notes}" : '')),
        ]);
        $attendance->save();

        $review->attendance_id = $attendance->id;
        $review->save();
    }

    protected function detectReason(User $user, Employee $employee, Carbon $date, ?Attendance $attendance): ?string
    {
        if ($this->workDay->hasApprovedLeaveOnDate($employee, $date)) {
            return null;
        }

        if (!$attendance || !$attendance->check_in) {
            $deadline = $this->schedule->scheduledCheckInAt($employee, $date)
                ->copy()
                ->addMinutes($this->schedule->lateGraceMinutes($employee))
                ->addHours((int) config('auto_penalties.no_start_grace_hours_after_shift', 2));

            if (now()->lt($deadline)) {
                return null;
            }

            return AttendanceAbsenceReview::REASON_NO_CHECK_IN;
        }

        if ($attendance->status === 'leave') {
            return AttendanceAbsenceReview::REASON_UNAPPROVED_LEAVE;
        }

        $required = (float) ($attendance->required_hours ?? $this->workDay->requiredDailyHours($employee));
        if ($attendance->check_out && $attendance->total_hours && (float) $attendance->total_hours < ($required - 0.25)) {
            return AttendanceAbsenceReview::REASON_SHORT_HOURS;
        }

        return null;
    }

    public function pendingCount(?Carbon $date = null): int
    {
        return AttendanceAbsenceReview::query()
            ->where('status', AttendanceAbsenceReview::STATUS_PENDING)
            ->when($date, fn ($q) => $q->whereDate('review_date', $date))
            ->count();
    }
}

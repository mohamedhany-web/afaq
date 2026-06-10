<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAbsenceReview extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED_ABSENT = 'confirmed_absent';
    public const STATUS_CONFIRMED_PRESENT = 'confirmed_present';
    public const STATUS_EXCUSED = 'excused';
    public const STATUS_AUTO_CONFIRMED = 'auto_confirmed';

    public const REASON_NO_CHECK_IN = 'no_check_in';
    public const REASON_SHORT_HOURS = 'short_hours';
    public const REASON_UNAPPROVED_LEAVE = 'unapproved_leave';

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'review_date',
        'flag_reason',
        'status',
        'has_approved_leave',
        'reports_to_user_id',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'review_date' => 'date',
        'has_approved_leave' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function lineManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reports_to_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmedAbsent(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED_ABSENT, self::STATUS_AUTO_CONFIRMED], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED_ABSENT => 'غياب مؤكد',
            self::STATUS_CONFIRMED_PRESENT => 'حضور مؤكد',
            self::STATUS_EXCUSED => 'معذور',
            self::STATUS_AUTO_CONFIRMED => 'غياب (تلقائي)',
            default => 'بانتظار المراجعة',
        };
    }

    public function reasonLabel(): string
    {
        return match ($this->flag_reason) {
            self::REASON_SHORT_HOURS => 'ساعات عمل ناقصة',
            self::REASON_UNAPPROVED_LEAVE => 'إجازة غير معتمدة',
            default => 'لم يُسجّل حضور',
        };
    }
}

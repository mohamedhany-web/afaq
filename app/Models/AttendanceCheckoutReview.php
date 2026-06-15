<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCheckoutReview extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVOKED = 'revoked';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'attendance_id',
        'employee_id',
        'review_date',
        'requested_check_out_at',
        'total_hours_preview',
        'is_early_departure',
        'met_required_hours',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'deduction_amount',
        'deduction_reason',
    ];

    protected $casts = [
        'review_date' => 'date',
        'requested_check_out_at' => 'datetime',
        'total_hours_preview' => 'decimal:2',
        'is_early_departure' => 'boolean',
        'met_required_hours' => 'boolean',
        'reviewed_at' => 'datetime',
        'deduction_amount' => 'decimal:2',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_REVOKED => 'ملغى الاعتماد',
            self::STATUS_CANCELLED => 'ملغى من الموظف',
            default => 'بانتظار العمليات',
        };
    }

    public function penaltySourceKey(): string
    {
        return 'checkout_review:' . $this->id;
    }
}

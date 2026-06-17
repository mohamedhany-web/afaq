<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFollowUp extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const TYPES = ['note', 'call', 'meeting', 'viewing', 'follow_up'];

    public const TYPE_LABELS = [
        'note' => 'ملاحظة',
        'call' => 'مكالمة',
        'meeting' => 'اجتماع',
        'viewing' => 'اجتماع عقاري',
        'follow_up' => 'متابعة',
    ];

    protected $fillable = [
        'user_id',
        'created_by',
        'client_id',
        'sale_id',
        'interaction_type',
        'notes',
        'scheduled_at',
        'status',
        'completed_at',
        'reminder_sent_at',
        'overdue_notified_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'overdue_notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->interaction_type] ?? $this->interaction_type;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->scheduled_at->isPast();
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }
}

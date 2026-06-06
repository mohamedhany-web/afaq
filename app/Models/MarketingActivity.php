<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingActivity extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'title', 'description', 'type', 'status', 'priority', 'campaign_id',
        'assigned_to', 'assigned_by', 'due_at', 'completed_at', 'recurrence',
        'recurrence_interval', 'parent_activity_id', 'next_occurrence_at',
        'completion_notes', 'notes',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_occurrence_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_activity_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_activity_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->pending()
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->pending()->whereDate('due_at', today());
    }

    public function isOverdue(): bool
    {
        return $this->due_at
            && $this->due_at->isPast()
            && in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS], true);
    }

    public function typeLabel(): string
    {
        return config('marketing.activity_types.' . $this->type, $this->type);
    }

    public function statusLabel(): string
    {
        if ($this->isOverdue()) {
            return 'متأخرة';
        }

        return config('marketing.activity_statuses.' . $this->status, $this->status);
    }

    public function priorityLabel(): string
    {
        return config('marketing.priorities.' . $this->priority, $this->priority);
    }

    public function recurrenceLabel(): string
    {
        return config('marketing.recurrence.' . $this->recurrence, $this->recurrence);
    }
}

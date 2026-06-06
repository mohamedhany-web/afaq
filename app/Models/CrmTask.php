<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmTask extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'title', 'description', 'assigned_to', 'assigned_by', 'assigner_type',
        'priority', 'status', 'category', 'client_id', 'project_id', 'sale_id',
        'due_at', 'accepted_at', 'started_at', 'completed_at', 'verified_at', 'verified_by',
        'completion_notes', 'performance_score', 'requires_acceptance', 'auto_generated',
        'source_key', 'sales_team_id', 'reminder_sent_at', 'escalated_at', 'meta',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'escalated_at' => 'datetime',
        'requires_acceptance' => 'boolean',
        'auto_generated' => 'boolean',
        'performance_score' => 'decimal:2',
        'meta' => 'array',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function salesTeam(): BelongsTo
    {
        return $this->belongsTo(SalesTeam::class, 'sales_team_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CrmTaskLog::class, 'task_id')->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', config('crm_tasks.active_statuses', []));
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_at', today());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_at', '<', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_IN_PROGRESS, self::STATUS_OVERDUE]);
    }

    public function isOverdue(): bool
    {
        return $this->due_at->isPast()
            && in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_IN_PROGRESS, self::STATUS_OVERDUE], true);
    }

    public function priorityLabel(): string
    {
        return config('crm_tasks.priority_labels.' . $this->priority, $this->priority);
    }

    public function statusLabel(): string
    {
        if ($this->isOverdue() && $this->status !== self::STATUS_OVERDUE) {
            return 'متأخرة';
        }

        return config('crm_tasks.status_labels.' . $this->status, $this->status);
    }

    public function categoryLabel(): string
    {
        return config('crm_tasks.category_labels.' . $this->category, $this->category);
    }
}

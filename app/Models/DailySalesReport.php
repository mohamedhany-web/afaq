<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySalesReport extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';

    protected $fillable = [
        'user_id',
        'report_date',
        'metrics',
        'obstacles',
        'support_required',
        'tomorrow_planned_calls',
        'tomorrow_planned_meetings',
        'tomorrow_planned_visits',
        'tomorrow_priority_leads',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'metrics' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function metric(string $section, string $key, mixed $default = 0): mixed
    {
        return data_get($this->metrics, "{$section}.{$key}", $default);
    }
}

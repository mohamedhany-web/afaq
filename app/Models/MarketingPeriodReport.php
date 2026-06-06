<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingPeriodReport extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';

    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';

    protected $fillable = [
        'user_id', 'period_type', 'period_start', 'period_end', 'metrics',
        'activities_summary', 'campaigns_progress', 'obstacles', 'support_required',
        'next_period_plan', 'team_summary', 'status', 'submitted_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
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

    public function periodLabel(): string
    {
        return config('marketing_reports.period_types.' . $this->period_type, $this->period_type);
    }

    public function periodRangeLabel(): string
    {
        if ($this->period_type === self::PERIOD_DAILY) {
            return $this->period_start->format('Y-m-d');
        }

        return $this->period_start->format('Y-m-d') . ' → ' . $this->period_end->format('Y-m-d');
    }

    public static function resolvePeriod(string $type, Carbon|string $anchor): array
    {
        $date = Carbon::parse($anchor)->startOfDay();

        return match ($type) {
            self::PERIOD_WEEKLY => [
                'start' => $date->copy()->startOfWeek(Carbon::SATURDAY),
                'end' => $date->copy()->endOfWeek(Carbon::SATURDAY),
            ],
            self::PERIOD_MONTHLY => [
                'start' => $date->copy()->startOfMonth(),
                'end' => $date->copy()->endOfMonth(),
            ],
            default => [
                'start' => $date->copy(),
                'end' => $date->copy(),
            ],
        };
    }

    public function metric(string $section, string $key, mixed $default = 0): mixed
    {
        return data_get($this->metrics, "{$section}.{$key}", $default);
    }
}

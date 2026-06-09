<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingPlan extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'title',
        'description',
        'objectives',
        'month',
        'year',
        'status',
        'campaign_id',
        'manager_id',
        'created_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(MarketingActivity::class, 'marketing_plan_id');
    }

    public function periodLabel(): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        return ($months[$this->month] ?? $this->month) . ' ' . $this->year;
    }

    public function statusLabel(): string
    {
        return config('marketing.plan_statuses.' . $this->status, $this->status);
    }

    public function progressPercent(): int
    {
        $total = $this->activities()->count();
        if ($total === 0) {
            return 0;
        }

        $done = $this->activities()->where('status', MarketingActivity::STATUS_COMPLETED)->count();

        return (int) round(($done / $total) * 100);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'name', 'description', 'channel', 'status', 'budget', 'spent_amount',
        'target_leads', 'start_date', 'end_date', 'project_id', 'manager_id',
        'created_by', 'notes',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
        return $this->hasMany(MarketingActivity::class, 'campaign_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Client::class, 'marketing_campaign_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function channelLabel(): string
    {
        return config('marketing.channels.' . $this->channel, $this->channel);
    }

    public function statusLabel(): string
    {
        return config('marketing.campaign_statuses.' . $this->status, $this->status);
    }

    public function leadsCount(): int
    {
        return $this->leads()->count();
    }
}

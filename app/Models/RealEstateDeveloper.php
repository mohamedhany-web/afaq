<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RealEstateDeveloper extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'نشط',
        self::STATUS_INACTIVE => 'موقوف',
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'website',
        'notes',
        'description',
        'address',
        'city',
        'status',
        'portal_enabled',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'portal_enabled' => 'boolean',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(DeveloperContract::class);
    }

    public function activeContract(): HasOne
    {
        return $this->hasOne(DeveloperContract::class)
            ->where('status', DeveloperContract::STATUS_ACTIVE)
            ->latestOfMany();
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(DeveloperAccount::class);
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(DeveloperPortfolioItem::class)->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeContracted($query)
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->whereHas('contracts', fn ($q) => $q->where('status', DeveloperContract::STATUS_ACTIVE));
    }

    public function isPortalReady(): bool
    {
        return $this->portal_enabled && $this->accounts()->where('is_active', true)->exists();
    }
}

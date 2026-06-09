<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperContract extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_EXPIRED = 'expired';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'نشط',
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_EXPIRED => 'منتهي',
    ];

    protected $fillable = [
        'real_estate_developer_id',
        'contract_ref',
        'commission_percent',
        'exclusivity',
        'exclusivity_until',
        'contact_person',
        'contact_phone',
        'listing_terms',
        'notes',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_percent' => 'decimal:2',
            'exclusivity' => 'boolean',
            'exclusivity_until' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(RealEstateDeveloper::class, 'real_estate_developer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }
}

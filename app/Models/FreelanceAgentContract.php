<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelanceAgentContract extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_TERMINATED = 'terminated';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'contract_number',
        'national_id',
        'nationality',
        'address',
        'phone',
        'start_date',
        'end_date',
        'status',
        'quarterly_target_amount',
        'quarterly_target_deals',
        'company_signatory_name',
        'company_signatory_title',
        'signed_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'signed_at' => 'datetime',
            'quarterly_target_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public static function activeForUser(int $userId): ?self
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            })
            ->latest('start_date')
            ->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleCommissionSplit extends Model
{
    protected $fillable = [
        'sale_id',
        'user_id',
        'agent_role',
        'percent_of_company',
        'amount',
        'payout_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'percent_of_company' => 'decimal:2',
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

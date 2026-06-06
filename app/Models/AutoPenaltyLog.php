<?php

namespace App\Models;

use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompPayrollPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoPenaltyLog extends Model
{
    protected $fillable = [
        'rule_id', 'user_id', 'source_type', 'source_key', 'amount', 'reason',
        'adjustment_id', 'period_id', 'metadata', 'applied_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'applied_at' => 'datetime',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AutoPenaltyRule::class, 'rule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(CompAdjustment::class, 'adjustment_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(CompPayrollPeriod::class, 'period_id');
    }
}

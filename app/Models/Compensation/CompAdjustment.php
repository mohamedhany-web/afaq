<?php

namespace App\Models\Compensation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompAdjustment extends Model
{
    protected $table = 'comp_adjustments';

    protected $fillable = [
        'type', 'user_id', 'period_id', 'rule_id', 'amount', 'reason', 'status',
        'requested_by', 'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(CompPayrollPeriod::class, 'period_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

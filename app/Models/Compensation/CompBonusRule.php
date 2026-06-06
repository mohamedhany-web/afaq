<?php

namespace App\Models\Compensation;

use Illuminate\Database\Eloquent\Model;

class CompBonusRule extends Model
{
    protected $table = 'comp_bonus_rules';

    protected $fillable = [
        'name', 'code', 'amount_type', 'amount', 'conditions', 'target_role', 'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];
}

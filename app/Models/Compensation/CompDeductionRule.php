<?php

namespace App\Models\Compensation;

use Illuminate\Database\Eloquent\Model;

class CompDeductionRule extends Model
{
    protected $table = 'comp_deduction_rules';

    protected $fillable = [
        'name', 'code', 'amount_type', 'amount', 'requires_approval', 'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];
}

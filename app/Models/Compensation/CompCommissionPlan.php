<?php

namespace App\Models\Compensation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompCommissionPlan extends Model
{
    protected $table = 'comp_commission_plans';

    protected $fillable = ['name', 'model', 'config', 'is_active', 'description'];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(CompEmployeeProfile::class, 'commission_plan_id');
    }
}

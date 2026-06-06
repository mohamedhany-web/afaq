<?php

namespace App\Models\Compensation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompEmployeeProfile extends Model
{
    protected $table = 'comp_employee_profiles';

    protected $fillable = [
        'user_id', 'base_salary', 'kpi_template_id', 'commission_plan_id',
        'effective_from', 'is_active', 'meta',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'effective_from' => 'date',
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kpiTemplate(): BelongsTo
    {
        return $this->belongsTo(CompKpiTemplate::class, 'kpi_template_id');
    }

    public function commissionPlan(): BelongsTo
    {
        return $this->belongsTo(CompCommissionPlan::class, 'commission_plan_id');
    }
}

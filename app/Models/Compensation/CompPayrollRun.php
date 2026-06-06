<?php

namespace App\Models\Compensation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompPayrollRun extends Model
{
    protected $table = 'comp_payroll_runs';

    protected $fillable = [
        'user_id', 'period_id', 'base_salary', 'commission_total', 'bonus_total',
        'deduction_total', 'kpi_score', 'kpi_level', 'team_score', 'net_pay',
        'status', 'calculated_at', 'approved_by', 'approved_at', 'breakdown',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'commission_total' => 'decimal:2',
        'bonus_total' => 'decimal:2',
        'deduction_total' => 'decimal:2',
        'kpi_score' => 'decimal:2',
        'team_score' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'breakdown' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(CompPayrollPeriod::class, 'period_id');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(CompPayrollLineItem::class, 'payroll_run_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

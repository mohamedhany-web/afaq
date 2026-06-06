<?php

namespace App\Models\Compensation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompPayrollLineItem extends Model
{
    protected $table = 'comp_payroll_line_items';

    protected $fillable = [
        'payroll_run_id', 'category', 'label', 'amount',
        'reference_type', 'reference_id', 'approval_status',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(CompPayrollRun::class, 'payroll_run_id');
    }
}

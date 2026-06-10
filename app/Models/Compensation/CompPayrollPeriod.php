<?php

namespace App\Models\Compensation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompPayrollPeriod extends Model
{
    protected $table = 'comp_payroll_periods';

    protected $fillable = ['year', 'month', 'starts_at', 'ends_at', 'status'];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function runs(): HasMany
    {
        return $this->hasMany(CompPayrollRun::class, 'period_id');
    }

    public function getLabelAttribute(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }
}

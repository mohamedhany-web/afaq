<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitPaymentPlan extends Model
{
    public const TYPE_CASH = 'cash';
    public const TYPE_INSTALLMENT = 'installment';

    protected $fillable = [
        'project_unit_id', 'plan_type', 'down_percent', 'years',
        'installment_per_m2', 'down_payment_amount', 'notes',
    ];

    protected $casts = [
        'down_percent' => 'decimal:2',
        'years' => 'integer',
        'installment_per_m2' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }

    public function planTypeLabel(): string
    {
        return config('project_units.plan_types.' . $this->plan_type, $this->plan_type);
    }
}

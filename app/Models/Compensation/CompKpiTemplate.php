<?php

namespace App\Models\Compensation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompKpiTemplate extends Model
{
    protected $table = 'comp_kpi_templates';

    protected $fillable = [
        'name', 'description', 'target_role', 'evaluation_period', 'is_active', 'created_by',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function items(): HasMany
    {
        return $this->hasMany(CompKpiItem::class, 'template_id')->orderBy('sort_order');
    }

    public function employeeProfiles(): HasMany
    {
        return $this->hasMany(CompEmployeeProfile::class, 'kpi_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function weightTotal(): float
    {
        return (float) $this->items()->sum('weight');
    }
}

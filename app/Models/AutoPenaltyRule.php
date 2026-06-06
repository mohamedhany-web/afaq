<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutoPenaltyRule extends Model
{
    protected $fillable = [
        'name', 'description', 'department_code', 'source_type', 'report_period_type',
        'amount', 'applies_to', 'grace_hours', 'is_active', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'grace_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AutoPenaltyLog::class, 'rule_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function departmentLabel(): string
    {
        if (!$this->department_code) {
            return 'كل الأقسام';
        }

        return config('auto_penalties.departments.' . $this->department_code, $this->department_code);
    }

    public function sourceLabel(): string
    {
        return config('auto_penalties.source_types.' . $this->source_type, $this->source_type);
    }

    public function appliesToLabel(): string
    {
        return config('auto_penalties.applies_to.' . $this->applies_to, $this->applies_to);
    }
}

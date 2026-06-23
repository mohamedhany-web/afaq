<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectUnit extends Model
{
    public const USE_RESIDENTIAL = 'residential';
    public const USE_COMMERCIAL = 'commercial';
    public const USE_ADMINISTRATIVE = 'administrative';
    public const USE_MEDICAL = 'medical';

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_SOLD = 'sold';

    protected $fillable = [
        'project_id', 'building_floor_id', 'code', 'use_type', 'direction',
        'floor_number', 'floor_label', 'apartment_number',
        'area_m2',
        'price_cash', 'price_installment', 'unit_price_total', 'status',
        'created_by',
        'mesh_x', 'mesh_y', 'mesh_z', 'mesh_w', 'mesh_h', 'mesh_d', 'meta',
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
        'price_cash' => 'decimal:2',
        'price_installment' => 'decimal:2',
        'unit_price_total' => 'decimal:2',
        'mesh_x' => 'decimal:2',
        'mesh_y' => 'decimal:2',
        'mesh_z' => 'decimal:2',
        'mesh_w' => 'decimal:2',
        'mesh_h' => 'decimal:2',
        'mesh_d' => 'decimal:2',
        'meta' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(BuildingFloor::class, 'building_floor_id');
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(UnitPaymentPlan::class, 'project_unit_id');
    }

    public function useTypeLabel(): string
    {
        return config('project_units.use_types.' . $this->use_type, $this->use_type);
    }

    public function directionLabel(): ?string
    {
        if (! $this->direction) {
            return null;
        }

        return config('project_inventory.directions.' . $this->direction, $this->direction);
    }

    public function statusLabel(): string
    {
        return config('project_units.statuses.' . $this->status, $this->status);
    }

    public function displayCode(): string
    {
        return $this->apartment_number ?: $this->code;
    }

    public function meshColor(): string
    {
        if ($this->status === self::STATUS_SOLD) {
            return config('project_units.status_colors.sold');
        }
        if ($this->status === self::STATUS_RESERVED) {
            return config('project_units.status_colors.reserved');
        }

        return config('project_units.use_colors.' . $this->use_type, '#94a3b8');
    }
}

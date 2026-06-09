<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingFloor extends Model
{
    protected $fillable = [
        'project_id', 'level', 'label', 'height_m', 'use_mix', 'sort_order',
    ];

    protected $casts = [
        'level' => 'integer',
        'height_m' => 'decimal:2',
        'use_mix' => 'array',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProjectUnit::class, 'building_floor_id')->orderBy('code');
    }
}

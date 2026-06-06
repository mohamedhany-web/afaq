<?php

namespace App\Models\Compensation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompKpiItem extends Model
{
    protected $table = 'comp_kpi_items';

    protected $fillable = [
        'template_id', 'slug', 'name', 'description', 'weight', 'target_value', 'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'target_value' => 'decimal:2',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CompKpiTemplate::class, 'template_id');
    }
}

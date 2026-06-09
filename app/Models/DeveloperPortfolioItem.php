<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperPortfolioItem extends Model
{
    protected $fillable = [
        'real_estate_developer_id',
        'title',
        'description',
        'location',
        'city',
        'project_type',
        'year',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(RealEstateDeveloper::class, 'real_estate_developer_id');
    }
}

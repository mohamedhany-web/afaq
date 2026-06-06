<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMapPin extends Model
{
    public const TYPES = [
        'project' => 'موقع المشروع',
        'unit' => 'وحدة',
        'landmark' => 'علامة مرجعية',
        'entrance' => 'مدخل',
    ];

    protected $fillable = [
        'project_id',
        'title',
        'pin_type',
        'latitude',
        'longitude',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->pin_type] ?? $this->pin_type;
    }
}

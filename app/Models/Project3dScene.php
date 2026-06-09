<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project3dScene extends Model
{
    protected $table = 'project_3d_scenes';

    protected $fillable = [
        'project_id', 'version', 'camera_config', 'scene_config', 'generated_at',
    ];

    protected $casts = [
        'version' => 'integer',
        'camera_config' => 'array',
        'scene_config' => 'array',
        'generated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

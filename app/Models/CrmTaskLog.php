<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTaskLog extends Model
{
    protected $fillable = [
        'task_id', 'user_id', 'action', 'old_status', 'new_status', 'notes', 'meta',
    ];

    protected $casts = ['meta' => 'array'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(CrmTask::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

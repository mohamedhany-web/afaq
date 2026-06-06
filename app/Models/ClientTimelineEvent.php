<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClientTimelineEvent extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'department',
        'event_type',
        'title',
        'description',
        'related_type',
        'related_id',
        'meta',
        'occurred_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function departmentLabel(): string
    {
        return config('crm_intelligence.departments')[$this->department] ?? $this->department;
    }

    public function eventTypeLabel(): string
    {
        return config('crm_intelligence.timeline_event_types')[$this->event_type] ?? $this->event_type;
    }
}

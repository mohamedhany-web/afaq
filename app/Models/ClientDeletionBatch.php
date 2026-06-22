<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDeletionBatch extends Model
{
    protected $fillable = [
        'user_id',
        'clients_count',
        'delete_reason',
        'clients_snapshot',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'clients_snapshot' => 'array',
        'clients_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isBulk(): bool
    {
        return $this->clients_count > 1;
    }
}

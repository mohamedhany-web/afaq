<?php

namespace App\Models\Compensation;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompAuditLog extends Model
{
    protected $table = 'comp_audit_logs';

    protected $fillable = [
        'actor_id', 'action', 'entity_type', 'entity_id',
        'old_values', 'new_values', 'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

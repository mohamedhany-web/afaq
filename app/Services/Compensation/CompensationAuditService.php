<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompAuditLog;
use Illuminate\Http\Request;

class CompensationAuditService
{
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $old = null,
        ?array $new = null,
        ?int $actorId = null,
    ): CompAuditLog {
        return CompAuditLog::create([
            'actor_id' => $actorId ?? auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request() instanceof Request ? request()->ip() : null,
        ]);
    }
}

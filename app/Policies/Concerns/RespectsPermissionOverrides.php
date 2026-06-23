<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait RespectsPermissionOverrides
{
    protected function adminUnlessDenied(User $user, string $permission): bool
    {
        return $user->adminBypassUnlessDenied($permission);
    }
}

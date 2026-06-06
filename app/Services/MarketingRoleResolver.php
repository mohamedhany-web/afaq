<?php

namespace App\Services;

use App\Models\User;

class MarketingRoleResolver
{
    public const WORKSPACE_ADMIN = 'admin';
    public const WORKSPACE_MANAGER = 'manager';
    public const WORKSPACE_REP = 'rep';

    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function isAdmin(): bool
    {
        return $this->user->hasRole(['super_admin', 'admin']);
    }

    public function isManager(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        return $this->user->isMarketingManager();
    }

    public function isRep(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        return $this->user->usesMarketingWorkspace() && !$this->isManager();
    }

    public function workspace(): string
    {
        if ($this->isAdmin()) {
            return self::WORKSPACE_ADMIN;
        }

        if ($this->isManager()) {
            return self::WORKSPACE_MANAGER;
        }

        return self::WORKSPACE_REP;
    }
}

<?php

namespace App\Services;

use App\Models\User;

class OperationsRoleResolver
{
    public const WORKSPACE_ADMIN = 'admin';
    public const WORKSPACE_MANAGER = 'manager';

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

        return $this->user->isOperationsManager();
    }

    public function workspace(): string
    {
        if ($this->isAdmin()) {
            return self::WORKSPACE_ADMIN;
        }

        return self::WORKSPACE_MANAGER;
    }
}

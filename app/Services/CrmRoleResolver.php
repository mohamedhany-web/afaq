<?php

namespace App\Services;

use App\Models\User;

class CrmRoleResolver
{
    public const WORKSPACE_ADMIN = 'admin';
    public const WORKSPACE_MANAGER = 'manager';
    public const WORKSPACE_TEAM_LEADER = 'team_leader';
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

    /** مدير قسم المبيعات — عدة فرق */
    public function isDepartmentManager(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        return $this->user->isSalesDepartmentManager();
    }

    /** قائد فريق — فريق واحد */
    public function isTeamLeader(): bool
    {
        if ($this->isAdmin() || $this->isDepartmentManager()) {
            return false;
        }

        return $this->user->isSalesTeamLeader();
    }

    public function isManager(): bool
    {
        return $this->isDepartmentManager() || $this->isTeamLeader();
    }

    public function isRep(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        if ($this->user->isSalesAgentOnly()) {
            return true;
        }

        return $this->user->hasRole(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            && !$this->isManager();
    }

    /** admin | manager | team_leader | rep */
    public function workspace(): string
    {
        if ($this->isAdmin()) {
            return self::WORKSPACE_ADMIN;
        }

        if ($this->isDepartmentManager()) {
            return self::WORKSPACE_MANAGER;
        }

        if ($this->isTeamLeader()) {
            return self::WORKSPACE_TEAM_LEADER;
        }

        return self::WORKSPACE_REP;
    }

    public function canCreateDailySalesReport(): bool
    {
        return $this->isRep();
    }
}

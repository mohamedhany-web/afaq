<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CrmEmployeeService
{
    public const ROLE_MANAGER = 'manager';
    public const ROLE_TEAM_LEADER = 'team_leader';
    public const ROLE_EMPLOYEE = 'employee';

    /** @var array<string, string> */
    public const ROLE_LABELS = [
        self::ROLE_MANAGER => 'مدير مبيعات',
        self::ROLE_TEAM_LEADER => 'قائد فريق مبيعات',
        self::ROLE_EMPLOYEE => 'موظف مبيعات',
    ];

    /** مدير قسم المبيعات — يدير جميع الفرق */
    public const LEGACY_DEPARTMENT_HEAD_ROLES = ['manager', 'sales_manager'];

    /** قائد فريق — يدير فريقاً واحداً يومياً */
    public const LEGACY_TEAM_LEADER_ROLES = ['sales_team_leader'];

    /** @deprecated استخدم LEGACY_DEPARTMENT_HEAD_ROLES أو isManagerScope */
    public const LEGACY_MANAGER_ROLES = ['manager', 'sales_manager', 'sales_team_leader'];

    public const LEGACY_EMPLOYEE_ROLES = ['employee', 'sales_agent', 'sales_rep'];

    public static function salesDepartment(): Department
    {
        return Department::firstOrCreate(
            ['code' => 'SAL'],
            [
                'name' => 'المبيعات',
                'description' => 'قسم المبيعات العقارية',
                'is_active' => true,
            ]
        );
    }

    public static function positionForRole(string $role): string
    {
        return match ($role) {
            self::ROLE_MANAGER => 'مدير مبيعات',
            self::ROLE_TEAM_LEADER => 'قائد فريق مبيعات',
            default => 'موظف مبيعات',
        };
    }

    public static function assignSalesRole(User $user, string $role): void
    {
        if (!in_array($role, [self::ROLE_MANAGER, self::ROLE_TEAM_LEADER, self::ROLE_EMPLOYEE], true)) {
            $role = self::ROLE_EMPLOYEE;
        }

        $strip = array_merge(
            self::LEGACY_DEPARTMENT_HEAD_ROLES,
            self::LEGACY_TEAM_LEADER_ROLES,
            self::LEGACY_EMPLOYEE_ROLES,
            MarketingEmployeeService::LEGACY_MANAGER_ROLES,
            MarketingEmployeeService::LEGACY_REP_ROLES,
            OperationsEmployeeService::LEGACY_MANAGER_ROLES,
        );

        foreach (array_unique($strip) as $name) {
            if ($user->hasRole($name)) {
                $user->removeRole($name);
            }
        }

        if ($role === self::ROLE_MANAGER) {
            $user->assignRole(['manager', 'sales_manager']);
        } elseif ($role === self::ROLE_TEAM_LEADER) {
            Role::firstOrCreate(['name' => 'sales_team_leader', 'guard_name' => 'web']);
            $user->assignRole('sales_team_leader');
        } else {
            $user->assignRole(['employee', 'sales_agent']);
        }
    }

    public static function currentSalesRole(User $user): string
    {
        if ($user->hasAnyRole(self::LEGACY_DEPARTMENT_HEAD_ROLES)) {
            return self::ROLE_MANAGER;
        }

        if ($user->hasAnyRole(self::LEGACY_TEAM_LEADER_ROLES)) {
            return self::ROLE_TEAM_LEADER;
        }

        if ($user->hasAnyRole(self::LEGACY_EMPLOYEE_ROLES)) {
            return self::ROLE_EMPLOYEE;
        }

        return self::ROLE_EMPLOYEE;
    }

    public static function isDepartmentHead(User $user): bool
    {
        return $user->hasAnyRole(self::LEGACY_DEPARTMENT_HEAD_ROLES);
    }

    public static function isTeamLeader(User $user): bool
    {
        return $user->hasAnyRole(self::LEGACY_TEAM_LEADER_ROLES);
    }
}


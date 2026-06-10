<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;

class OperationsEmployeeService
{
    public const ROLE_MANAGER = 'operation_manager';

    public const LEGACY_MANAGER_ROLES = ['operation_manager'];

    public const ROLE_LABELS = [
        self::ROLE_MANAGER => 'مدير عمليات',
    ];

    public static function operationsDepartment(): Department
    {
        return Department::firstOrCreate(
            ['code' => 'OPS'],
            [
                'name' => 'العمليات',
                'description' => 'قسم العمليات والتشغيل والمشاريع',
                'is_active' => true,
            ]
        );
    }

    public static function positionForRole(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? 'مدير عمليات';
    }

    public static function assignOperationsRole(User $user, string $role = self::ROLE_MANAGER): void
    {
        $role = self::ROLE_MANAGER;

        $strip = array_merge(
            self::LEGACY_MANAGER_ROLES,
            CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
            CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
            MarketingEmployeeService::LEGACY_MANAGER_ROLES,
            MarketingEmployeeService::LEGACY_REP_ROLES,
            ['manager', 'employee', 'project_manager'],
        );

        foreach (array_unique($strip) as $name) {
            if ($user->hasRole($name)) {
                $user->removeRole($name);
            }
        }

        $user->assignRole($role);
    }

    public static function currentOperationsRole(User $user): string
    {
        if ($user->hasAnyRole(self::LEGACY_MANAGER_ROLES)) {
            return self::ROLE_MANAGER;
        }

        return self::ROLE_MANAGER;
    }
}

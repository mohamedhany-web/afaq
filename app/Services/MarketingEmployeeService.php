<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MarketingEmployeeService
{
    public const ROLE_MANAGER = 'marketing_manager';
    public const ROLE_REP = 'marketing_rep';

    public const LEGACY_MANAGER_ROLES = ['marketing_manager'];
    public const LEGACY_REP_ROLES = ['marketing_rep'];

    public const ROLE_LABELS = [
        self::ROLE_MANAGER => 'مدير تسويق',
        self::ROLE_REP => 'موظف تسويق',
    ];

    public static function marketingDepartment(): Department
    {
        return Department::firstOrCreate(
            ['code' => 'MKT'],
            [
                'name' => 'التسويق',
                'description' => 'قسم التسويق والحملات والمحتوى',
                'is_active' => true,
            ]
        );
    }

    public static function positionForRole(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? 'موظف تسويق';
    }

    public static function assignMarketingRole(User $user, string $role): void
    {
        if (!in_array($role, [self::ROLE_MANAGER, self::ROLE_REP], true)) {
            $role = self::ROLE_REP;
        }

        $strip = array_merge(
            self::LEGACY_MANAGER_ROLES,
            self::LEGACY_REP_ROLES,
            CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
            CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
            OperationsEmployeeService::LEGACY_MANAGER_ROLES,
            ['manager', 'employee'],
        );

        foreach (array_unique($strip) as $name) {
            if ($user->hasRole($name)) {
                $user->removeRole($name);
            }
        }

        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);

        $user->assignRole($role);
    }

    public static function currentMarketingRole(User $user): string
    {
        if ($user->hasAnyRole(self::LEGACY_MANAGER_ROLES)) {
            return self::ROLE_MANAGER;
        }

        if ($user->hasAnyRole(self::LEGACY_REP_ROLES)) {
            return self::ROLE_REP;
        }

        return self::ROLE_REP;
    }
}

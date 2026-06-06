<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;

class CrmEmployeeService
{
    public const ROLE_MANAGER = 'manager';
    public const ROLE_EMPLOYEE = 'employee';

    /** @var array<string, string> */
    public const ROLE_LABELS = [
        self::ROLE_MANAGER => 'مدير مبيعات',
        self::ROLE_EMPLOYEE => 'موظف مبيعات',
    ];

  /** Legacy role names kept for backward compatibility */
    public const LEGACY_MANAGER_ROLES = ['manager', 'sales_manager'];
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
            default => 'موظف مبيعات',
        };
    }

    public static function assignSalesRole(User $user, string $role): void
    {
        if (!in_array($role, [self::ROLE_MANAGER, self::ROLE_EMPLOYEE], true)) {
            $role = self::ROLE_EMPLOYEE;
        }

        $strip = array_merge(
            self::LEGACY_MANAGER_ROLES,
            self::LEGACY_EMPLOYEE_ROLES,
            MarketingEmployeeService::LEGACY_MANAGER_ROLES,
            MarketingEmployeeService::LEGACY_REP_ROLES,
        );

        foreach (array_unique($strip) as $name) {
            if ($user->hasRole($name)) {
                $user->removeRole($name);
            }
        }

        $user->assignRole($role);

        // Aliases for existing CRM checks
        if ($role === self::ROLE_MANAGER) {
            $user->assignRole('sales_manager');
        } else {
            $user->assignRole('sales_agent');
        }
    }

    public static function currentSalesRole(User $user): string
    {
        if ($user->hasAnyRole(self::LEGACY_MANAGER_ROLES)) {
            return self::ROLE_MANAGER;
        }

        if ($user->hasAnyRole(self::LEGACY_EMPLOYEE_ROLES)) {
            return self::ROLE_EMPLOYEE;
        }

        return self::ROLE_EMPLOYEE;
    }
}

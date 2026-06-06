<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeRoleService
{
    public const MODULE_HR = 'hr';
    public const MODULE_CRM = 'crm';
    public const MODULE_MARKETING = 'marketing';

    public static function moduleForEmployee(Employee $employee): string
    {
        $code = $employee->department?->code;

        return match ($code) {
            'MKT' => self::MODULE_MARKETING,
            'SAL' => self::MODULE_CRM,
            default => self::MODULE_HR,
        };
    }

    public static function isMarketingEmployee(Employee $employee): bool
    {
        return self::moduleForEmployee($employee) === self::MODULE_MARKETING;
    }

    public static function isSalesEmployee(Employee $employee): bool
    {
        return self::moduleForEmployee($employee) === self::MODULE_CRM;
    }

    /** @return array{key: string, label: string, module: string} */
    public static function resolve(Employee $employee): array
    {
        if (!$employee->user) {
            return [
                'key' => 'employee',
                'label' => 'موظف',
                'module' => self::moduleForEmployee($employee),
            ];
        }

        if (self::isMarketingEmployee($employee)) {
            $key = MarketingEmployeeService::currentMarketingRole($employee->user);

            return [
                'key' => $key,
                'label' => MarketingEmployeeService::ROLE_LABELS[$key] ?? 'موظف تسويق',
                'module' => self::MODULE_MARKETING,
            ];
        }

        if (self::isSalesEmployee($employee)) {
            $key = CrmEmployeeService::currentSalesRole($employee->user);

            return [
                'key' => $key,
                'label' => CrmEmployeeService::ROLE_LABELS[$key] ?? 'موظف مبيعات',
                'module' => self::MODULE_CRM,
            ];
        }

        $roleName = CrmRoleCatalogService::resolveUserDisplayRole($employee->user);

        return [
            'key' => $roleName ?? 'employee',
            'label' => $roleName ? CrmRoleCatalogService::roleLabel($roleName) : 'موظف',
            'module' => self::MODULE_HR,
        ];
    }

    public static function assignRoleForDepartment(User $user, string $departmentCode, string $roleKey): void
    {
        if ($departmentCode === 'MKT') {
            MarketingEmployeeService::assignMarketingRole($user, $roleKey);
        } elseif ($departmentCode === 'SAL') {
            CrmEmployeeService::assignSalesRole($user, $roleKey);
        }
    }

    public static function roleLabelsForDepartment(?string $departmentCode): array
    {
        return match ($departmentCode) {
            'MKT' => MarketingEmployeeService::ROLE_LABELS,
            'SAL' => CrmEmployeeService::ROLE_LABELS,
            default => CrmEmployeeService::ROLE_LABELS,
        };
    }

    public static function currentRoleKey(Employee $employee): string
    {
        return self::resolve($employee)['key'];
    }
}

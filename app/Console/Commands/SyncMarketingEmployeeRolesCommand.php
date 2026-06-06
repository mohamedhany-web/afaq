<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\EmployeeRoleService;
use App\Services\MarketingEmployeeService;
use Illuminate\Console\Command;

class SyncMarketingEmployeeRolesCommand extends Command
{
    protected $signature = 'marketing:sync-employee-roles';

    protected $description = 'إزالة أدوار المبيعات من موظفي قسم التسويق وتصحيح الأدوار';

    public function handle(): int
    {
        $dept = MarketingEmployeeService::marketingDepartment();

        Employee::query()
            ->where('department_id', $dept->id)
            ->with('user.roles')
            ->each(function (Employee $employee) {
                if (!$employee->user) {
                    return;
                }

                $role = EmployeeRoleService::currentRoleKey($employee);
                MarketingEmployeeService::assignMarketingRole($employee->user, $role);

                $this->line(sprintf(
                    '#%d %s — %s [%s]',
                    $employee->id,
                    trim($employee->first_name . ' ' . $employee->last_name),
                    $role,
                    $employee->user->roles->pluck('name')->join(', ')
                ));
            });

        return self::SUCCESS;
    }
}

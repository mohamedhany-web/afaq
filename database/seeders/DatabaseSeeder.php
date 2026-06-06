<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            UserEmployeeSeeder::class,
            ClientSeeder::class,
            ChartOfAccountsSeeder::class,
            UserPermissionSeeder::class,
            SystemSettingsSeeder::class,
            ProjectSeeder::class,
            RealEstateCrmSeeder::class,
            CompensationModuleSeeder::class,
            AutoPenaltyRuleSeeder::class,
            AttendanceSeeder::class,
            LeaveSeeder::class,
            SalarySeeder::class,
        ]);
    }
}
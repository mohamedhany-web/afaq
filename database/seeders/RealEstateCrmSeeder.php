<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RealEstateCrmSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $crmPermissions = [
            'access-crm',
            'view-team-sales',
            'manage-sales-teams',
            'view-crm-clients',
            'view-crm-pipeline',
        ];

        foreach ($crmPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // الأدوار الثلاثة فقط للمبيعات العقارية
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'access-crm', 'view-team-sales', 'manage-sales-teams', 'view-crm-clients', 'view-crm-pipeline',
            'view-sales', 'create-sales', 'edit-sales',
            'view-clients', 'create-clients', 'edit-clients',
            'view-own-projects', 'view-all-projects',
            'view-dashboard', 'view-employees', 'create-employees', 'edit-employees',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employee->syncPermissions([
            'access-crm', 'view-crm-clients', 'view-crm-pipeline',
            'view-sales', 'create-sales', 'edit-sales',
            'view-clients', 'create-clients', 'edit-clients',
            'view-own-projects', 'view-dashboard',
        ]);

        // أسماء قديمة — نفس الصلاحيات للتوافق
        $salesManager = Role::firstOrCreate(['name' => 'sales_manager', 'guard_name' => 'web']);
        $salesManager->syncPermissions($manager->permissions->pluck('name')->all());

        $salesAgent = Role::firstOrCreate(['name' => 'sales_agent', 'guard_name' => 'web']);
        $salesAgent->syncPermissions($employee->permissions->pluck('name')->all());

        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($crmPermissions);
            $superAdmin->givePermissionTo('manage-sales-teams');
        }
    }
}

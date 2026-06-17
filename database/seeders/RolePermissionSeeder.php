<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // إعادة تعيين cache الصلاحيات
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions from السجل المركزي
        $permissions = config('permissions.registry', []);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ========== الأدوار الوظيفية ==========
        
        // 1. مدير النظام (Super Admin)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. مدير عام (Admin) — CRM عقاري
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view-users', 'create-users', 'edit-users',
            'view-employees', 'create-employees', 'edit-employees',
            'view-all-projects', 'create-projects', 'edit-projects', 'delete-projects', 'approve-project-changes',
            'view-clients', 'create-clients', 'edit-clients', 'delete-clients',
            'view-developers', 'manage-developers',
            'view-sales', 'create-sales', 'edit-sales', 'delete-sales',
            'view-finance', 'create-finance', 'edit-finance',
            'view-attendance', 'create-attendance', 'edit-attendance',
            'view-leaves', 'create-leaves', 'edit-leaves', 'approve-leaves',
            'view-salaries', 'create-salaries', 'edit-salaries', 'approve-salaries',
            'view-training', 'create-training', 'edit-training',
            'view-meetings', 'create-meetings', 'edit-meetings',
            'view-assets',
            'view-invoices', 'create-invoices', 'edit-invoices',
            'view-contracts', 'create-contracts', 'edit-contracts',
            'view-tickets', 'create-tickets', 'edit-tickets',
            'view-departments', 'create-departments', 'edit-departments',
            'view-reports', 'generate-reports', 'export-reports',
            'view-dashboard', 'view-analytics',
            'view-settings', 'manage-roles',
            'view-marketing', 'create-marketing', 'edit-marketing', 'delete-marketing', 'manage-marketing',
        ]);

        // 3. مدير مشاريع (Project Manager)
        $projectManager = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'web']);
        $projectManager->syncPermissions([
            'view-employees',
            'view-all-projects', 'create-projects', 'edit-projects',
            'view-all-tasks', 'create-tasks', 'edit-tasks',
            'view-clients', 'edit-clients',
            'view-bugs', 'create-bugs', 'edit-bugs',
            'view-qa', 'create-qa', 'edit-qa',
            'view-reports', 'generate-reports',
            'view-dashboard',
            'view-training', 'create-training', 'edit-training',
            'view-meetings', 'create-meetings', 'edit-meetings'
        ]);

        // 4. موظف (Employee)
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employee->syncPermissions([
            'view-own-projects', 'view-own-tasks', 'edit-tasks',
            'view-clients',
            'view-bugs', 'create-bugs',
            'view-leaves', 'create-leaves',
            'view-dashboard',
            'view-training', 'view-meetings'
        ]);

        // 5. الموارد البشرية (HR)
        $hr = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $hr->syncPermissions([
            'view-users', 'create-users', 'edit-users',
            'view-employees', 'create-employees', 'edit-employees',
            'view-attendance', 'create-attendance', 'edit-attendance',
            'view-leaves', 'create-leaves', 'edit-leaves', 'approve-leaves',
            'view-salaries', 'create-salaries', 'edit-salaries',
            'view-reports', 'generate-reports',
            'view-dashboard',
            'view-training', 'create-training', 'edit-training', 'delete-training',
            'view-meetings', 'create-meetings', 'edit-meetings', 'delete-meetings',
            'view-assets'
        ]);

        // 6. محاسب (Accountant)
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'view-finance', 'create-finance', 'edit-finance', 'delete-finance', 'approve-expenses',
            'view-invoices', 'create-invoices', 'edit-invoices',
            'view-salaries',
            'view-contracts',
            'view-reports', 'generate-reports', 'export-reports',
            'view-dashboard', 'view-analytics'
        ]);

        // 7. مدير مبيعات (CRM)
        $salesManager = Role::firstOrCreate(['name' => 'sales_manager', 'guard_name' => 'web']);
        $salesManagerPerms = [
            'view-clients', 'create-clients', 'edit-clients',
            'view-sales', 'create-sales', 'edit-sales',
            'view-all-projects', 'create-projects', 'edit-projects', 'delete-projects',
            'view-employees',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves', 'approve-leaves',
            'view-reports', 'generate-reports', 'export-reports',
            'view-dashboard', 'view-analytics',
            'view-training', 'view-meetings',
        ];
        $salesManager->syncPermissions($salesManagerPerms);

        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web'])
            ->syncPermissions($salesManagerPerms);

        $teamLeaderPerms = [
            'view-clients', 'create-clients', 'edit-clients',
            'view-sales', 'create-sales', 'edit-sales',
            'view-all-projects', 'create-projects', 'edit-projects',
            'view-employees',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves', 'approve-leaves',
            'view-reports', 'generate-reports',
            'view-dashboard', 'view-analytics',
            'view-training', 'view-meetings',
        ];
        Role::firstOrCreate(['name' => 'sales_team_leader', 'guard_name' => 'web'])
            ->syncPermissions($teamLeaderPerms);

        // 8. موظف مبيعات (CRM)
        $salesRep = Role::firstOrCreate(['name' => 'sales_rep', 'guard_name' => 'web']);
        $salesRep->syncPermissions([
            'view-clients', 'create-clients', 'edit-clients',
            'view-sales', 'create-sales', 'edit-sales',
            'view-all-projects', 'create-projects', 'edit-projects', 'delete-projects',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves',
            'view-dashboard',
            'view-training', 'view-meetings',
        ]);

        Role::firstOrCreate(['name' => 'sales_agent', 'guard_name' => 'web'])
            ->syncPermissions($salesRep->permissions->pluck('name')->all());

        $marketingManagerPerms = [
            'view-marketing', 'create-marketing', 'edit-marketing', 'delete-marketing', 'manage-marketing',
            'view-clients', 'create-clients', 'edit-clients',
            'view-all-projects',
            'view-employees',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves', 'approve-leaves',
            'view-reports', 'generate-reports', 'export-reports',
            'view-dashboard', 'view-analytics',
            'view-training', 'view-meetings',
        ];

        Role::firstOrCreate(['name' => 'marketing_manager', 'guard_name' => 'web'])
            ->syncPermissions($marketingManagerPerms);

        Role::firstOrCreate(['name' => 'marketing_rep', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-marketing', 'create-marketing', 'edit-marketing',
                'view-clients', 'create-clients',
                'view-all-projects',
                'view-dashboard',
                'view-attendance', 'create-attendance',
                'view-leaves', 'create-leaves',
                'view-training', 'view-meetings',
            ]);

        // 8. مدير عمليات (Operations)
        $operationManagerPerms = [
            'view-all-projects', 'create-projects', 'edit-projects', 'approve-project-changes',
            'view-all-tasks', 'create-tasks', 'edit-tasks',
            'view-developers', 'manage-developers',
            'view-clients',
            'view-employees',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves', 'approve-leaves',
            'view-reports', 'generate-reports', 'export-reports',
            'view-dashboard', 'view-analytics',
            'view-departments',
            'view-training', 'view-meetings',
        ];

        Role::firstOrCreate(['name' => 'operation_manager', 'guard_name' => 'web'])
            ->syncPermissions($operationManagerPerms);

        // 9. دعم فني (Support)
        $support = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $support->syncPermissions([
            'view-clients',
            'view-tickets', 'create-tickets', 'edit-tickets',
            'view-bugs', 'create-bugs',
            'view-dashboard'
        ]);

        // 9. عميل (Client) - بوابة العميل
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $client->syncPermissions([
            'view-client-portal',
            'view-client-projects',
            'view-client-invoices',
            'view-client-tickets',
            'create-client-tickets',
            // الرسائل/الإشعارات متاحة للجميع داخل النظام بالفعل
        ]);

        // 9. مطور (Developer)
        $developer = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $developer->syncPermissions([
            'view-own-projects', 'view-own-tasks', 'edit-tasks',
            'view-bugs', 'create-bugs', 'edit-bugs',
            'view-qa', 'create-qa', 'edit-qa',
            'view-dashboard',
            'view-training', 'create-training', 'edit-training',
            'view-meetings', 'create-meetings', 'edit-meetings'
        ]);

        // 10. مصمم (Designer)
        $designer = Role::firstOrCreate(['name' => 'designer', 'guard_name' => 'web']);
        $designer->syncPermissions([
            'view-own-projects', 'view-own-tasks', 'edit-tasks',
            'view-clients',
            'view-design', 'create-design', 'edit-design', 'manage-design',
            'view-dashboard',
            'view-training', 'create-training', 'edit-training',
            'view-meetings', 'create-meetings', 'edit-meetings'
        ]);
    }
}

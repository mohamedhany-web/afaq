<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = [
            'view-leaves',
            'create-leaves',
            'view-developers',
            'manage-developers',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $leaveRoles = [
            'sales_rep',
            'sales_agent',
            'sales_manager',
            'manager',
            'marketing_rep',
            'marketing_manager',
            'operation_manager',
            'employee',
        ];

        $operationManagerPerms = [
            'view-all-projects', 'create-projects', 'edit-projects', 'approve-project-changes',
            'view-all-tasks', 'create-tasks', 'edit-tasks',
            'view-developers', 'manage-developers',
            'view-clients', 'view-employees',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves', 'approve-leaves',
            'view-reports', 'generate-reports', 'export-reports',
            'view-dashboard', 'view-analytics',
            'view-departments', 'view-training', 'view-meetings',
        ];

        foreach ($leaveRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo(['view-leaves', 'create-leaves']);
            }
        }

        foreach (['admin', 'super_admin'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo(['view-developers', 'manage-developers']);
            }
        }

        $opsRole = Role::where('name', 'operation_manager')->where('guard_name', 'web')->first();
        if ($opsRole) {
            $opsRole->syncPermissions($operationManagerPerms);
        } else {
            Role::firstOrCreate(['name' => 'operation_manager', 'guard_name' => 'web'])
                ->syncPermissions($operationManagerPerms);
        }
    }

    public function down(): void
    {
        // لا إزالة تلقائية — قد تكون مُعدّة يدوياً
    }
};

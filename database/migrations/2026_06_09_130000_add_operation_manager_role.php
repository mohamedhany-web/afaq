<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perms = [
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

        foreach ($perms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'operation_manager', 'guard_name' => 'web'])
            ->syncPermissions($perms);
    }

    public function down(): void
    {
        // لا إزالة تلقائية
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        $marketingRepPerms = [
            'view-marketing', 'create-marketing', 'edit-marketing',
            'view-clients', 'create-clients',
            'view-all-projects',
            'view-dashboard',
            'view-attendance', 'create-attendance',
            'view-leaves', 'create-leaves',
            'view-training', 'view-meetings',
        ];

        foreach (array_unique(array_merge($marketingManagerPerms, $marketingRepPerms)) as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'marketing_manager', 'guard_name' => 'web'])
            ->syncPermissions($marketingManagerPerms);

        Role::firstOrCreate(['name' => 'marketing_rep', 'guard_name' => 'web'])
            ->syncPermissions($marketingRepPerms);
    }

    public function down(): void
    {
        // الأدوار قد تكون مُستخدمة — لا حذف تلقائي
    }
};

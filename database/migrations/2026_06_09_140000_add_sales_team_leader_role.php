<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        foreach ($teamLeaderPerms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'sales_team_leader', 'guard_name' => 'web'])
            ->syncPermissions($teamLeaderPerms);
    }

    public function down(): void
    {
        // لا إزالة تلقائية
    }
};

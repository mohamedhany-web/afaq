<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $projectPerms = [
            'view-all-projects',
            'create-projects',
            'edit-projects',
        ];

        foreach ($projectPerms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $salesRoles = ['sales_rep', 'sales_agent', 'sales_team_leader'];

        foreach ($salesRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($projectPerms);
            }
        }
    }

    public function down(): void
    {
        // لا إزالة تلقائية
    }
};

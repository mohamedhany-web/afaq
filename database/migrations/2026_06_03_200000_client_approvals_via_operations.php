<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'approve-client-changes', 'guard_name' => 'web']);

        $salesRoles = ['sales_manager', 'manager', 'sales_team_leader'];
        foreach ($salesRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role?->hasPermissionTo('approve-client-changes')) {
                $role->revokePermissionTo('approve-client-changes');
            }
        }

        foreach (['operation_manager', 'super_admin', 'admin'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo('approve-client-changes');
            }
        }
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $opsRole = Role::where('name', 'operation_manager')->where('guard_name', 'web')->first();
        if ($opsRole?->hasPermissionTo('approve-client-changes')) {
            $opsRole->revokePermissionTo('approve-client-changes');
        }

        foreach (['sales_manager', 'manager', 'sales_team_leader'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo('approve-client-changes');
            }
        }
    }
};

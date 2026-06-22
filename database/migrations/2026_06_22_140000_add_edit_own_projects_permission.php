<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'edit-own-projects', 'guard_name' => 'web']);

        $roles = [
            'project_manager',
            'employee',
            'department_manager',
        ];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role && $role->hasPermissionTo('view-own-projects')) {
                $role->givePermissionTo('edit-own-projects');
            }
        }
    }

    public function down(): void
    {
        Permission::where('name', 'edit-own-projects')->where('guard_name', 'web')->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};

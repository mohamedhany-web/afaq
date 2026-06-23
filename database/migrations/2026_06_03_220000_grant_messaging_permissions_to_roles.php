<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $basic = [
            'view-messages', 'create-messages', 'send-messages',
            'reply-messages', 'mark-messages-important',
        ];

        $extended = array_merge($basic, [
            'edit-messages', 'delete-messages', 'view-all-messages',
            'send-announcements', 'send-group-messages',
        ]);

        foreach ($basic as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        foreach ($extended as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $internalRoles = Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', ['client'])
            ->get();

        foreach ($internalRoles as $role) {
            $role->givePermissionTo($basic);
        }

        foreach (['super_admin', 'admin', 'hr', 'sales_manager', 'manager', 'operation_manager'] as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($extended);
            }
        }
    }

    public function down(): void
    {
        // لا إزالة تلقائية — تخصيصات يدوية
    }
};

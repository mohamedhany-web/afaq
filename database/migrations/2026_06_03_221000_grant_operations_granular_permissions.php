<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $opsPermissions = [
            'access-operations',
            'import-clients', 'export-clients', 'bulk-update-clients', 'bulk-delete-clients',
            'view-client-deletion-log', 'manage-client-staff-notes',
            'distribute-leads', 'auto-distribute-leads',
            'view-follow-ups', 'manage-follow-ups',
            'view-inventory', 'export-inventory',
            'view-operations-reports', 'annotate-operations-reports',
            'review-attendance-absence', 'review-attendance-checkout', 'manage-exit-permits',
            'manage-project-units', 'view-crm-intelligence',
        ];

        foreach ($opsPermissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $role = Role::where('name', 'operation_manager')->where('guard_name', 'web')->first();
        if ($role) {
            $role->givePermissionTo($opsPermissions);
        }
    }

    public function down(): void
    {
        //
    }
};

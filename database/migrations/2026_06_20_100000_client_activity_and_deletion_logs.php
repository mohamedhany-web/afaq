<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('client_deletion_batches')) {
            Schema::create('client_deletion_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('clients_count')->default(1);
                $table->text('delete_reason');
                $table->json('clients_snapshot');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }

        $permission = Permission::firstOrCreate(
            ['name' => 'transfer-clients', 'guard_name' => 'web'],
        );

        $roles = ['super_admin', 'admin', 'operation_manager', 'sales_manager', 'sales_team_leader'];
        foreach ($roles as $roleName) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_deletion_batches');

        Permission::where('name', 'transfer-clients')->delete();
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('project_change_requests', 'request_reason')) {
            Schema::table('project_change_requests', function (Blueprint $table) {
                $table->text('request_reason')->nullable()->after('review_notes');
            });
        }

        Schema::create('client_change_requests', function (Blueprint $table) {
            $table->id();
            $table->string('action', 16);
            $table->string('status', 16)->default('pending');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('request_reason')->nullable();
            $table->string('summary')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['status', 'action']);
            $table->index(['client_id', 'status']);
        });

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'approve-client-changes', 'guard_name' => 'web']);

        $approverRoles = ['super_admin', 'admin', 'sales_manager', 'manager', 'sales_team_leader'];

        foreach ($approverRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo('approve-client-changes');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_change_requests');

        if (Schema::hasColumn('project_change_requests', 'request_reason')) {
            Schema::table('project_change_requests', function (Blueprint $table) {
                $table->dropColumn('request_reason');
            });
        }
    }
};

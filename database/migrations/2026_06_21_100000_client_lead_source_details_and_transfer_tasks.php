<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('clients', 'lead_source_details')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->json('lead_source_details')->nullable()->after('lead_source');
            });
        }

        Permission::firstOrCreate(
            ['name' => 'transfer-tasks', 'guard_name' => 'web'],
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'lead_source_details')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('lead_source_details');
            });
        }

        Permission::where('name', 'transfer-tasks')->delete();
    }
};

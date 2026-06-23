<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('project_manager_id')
                    ->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('project_units', function (Blueprint $table) {
            if (! Schema::hasColumn('project_units', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('project_id')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_units', function (Blueprint $table) {
            if (Schema::hasColumn('project_units', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};

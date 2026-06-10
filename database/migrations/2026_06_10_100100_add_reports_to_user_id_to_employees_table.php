<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'reports_to_user_id')) {
                $table->foreignId('reports_to_user_id')
                    ->nullable()
                    ->after('department_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'reports_to_user_id')) {
                $table->dropConstrainedForeignId('reports_to_user_id');
            }
        });
    }
};

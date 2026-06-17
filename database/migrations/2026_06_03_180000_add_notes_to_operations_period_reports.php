<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations_period_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('operations_period_reports', 'notes')) {
                $table->text('notes')->nullable()->after('next_period_plan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations_period_reports', function (Blueprint $table) {
            if (Schema::hasColumn('operations_period_reports', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clients', 'lead_stage')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('lead_stage', 32)->default('lead')->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'lead_stage')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('lead_stage');
            });
        }
    }
};

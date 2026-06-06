<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['stage', 'updated_at'], 'sales_stage_updated_index');
            $table->index('assigned_to', 'sales_assigned_to_index');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_stage_updated_index');
            $table->dropIndex('sales_assigned_to_index');
        });
    }
};

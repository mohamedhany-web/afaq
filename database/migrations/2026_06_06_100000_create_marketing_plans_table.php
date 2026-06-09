<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('status', 20)->default('draft');
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'year', 'month']);
            $table->index('manager_id');
        });

        Schema::table('marketing_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('marketing_activities', 'marketing_plan_id')) {
                $table->foreignId('marketing_plan_id')->nullable()->after('campaign_id')
                    ->constrained('marketing_plans')->nullOnDelete();
                $table->index('marketing_plan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketing_activities', function (Blueprint $table) {
            if (Schema::hasColumn('marketing_activities', 'marketing_plan_id')) {
                $table->dropConstrainedForeignId('marketing_plan_id');
            }
        });

        Schema::dropIfExists('marketing_plans');
    }
};

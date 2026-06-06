<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_period_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period_type', 20);
            $table->date('period_start');
            $table->date('period_end');

            $table->json('metrics')->nullable();
            $table->text('activities_summary')->nullable();
            $table->text('campaigns_progress')->nullable();
            $table->text('obstacles')->nullable();
            $table->text('support_required')->nullable();
            $table->text('next_period_plan')->nullable();
            $table->text('team_summary')->nullable();

            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_type', 'period_start']);
            $table->index(['period_type', 'period_start', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_period_reports');
    }
};

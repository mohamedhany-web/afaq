<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations_period_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('period_type', 20);
            $table->date('period_start');
            $table->date('period_end');

            $table->json('metrics')->nullable();
            $table->text('operations_summary')->nullable();
            $table->text('projects_progress')->nullable();
            $table->text('team_coordination')->nullable();
            $table->text('obstacles')->nullable();
            $table->text('support_required')->nullable();
            $table->text('next_period_plan')->nullable();
            $table->text('admin_notes')->nullable();

            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_type', 'period_start'], 'ops_reports_user_period_unique');
            $table->index(['period_type', 'period_start', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations_period_reports');
    }
};

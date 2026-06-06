<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('assigner_type', 20)->default('manager');
            $table->string('priority', 20)->default('medium');
            $table->string('status', 24)->default('pending');
            $table->string('category', 32)->default('follow_ups');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('due_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('completion_notes')->nullable();
            $table->decimal('performance_score', 5, 2)->nullable();
            $table->boolean('requires_acceptance')->default(false);
            $table->boolean('auto_generated')->default(false);
            $table->string('source_key', 128)->nullable()->unique();
            $table->foreignId('sales_team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['assigned_to', 'status', 'due_at']);
            $table->index(['assigned_by', 'created_at']);
            $table->index(['status', 'due_at']);
        });

        Schema::create('crm_task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('crm_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 64);
            $table->string('old_status', 24)->nullable();
            $table->string('new_status', 24)->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_task_logs');
        Schema::dropIfExists('crm_tasks');
    }
};

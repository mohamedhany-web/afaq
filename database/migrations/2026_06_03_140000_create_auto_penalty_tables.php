<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('auto_penalty_rules')) {
            Schema::create('auto_penalty_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('department_code', 16)->nullable();
                $table->string('source_type', 48);
                $table->string('report_period_type', 16)->nullable();
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('applies_to', 20)->default('all');
                $table->unsignedSmallInteger('grace_hours')->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['department_code', 'source_type', 'is_active']);
            });
        }

        if (! Schema::hasTable('auto_penalty_logs')) {
            Schema::create('auto_penalty_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rule_id')->constrained('auto_penalty_rules')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('source_type', 48);
                $table->string('source_key', 128);
                $table->decimal('amount', 14, 2);
                $table->text('reason');
                $table->foreignId('adjustment_id')->nullable()->constrained('comp_adjustments')->nullOnDelete();
                $table->foreignId('period_id')->nullable()->constrained('comp_payroll_periods')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamp('applied_at');
                $table->timestamps();

                $table->unique(['rule_id', 'source_key']);
                $table->index(['user_id', 'applied_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_penalty_logs');
        Schema::dropIfExists('auto_penalty_rules');
    }
};

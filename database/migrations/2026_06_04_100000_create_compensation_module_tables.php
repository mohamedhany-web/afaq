<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comp_kpi_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('target_role', 20); // rep | manager
            $table->string('evaluation_period', 20)->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['target_role', 'is_active']);
        });

        Schema::create('comp_kpi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('comp_kpi_templates')->cascadeOnDelete();
            $table->string('slug', 64);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2)->default(0);
            $table->decimal('target_value', 14, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['template_id', 'slug']);
        });

        Schema::create('comp_commission_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model', 32); // percentage, fixed_per_deal, revenue_tier, hybrid
            $table->json('config');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('comp_bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 64)->unique();
            $table->string('amount_type', 32); // fixed, percent_salary, percent_revenue
            $table->decimal('amount', 14, 2)->default(0);
            $table->json('conditions')->nullable();
            $table->string('target_role', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('comp_deduction_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 64)->unique();
            $table->string('amount_type', 32)->default('fixed');
            $table->decimal('amount', 14, 2)->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('comp_employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('base_salary', 14, 2)->default(0);
            $table->foreignId('kpi_template_id')->nullable()->constrained('comp_kpi_templates')->nullOnDelete();
            $table->foreignId('commission_plan_id')->nullable()->constrained('comp_commission_plans')->nullOnDelete();
            $table->date('effective_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('comp_payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('status', 20)->default('open'); // open, closed
            $table->timestamps();
            $table->unique(['year', 'month']);
        });

        Schema::create('comp_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('comp_payroll_periods')->cascadeOnDelete();
            $table->decimal('base_salary', 14, 2)->default(0);
            $table->decimal('commission_total', 14, 2)->default(0);
            $table->decimal('bonus_total', 14, 2)->default(0);
            $table->decimal('deduction_total', 14, 2)->default(0);
            $table->decimal('kpi_score', 5, 2)->nullable();
            $table->string('kpi_level', 32)->nullable();
            $table->decimal('team_score', 5, 2)->nullable();
            $table->decimal('net_pay', 14, 2)->default(0);
            $table->string('status', 24)->default('draft');
            $table->timestamp('calculated_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->json('breakdown')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'period_id']);
            $table->index(['period_id', 'status']);
        });

        Schema::create('comp_payroll_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('comp_payroll_runs')->cascadeOnDelete();
            $table->string('category', 32); // commission, bonus, deduction, kpi_note
            $table->string('label');
            $table->decimal('amount', 14, 2);
            $table->string('reference_type', 64)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('approval_status', 20)->default('approved');
            $table->timestamps();
        });

        Schema::create('comp_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('type', 16); // bonus | deduction
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('period_id')->nullable()->constrained('comp_payroll_periods')->nullOnDelete();
            $table->foreignId('rule_id')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('reason');
            $table->string('status', 20)->default('pending');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->index(['type', 'status']);
        });

        Schema::create('comp_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 64);
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comp_audit_logs');
        Schema::dropIfExists('comp_adjustments');
        Schema::dropIfExists('comp_payroll_line_items');
        Schema::dropIfExists('comp_payroll_runs');
        Schema::dropIfExists('comp_payroll_periods');
        Schema::dropIfExists('comp_employee_profiles');
        Schema::dropIfExists('comp_deduction_rules');
        Schema::dropIfExists('comp_bonus_rules');
        Schema::dropIfExists('comp_commission_plans');
        Schema::dropIfExists('comp_kpi_items');
        Schema::dropIfExists('comp_kpi_templates');
    }
};

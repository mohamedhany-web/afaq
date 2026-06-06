<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('channel', 40)->default('other');
            $table->string('status', 20)->default('draft');
            $table->decimal('budget', 14, 2)->nullable();
            $table->decimal('spent_amount', 14, 2)->default(0);
            $table->unsignedInteger('target_leads')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'channel']);
            $table->index('manager_id');
        });

        Schema::create('marketing_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 30)->default('content');
            $table->string('status', 20)->default('pending');
            $table->string('priority', 20)->default('medium');
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('recurrence', 20)->default('none');
            $table->unsignedTinyInteger('recurrence_interval')->default(1);
            $table->foreignId('parent_activity_id')->nullable()->constrained('marketing_activities')->nullOnDelete();
            $table->timestamp('next_occurrence_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'due_at']);
            $table->index('assigned_to');
            $table->index('recurrence');
        });

        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'lead_source')) {
                $table->string('lead_source', 60)->nullable()->after('client_type');
            }
            if (!Schema::hasColumn('clients', 'marketing_campaign_id')) {
                $table->foreignId('marketing_campaign_id')->nullable()->after('lead_source')
                    ->constrained('marketing_campaigns')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'marketing_campaign_id')) {
                $table->dropConstrainedForeignId('marketing_campaign_id');
            }
            if (Schema::hasColumn('clients', 'lead_source')) {
                $table->dropColumn('lead_source');
            }
        });

        Schema::dropIfExists('marketing_activities');
        Schema::dropIfExists('marketing_campaigns');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'lost_reason')) {
                $table->string('lost_reason', 50)->nullable()->after('lead_stage');
            }
            if (!Schema::hasColumn('clients', 'lost_reason_notes')) {
                $table->text('lost_reason_notes')->nullable()->after('lost_reason');
            }
            if (!Schema::hasColumn('clients', 'lost_at')) {
                $table->timestamp('lost_at')->nullable()->after('lost_reason_notes');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'lost_reason')) {
                $table->string('lost_reason', 50)->nullable()->after('stage');
            }
            if (!Schema::hasColumn('sales', 'lost_reason_notes')) {
                $table->text('lost_reason_notes')->nullable()->after('lost_reason');
            }
            if (!Schema::hasColumn('sales', 'lost_at')) {
                $table->timestamp('lost_at')->nullable()->after('lost_reason_notes');
            }
        });

        if (!Schema::hasTable('client_timeline_events')) {
            Schema::create('client_timeline_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('department', 30)->default('sales');
                $table->string('event_type', 40);
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('related_type')->nullable();
                $table->unsignedBigInteger('related_id')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('occurred_at');
                $table->timestamps();

                $table->index(['client_id', 'occurred_at']);
                $table->index(['event_type', 'occurred_at']);
            });
        }

        if (!Schema::hasTable('client_post_sales_cases')) {
            Schema::create('client_post_sales_cases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('case_type', 30);
                $table->string('status', 30)->default('open');
                $table->string('department', 30)->default('customer_service');
                $table->string('title');
                $table->text('description')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['client_id', 'status']);
                $table->index(['case_type', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_post_sales_cases');
        Schema::dropIfExists('client_timeline_events');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['lost_reason', 'lost_reason_notes', 'lost_at']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['lost_reason', 'lost_reason_notes', 'lost_at']);
        });
    }
};

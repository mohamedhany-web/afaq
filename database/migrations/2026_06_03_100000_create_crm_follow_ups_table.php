<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('interaction_type', 32);
            $table->text('notes');
            $table->dateTime('scheduled_at');
            $table->string('status', 20)->default('scheduled');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('overdue_notified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scheduled_at', 'status']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_follow_ups');
    }
};

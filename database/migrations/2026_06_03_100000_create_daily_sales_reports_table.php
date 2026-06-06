<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_sales_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');

            $table->json('metrics');
            $table->text('obstacles')->nullable();
            $table->text('support_required')->nullable();
            $table->unsignedSmallInteger('tomorrow_planned_calls')->nullable();
            $table->unsignedSmallInteger('tomorrow_planned_meetings')->nullable();
            $table->unsignedSmallInteger('tomorrow_planned_visits')->nullable();
            $table->text('tomorrow_priority_leads')->nullable();

            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'report_date']);
            $table->index(['status', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_sales_reports');
    }
};

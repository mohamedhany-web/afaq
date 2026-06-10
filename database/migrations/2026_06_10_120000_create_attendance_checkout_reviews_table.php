<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_checkout_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('review_date');
            $table->timestamp('requested_check_out_at');
            $table->decimal('total_hours_preview', 5, 2)->nullable();
            $table->boolean('is_early_departure')->default(false);
            $table->boolean('met_required_hours')->default(false);
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['review_date', 'status']);
            $table->index(['attendance_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_checkout_reviews');
    }
};

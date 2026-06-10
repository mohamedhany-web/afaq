<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_absence_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained()->nullOnDelete();
            $table->date('review_date');
            $table->string('flag_reason', 40)->default('no_check_in');
            $table->string('status', 30)->default('pending');
            $table->boolean('has_approved_leave')->default(false);
            $table->foreignId('reports_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'review_date'], 'att_abs_review_emp_date');
            $table->index(['review_date', 'status'], 'att_abs_review_date_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_absence_reviews');
    }
};

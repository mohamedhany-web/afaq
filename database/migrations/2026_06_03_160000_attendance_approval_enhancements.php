<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('attendances', 'current_status')) {
            DB::statement("ALTER TABLE attendances MODIFY current_status VARCHAR(30) NULL");
        }

        Schema::table('attendance_checkout_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance_checkout_reviews', 'deduction_amount')) {
                $table->decimal('deduction_amount', 10, 2)->nullable()->after('review_notes');
            }
            if (! Schema::hasColumn('attendance_checkout_reviews', 'deduction_reason')) {
                $table->string('deduction_reason', 500)->nullable()->after('deduction_amount');
            }
        });

        DB::statement("ALTER TABLE attendance_checkout_reviews MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('attendance_checkout_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_checkout_reviews', 'deduction_reason')) {
                $table->dropColumn('deduction_reason');
            }
            if (Schema::hasColumn('attendance_checkout_reviews', 'deduction_amount')) {
                $table->dropColumn('deduction_amount');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->time('work_start_time')->default('09:00:00')->after('daily_hours');
            $table->time('work_end_time')->default('17:00:00')->after('work_start_time');
            $table->json('weekly_off_days')->nullable()->after('work_end_time');
            $table->unsignedTinyInteger('late_grace_minutes')->default(15)->after('weekly_off_days');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'work_start_time',
                'work_end_time',
                'weekly_off_days',
                'late_grace_minutes',
            ]);
        });
    }
};

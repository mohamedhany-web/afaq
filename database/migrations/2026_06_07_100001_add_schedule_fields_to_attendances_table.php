<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('scheduled_check_in_at')->nullable()->after('check_in');
            $table->unsignedSmallInteger('late_minutes')->nullable()->after('scheduled_check_in_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['scheduled_check_in_at', 'late_minutes']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('required_hours', 4, 1)->nullable()->after('total_hours');
            $table->dateTime('scheduled_checkout_at')->nullable()->after('required_hours');
            $table->boolean('auto_checkout')->default(false)->after('scheduled_checkout_at');
            $table->boolean('work_day_locked')->default(false)->after('auto_checkout');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['required_hours', 'scheduled_checkout_at', 'auto_checkout', 'work_day_locked']);
        });
    }
};

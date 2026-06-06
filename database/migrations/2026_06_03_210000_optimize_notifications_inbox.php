<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'digest_key')) {
                $table->string('digest_key', 120)->nullable()->after('type');
                $table->index(['user_id', 'digest_key']);
            }

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['user_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'digest_key')) {
                $table->dropIndex(['user_id', 'digest_key']);
                $table->dropColumn('digest_key');
            }
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['user_id', 'is_read', 'created_at']);
            $table->dropIndex(['user_id', 'type', 'created_at']);
        });
    }
};

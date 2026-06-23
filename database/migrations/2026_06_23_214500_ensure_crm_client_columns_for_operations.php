<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'phone_normalized')) {
                $table->string('phone_normalized', 32)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('clients', 'lost_reason')) {
                $table->string('lost_reason', 50)->nullable()->after('lead_stage');
            }
            if (! Schema::hasColumn('clients', 'lost_reason_notes')) {
                $table->text('lost_reason_notes')->nullable()->after('lost_reason');
            }
            if (! Schema::hasColumn('clients', 'lost_at')) {
                $table->timestamp('lost_at')->nullable()->after('lost_reason_notes');
            }
        });

        if (Schema::hasColumn('clients', 'phone_normalized')) {
            DB::table('clients')
                ->whereNull('phone_normalized')
                ->whereNotNull('phone')
                ->orderBy('id')
                ->chunkById(200, function ($clients) {
                    foreach ($clients as $client) {
                        $normalized = \App\Models\Client::normalizePhone($client->phone);
                        if ($normalized) {
                            DB::table('clients')
                                ->where('id', $client->id)
                                ->update(['phone_normalized' => $normalized]);
                        }
                    }
                });
        }

        if (Schema::hasColumn('clients', 'phone_normalized')) {
            try {
                Schema::table('clients', function (Blueprint $table) {
                    $table->unique('phone_normalized', 'clients_phone_normalized_unique');
                });
            } catch (\Throwable) {
                // Index may already exist.
            }
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'phone_normalized')) {
                try {
                    $table->dropUnique('clients_phone_normalized_unique');
                } catch (\Throwable) {
                }
                $table->dropColumn('phone_normalized');
            }
            foreach (['lost_at', 'lost_reason_notes', 'lost_reason'] as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

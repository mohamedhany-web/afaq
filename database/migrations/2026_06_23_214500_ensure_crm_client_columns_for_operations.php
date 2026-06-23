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
            $this->reconcilePhoneNormalized();
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

    protected function reconcilePhoneNormalized(): void
    {
        // Reset partial backfills so duplicate phones can be reconciled safely.
        DB::table('clients')->whereNotNull('phone_normalized')->update(['phone_normalized' => null]);

        $owners = [];

        DB::table('clients')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('id')
            ->select(['id', 'phone'])
            ->chunkById(200, function ($rows) use (&$owners) {
                foreach ($rows as $row) {
                    $normalized = \App\Models\Client::normalizePhone($row->phone);
                    if (! $normalized || isset($owners[$normalized])) {
                        continue;
                    }

                    $owners[$normalized] = (int) $row->id;
                }
            });

        foreach ($owners as $normalized => $clientId) {
            DB::table('clients')
                ->where('id', $clientId)
                ->update(['phone_normalized' => $normalized]);
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

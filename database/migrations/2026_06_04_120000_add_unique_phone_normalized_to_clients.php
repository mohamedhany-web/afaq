<?php

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone_normalized', 32)->nullable()->after('phone');
        });

        $seen = [];

        DB::table('clients')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('id')
            ->select(['id', 'phone'])
            ->chunkById(200, function ($rows) use (&$seen) {
                foreach ($rows as $row) {
                    $normalized = Client::normalizePhone($row->phone);
                    if (!$normalized) {
                        continue;
                    }

                    if (isset($seen[$normalized])) {
                        continue;
                    }

                    $seen[$normalized] = (int) $row->id;
                    DB::table('clients')->where('id', $row->id)->update([
                        'phone_normalized' => $normalized,
                    ]);
                }
            });

        Schema::table('clients', function (Blueprint $table) {
            $table->unique('phone_normalized', 'clients_phone_normalized_unique');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('clients_phone_normalized_unique');
            $table->dropColumn('phone_normalized');
        });
    }
};

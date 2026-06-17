<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('property_types')->nullable()->after('city');
        });

        DB::table('projects')
            ->whereNotNull('property_type')
            ->where('property_type', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $raw = $row->property_type;
                    $types = [];

                    if (is_string($raw) && str_starts_with(trim($raw), '[')) {
                        $decoded = json_decode($raw, true);
                        if (is_array($decoded)) {
                            $types = array_values(array_filter($decoded));
                        }
                    } elseif (is_string($raw) && $raw !== '') {
                        $types = [$raw];
                    }

                    DB::table('projects')->where('id', $row->id)->update([
                        'property_types' => json_encode($types, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('property_types');
        });
    }
};

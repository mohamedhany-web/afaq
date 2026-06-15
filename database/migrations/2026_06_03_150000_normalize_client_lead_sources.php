<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = config('client_lead_sources.legacy_map', []);

        foreach ($map as $old => $new) {
            DB::table('clients')
                ->where('lead_source', $old)
                ->update(['lead_source' => $new]);
        }
    }

    public function down(): void
    {
        // لا يُسترجَع التصنيف القديم تلقائياً
    }
};

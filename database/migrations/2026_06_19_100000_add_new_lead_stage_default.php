<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('clients', 'lead_stage')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE clients MODIFY lead_stage VARCHAR(32) NOT NULL DEFAULT 'new'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('clients', 'lead_stage')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE clients MODIFY lead_stage VARCHAR(32) NOT NULL DEFAULT 'lead'");
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE clients MODIFY COLUMN status ENUM('active', 'inactive', 'suspended', 'prospect') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::table('clients')->where('status', 'prospect')->update(['status' => 'active']);
        DB::statement("ALTER TABLE clients MODIFY COLUMN status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active'");
    }
};

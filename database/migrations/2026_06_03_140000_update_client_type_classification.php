<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE clients MODIFY client_type VARCHAR(30) NOT NULL DEFAULT 'individual'");

        DB::table('clients')
            ->whereIn('client_type', ['small_business', 'enterprise'])
            ->update(['client_type' => 'company']);
    }

    public function down(): void
    {
        DB::table('clients')
            ->where('client_type', 'company')
            ->update(['client_type' => 'small_business']);

        DB::table('clients')
            ->whereIn('client_type', ['freelance', 'investor', 'partner'])
            ->update(['client_type' => 'individual']);

        DB::statement("ALTER TABLE clients MODIFY client_type ENUM('individual', 'small_business', 'enterprise') NOT NULL DEFAULT 'individual'");
    }
};

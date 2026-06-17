<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')->where('ownership_type', 'owned')->update(['ownership_type' => 'afaq_private']);
        DB::table('projects')->where('ownership_type', 'developer_third_party')->update(['ownership_type' => 'developer']);
    }

    public function down(): void
    {
        DB::table('projects')->where('ownership_type', 'afaq_private')->update(['ownership_type' => 'owned']);
        DB::table('projects')->where('ownership_type', 'developer')->update(['ownership_type' => 'developer_third_party']);
    }
};

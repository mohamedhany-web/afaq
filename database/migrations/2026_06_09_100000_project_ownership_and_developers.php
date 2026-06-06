<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_developers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('real_estate_developer_id')->nullable()->after('developer_name')
                ->constrained('real_estate_developers')->nullOnDelete();
            $table->string('ownership_type', 40)->default('developer_third_party')->after('real_estate_developer_id');
            $table->json('ownership_details')->nullable()->after('ownership_type');
        });

        $names = DB::table('projects')
            ->whereNotNull('developer_name')
            ->where('developer_name', '!=', '')
            ->distinct()
            ->pluck('developer_name');

        foreach ($names as $name) {
            $trimmed = trim((string) $name);
            if ($trimmed === '') {
                continue;
            }
            DB::table('real_estate_developers')->insertOrIgnore([
                'name' => $trimmed,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (DB::table('projects')->whereNotNull('developer_name')->where('developer_name', '!=', '')->get() as $row) {
            $devId = DB::table('real_estate_developers')->where('name', trim($row->developer_name))->value('id');
            if ($devId) {
                DB::table('projects')->where('id', $row->id)->update([
                    'real_estate_developer_id' => $devId,
                    'ownership_type' => 'developer_third_party',
                ]);
            }
        }

        DB::table('projects')
            ->whereNull('real_estate_developer_id')
            ->update(['ownership_type' => 'owned']);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('real_estate_developer_id');
            $table->dropColumn(['ownership_type', 'ownership_details']);
        });

        Schema::dropIfExists('real_estate_developers');
    }
};

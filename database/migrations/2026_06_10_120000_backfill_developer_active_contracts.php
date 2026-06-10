<?php

use App\Models\DeveloperContract;
use App\Models\RealEstateDeveloper;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        RealEstateDeveloper::query()
            ->where('status', RealEstateDeveloper::STATUS_ACTIVE)
            ->whereDoesntHave('contracts', fn ($q) => $q->where('status', DeveloperContract::STATUS_ACTIVE))
            ->orderBy('id')
            ->each(function (RealEstateDeveloper $developer): void {
                DeveloperContract::create([
                    'real_estate_developer_id' => $developer->id,
                    'status' => DeveloperContract::STATUS_ACTIVE,
                    'approved_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        // لا إزالة تلقائية
    }
};

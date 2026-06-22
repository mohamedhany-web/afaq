<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** إضافة أعمدة الوحدات/السداد إن فُقدت لأن الترحيل السابق شُغّل قبل إنشاء الجداول */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_units')) {
            Schema::table('project_units', function (Blueprint $table) {
                foreach ([
                    'direction' => fn () => $table->string('direction', 32)->nullable(),
                    'floor_number' => fn () => $table->string('floor_number', 16)->nullable(),
                    'floor_label' => fn () => $table->string('floor_label', 64)->nullable(),
                    'apartment_number' => fn () => $table->string('apartment_number', 32)->nullable(),
                    'unit_price_total' => fn () => $table->decimal('unit_price_total', 14, 2)->nullable(),
                ] as $col => $adder) {
                    if (! Schema::hasColumn('project_units', $col)) {
                        $adder();
                    }
                }
            });
        }

        if (Schema::hasTable('unit_payment_plans')) {
            Schema::table('unit_payment_plans', function (Blueprint $table) {
                $decimals = [
                    'building_percent' => [5, 2],
                    'discount_percent' => [5, 2],
                    'loading_percent' => [5, 2],
                    'net_unit_price' => [14, 2],
                    'total_contract_amount' => [14, 2],
                    'maintenance_deposit' => [14, 2],
                    'remaining_balance' => [14, 2],
                ];
                foreach ($decimals as $col => [$p, $s]) {
                    if (! Schema::hasColumn('unit_payment_plans', $col)) {
                        $table->decimal($col, $p, $s)->nullable();
                    }
                }
                if (! Schema::hasColumn('unit_payment_plans', 'installment_months')) {
                    $table->unsignedSmallInteger('installment_months')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // لا حذف — ترحيل إصلاحي فقط
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects') && ! Schema::hasColumn('projects', 'inventory_source')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('inventory_source', 24)->default('developer');
                $table->index('inventory_source');
            });
        }

        if (Schema::hasTable('project_units')) {
            Schema::table('project_units', function (Blueprint $table) {
                if (Schema::hasColumn('project_units', 'building_floor_id')) {
                    try {
                        $table->foreignId('building_floor_id')->nullable()->change();
                    } catch (\Throwable) {
                        // doctrine/dbal may be unavailable
                    }
                }
                foreach ([
                    'direction' => fn (Blueprint $t) => $t->string('direction', 32)->nullable(),
                    'floor_number' => fn (Blueprint $t) => $t->string('floor_number', 16)->nullable(),
                    'floor_label' => fn (Blueprint $t) => $t->string('floor_label', 64)->nullable(),
                    'apartment_number' => fn (Blueprint $t) => $t->string('apartment_number', 32)->nullable(),
                    'unit_price_total' => fn (Blueprint $t) => $t->decimal('unit_price_total', 14, 2)->nullable(),
                ] as $col => $adder) {
                    if (! Schema::hasColumn('project_units', $col)) {
                        $adder($table);
                    }
                }
            });
        }

        if (Schema::hasTable('unit_payment_plans')) {
            Schema::table('unit_payment_plans', function (Blueprint $table) {
                $cols = [
                    'building_percent' => ['decimal', 5, 2],
                    'discount_percent' => ['decimal', 5, 2],
                    'loading_percent' => ['decimal', 5, 2],
                    'net_unit_price' => ['decimal', 14, 2],
                    'total_contract_amount' => ['decimal', 14, 2],
                    'maintenance_deposit' => ['decimal', 14, 2],
                    'remaining_balance' => ['decimal', 14, 2],
                ];
                foreach ($cols as $name => [$type, $p, $s]) {
                    if (! Schema::hasColumn('unit_payment_plans', $name)) {
                        $table->decimal($name, $p, $s)->nullable();
                    }
                }
                if (! Schema::hasColumn('unit_payment_plans', 'installment_months')) {
                    $table->unsignedSmallInteger('installment_months')->nullable();
                }
            });
        }

        if (Schema::hasColumn('projects', 'inventory_source') && Schema::hasColumn('projects', 'ownership_type')) {
            DB::table('projects')->where('ownership_type', 'afaq_private')->update(['inventory_source' => 'company']);
            DB::table('projects')->where('ownership_type', 'developer')->update(['inventory_source' => 'developer']);
            DB::table('projects')
                ->whereNotIn('ownership_type', ['afaq_private', 'developer'])
                ->update(['inventory_source' => 'non_company']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unit_payment_plans')) {
            Schema::table('unit_payment_plans', function (Blueprint $table) {
                foreach (['building_percent', 'discount_percent', 'loading_percent', 'net_unit_price', 'total_contract_amount', 'maintenance_deposit', 'remaining_balance', 'installment_months'] as $col) {
                    if (Schema::hasColumn('unit_payment_plans', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('project_units')) {
            Schema::table('project_units', function (Blueprint $table) {
                foreach (['direction', 'floor_number', 'floor_label', 'apartment_number', 'unit_price_total'] as $col) {
                    if (Schema::hasColumn('project_units', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasColumn('projects', 'inventory_source')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropIndex(['inventory_source']);
                $table->dropColumn('inventory_source');
            });
        }
    }
};

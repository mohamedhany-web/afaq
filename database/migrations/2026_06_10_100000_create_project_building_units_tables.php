<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'land_area_m2')) {
                $table->decimal('land_area_m2', 12, 2)->nullable()->after('location');
            }
            if (!Schema::hasColumn('projects', 'building_config')) {
                $table->json('building_config')->nullable()->after('land_area_m2');
            }
        });

        Schema::create('building_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('level');
            $table->string('label', 64);
            $table->decimal('height_m', 5, 2)->default(3.6);
            $table->json('use_mix')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['project_id', 'level']);
            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_floor_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('use_type', 24);
            $table->decimal('area_m2', 10, 2);
            $table->decimal('price_cash', 14, 2)->nullable();
            $table->decimal('price_installment', 14, 2)->nullable();
            $table->string('status', 20)->default('available');
            $table->decimal('mesh_x', 8, 2)->default(0);
            $table->decimal('mesh_y', 8, 2)->default(0);
            $table->decimal('mesh_z', 8, 2)->default(0);
            $table->decimal('mesh_w', 8, 2)->default(1);
            $table->decimal('mesh_h', 8, 2)->default(1);
            $table->decimal('mesh_d', 8, 2)->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'code']);
            $table->index(['project_id', 'status']);
            $table->index(['building_floor_id', 'use_type']);
        });

        Schema::create('unit_payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_unit_id')->constrained()->cascadeOnDelete();
            $table->string('plan_type', 20);
            $table->decimal('down_percent', 5, 2)->nullable();
            $table->unsignedTinyInteger('years')->nullable();
            $table->decimal('installment_per_m2', 14, 2)->nullable();
            $table->decimal('down_payment_amount', 14, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('project_3d_scenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('version')->default(1);
            $table->json('camera_config')->nullable();
            $table->json('scene_config')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_3d_scenes');
        Schema::dropIfExists('unit_payment_plans');
        Schema::dropIfExists('project_units');
        Schema::dropIfExists('building_floors');

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'building_config')) {
                $table->dropColumn('building_config');
            }
            if (Schema::hasColumn('projects', 'land_area_m2')) {
                $table->dropColumn('land_area_m2');
            }
        });
    }
};

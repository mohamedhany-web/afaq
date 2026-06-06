<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('location');
            }
            if (!Schema::hasColumn('projects', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('projects', 'map_zoom')) {
                $table->unsignedTinyInteger('map_zoom')->nullable()->default(14)->after('longitude');
            }
        });

        if (!Schema::hasTable('project_map_pins')) {
            Schema::create('project_map_pins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('pin_type', 20)->default('unit');
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['project_id', 'pin_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_map_pins');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'map_zoom']);
        });
    }
};

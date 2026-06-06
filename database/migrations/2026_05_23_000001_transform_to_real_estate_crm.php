<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'location')) {
                $table->string('location')->nullable()->after('description');
                $table->string('city')->nullable()->after('location');
                $table->string('property_type')->nullable()->after('city');
                $table->integer('total_units')->default(0)->after('property_type');
                $table->integer('available_units')->default(0)->after('total_units');
                $table->integer('sold_units')->default(0)->after('available_units');
                $table->decimal('price_from', 15, 2)->nullable()->after('sold_units');
                $table->decimal('price_to', 15, 2)->nullable()->after('price_from');
                $table->string('developer_name')->nullable()->after('price_to');
                $table->string('listing_status')->default('active')->after('developer_name');
            }
        });

        if (!Schema::hasTable('sales_teams')) {
            Schema::create('sales_teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_team_members')) {
            Schema::create('sales_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_team_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['sales_team_id', 'user_id']);
            });
        }

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'sales_team_id')) {
                $table->foreignId('sales_team_id')->nullable()->constrained()->nullOnDelete();
                $table->string('unit_type')->nullable();
                $table->string('interest_type')->nullable();
                $table->date('viewing_date')->nullable();
                $table->text('viewing_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'sales_team_id')) {
                $table->dropForeign(['sales_team_id']);
                $table->dropColumn(['sales_team_id', 'unit_type', 'interest_type', 'viewing_date', 'viewing_notes']);
            }
        });

        Schema::dropIfExists('sales_team_members');
        Schema::dropIfExists('sales_teams');

        Schema::table('projects', function (Blueprint $table) {
            $columns = ['location', 'city', 'property_type', 'total_units', 'available_units',
                'sold_units', 'price_from', 'price_to', 'developer_name', 'listing_status'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

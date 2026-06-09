<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_developers', function (Blueprint $table) {
            $table->text('description')->nullable()->after('notes');
            $table->string('address')->nullable()->after('description');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('status', 20)->default('active')->after('city');
            $table->boolean('portal_enabled')->default(false)->after('status');
        });

        Schema::create('developer_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('real_estate_developer_id')->constrained()->cascadeOnDelete();
            $table->string('contract_ref')->nullable();
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->boolean('exclusivity')->default(false);
            $table->date('exclusivity_until')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone', 40)->nullable();
            $table->text('listing_terms')->nullable();
            $table->text('notes')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['real_estate_developer_id', 'status']);
        });

        Schema::create('developer_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('real_estate_developer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('portal_role', 30)->default('owner');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('developer_portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('real_estate_developer_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('project_type', 40)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_portfolio_items');
        Schema::dropIfExists('developer_accounts');
        Schema::dropIfExists('developer_contracts');

        Schema::table('real_estate_developers', function (Blueprint $table) {
            $table->dropColumn(['description', 'address', 'city', 'status', 'portal_enabled']);
        });
    }
};

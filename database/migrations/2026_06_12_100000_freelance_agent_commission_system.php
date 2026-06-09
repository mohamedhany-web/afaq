<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelance_agent_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number', 64)->nullable();
            $table->string('national_id', 32)->nullable();
            $table->string('nationality', 64)->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 24)->default('active'); // active, terminated, expired
            $table->decimal('quarterly_target_amount', 14, 2)->nullable();
            $table->unsignedSmallInteger('quarterly_target_deals')->nullable();
            $table->string('company_signatory_name')->nullable();
            $table->string('company_signatory_title')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('transaction_type', 32)->nullable()->after('interest_type');
            $table->decimal('company_commission_amount', 14, 2)->nullable()->after('transaction_type');
            $table->foreignId('listing_agent_id')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->boolean('commission_collected')->default(false)->after('company_commission_amount');
            $table->timestamp('commission_collected_at')->nullable()->after('commission_collected');
            $table->string('commission_payout_status', 24)->default('pending')->after('commission_collected_at');
            $table->text('commission_notes')->nullable()->after('commission_payout_status');
        });

        Schema::create('sale_commission_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('agent_role', 32); // primary_agent, listing_agent, selling_agent
            $table->decimal('percent_of_company', 5, 2);
            $table->decimal('amount', 14, 2);
            $table->string('payout_status', 24)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['sale_id', 'user_id', 'agent_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_commission_splits');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('listing_agent_id');
            $table->dropColumn([
                'transaction_type',
                'company_commission_amount',
                'commission_collected',
                'commission_collected_at',
                'commission_payout_status',
                'commission_notes',
            ]);
        });

        Schema::dropIfExists('freelance_agent_contracts');
    }
};

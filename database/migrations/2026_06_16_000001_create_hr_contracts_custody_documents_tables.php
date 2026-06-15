<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->string('contract_type', 40);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('status', 20)->default('draft');
            $table->text('terms')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
        });

        Schema::create('custody_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->string('category', 40);
            $table->string('serial_number')->nullable();
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->string('issue_condition')->nullable();
            $table->text('issue_notes')->nullable();
            $table->string('issue_file_path')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('return_condition')->nullable();
            $table->text('return_notes')->nullable();
            $table->string('return_file_path')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index('status');
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 40);
            $table->string('title');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime', 120)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->date('expires_at')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('custody_assignments');
        Schema::dropIfExists('employee_contracts');
    }
};

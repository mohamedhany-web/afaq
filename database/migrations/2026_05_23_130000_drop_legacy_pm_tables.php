<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('task_updates');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('bugs');
        Schema::dropIfExists('qa_tests');
        Schema::dropIfExists('q_a_tests');
    }

    public function down(): void
    {
        // Legacy PM tables removed intentionally; restore from git history if needed.
    }
};

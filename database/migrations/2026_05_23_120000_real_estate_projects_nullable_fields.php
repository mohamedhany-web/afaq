<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if ($this->hasForeign('projects', 'projects_client_id_foreign')) {
                $table->dropForeign(['client_id']);
            }
            if ($this->hasForeign('projects', 'projects_project_manager_id_foreign')) {
                $table->dropForeign(['project_manager_id']);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->unsignedBigInteger('project_manager_id')->nullable()->change();
            $table->date('start_date')->nullable()->change();
        });

        Schema::table('projects', function (Blueprint $table) {
            if (!$this->hasForeign('projects', 'projects_client_id_foreign')) {
                $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            }
            if (!$this->hasForeign('projects', 'projects_project_manager_id_foreign')) {
                $table->foreign('project_manager_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if ($this->hasForeign('projects', 'projects_client_id_foreign')) {
                $table->dropForeign(['client_id']);
            }
            if ($this->hasForeign('projects', 'projects_project_manager_id_foreign')) {
                $table->dropForeign(['project_manager_id']);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->unsignedBigInteger('project_manager_id')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreign('project_manager_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    private function hasForeign(string $table, string $name): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$database, $table, $name, 'FOREIGN KEY']
        );

        return count($result) > 0;
    }
};

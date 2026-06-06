<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('projects')) {
            return;
        }

        if ($this->columnsAlreadyNullable()) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if ($this->hasForeignKeyOnColumn('projects', 'client_id')) {
                $table->dropForeign(['client_id']);
            }
            if ($this->hasForeignKeyOnColumn('projects', 'project_manager_id')) {
                $table->dropForeign(['project_manager_id']);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->unsignedBigInteger('project_manager_id')->nullable()->change();
            $table->date('start_date')->nullable()->change();
        });

        Schema::table('projects', function (Blueprint $table) {
            if (!$this->hasForeignKeyOnColumn('projects', 'client_id')) {
                $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
            }
            if (!$this->hasForeignKeyOnColumn('projects', 'project_manager_id')) {
                $table->foreign('project_manager_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if ($this->hasForeignKeyOnColumn('projects', 'client_id')) {
                $table->dropForeign(['client_id']);
            }
            if ($this->hasForeignKeyOnColumn('projects', 'project_manager_id')) {
                $table->dropForeign(['project_manager_id']);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->unsignedBigInteger('project_manager_id')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
        });

        Schema::table('projects', function (Blueprint $table) {
            if (!$this->hasForeignKeyOnColumn('projects', 'client_id')) {
                $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            }
            if (!$this->hasForeignKeyOnColumn('projects', 'project_manager_id')) {
                $table->foreign('project_manager_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }

    private function columnsAlreadyNullable(): bool
    {
        return $this->columnIsNullable('projects', 'client_id')
            && $this->columnIsNullable('projects', 'project_manager_id')
            && $this->columnIsNullable('projects', 'start_date');
    }

    private function columnIsNullable(string $table, string $column): bool
    {
        if (!Schema::hasColumn($table, $column)) {
            return true;
        }

        $driver = Schema::getConnection()->getDriverName();

        return match ($driver) {
            'sqlite' => $this->sqliteColumnIsNullable($table, $column),
            'mysql', 'mariadb' => $this->mysqlColumnIsNullable($table, $column),
            'pgsql' => $this->pgsqlColumnIsNullable($table, $column),
            default => false,
        };
    }

    private function sqliteColumnIsNullable(string $table, string $column): bool
    {
        $columns = Schema::getConnection()->select("PRAGMA table_info('{$table}')");
        $match = collect($columns)->firstWhere('name', $column);

        return $match !== null && (int) ($match->notnull ?? 1) === 0;
    }

    private function mysqlColumnIsNullable(string $table, string $column): bool
    {
        $database = Schema::getConnection()->getDatabaseName();
        $result = Schema::getConnection()->select(
            'SELECT IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1',
            [$database, $table, $column],
        );

        return isset($result[0]) && strtoupper((string) $result[0]->IS_NULLABLE) === 'YES';
    }

    private function pgsqlColumnIsNullable(string $table, string $column): bool
    {
        $result = Schema::getConnection()->select(
            'SELECT is_nullable FROM information_schema.columns WHERE table_name = ? AND column_name = ? LIMIT 1',
            [$table, $column],
        );

        return isset($result[0]) && strtoupper((string) $result[0]->is_nullable) === 'YES';
    }

    private function hasForeignKeyOnColumn(string $table, string $column): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        return match ($driver) {
            'sqlite' => $this->sqliteHasForeignKeyOnColumn($table, $column),
            'mysql', 'mariadb' => $this->mysqlHasForeignKeyOnColumn($table, $column),
            'pgsql' => $this->pgsqlHasForeignKeyOnColumn($table, $column),
            default => false,
        };
    }

    private function sqliteHasForeignKeyOnColumn(string $table, string $column): bool
    {
        $foreignKeys = Schema::getConnection()->select("PRAGMA foreign_key_list('{$table}')");

        return collect($foreignKeys)->contains(fn ($fk) => ($fk->from ?? null) === $column);
    }

    private function mysqlHasForeignKeyOnColumn(string $table, string $column): bool
    {
        $database = Schema::getConnection()->getDatabaseName();
        $result = Schema::getConnection()->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1',
            [$database, $table, $column],
        );

        return count($result) > 0;
    }

    private function pgsqlHasForeignKeyOnColumn(string $table, string $column): bool
    {
        $result = Schema::getConnection()->select(
            'SELECT 1 FROM information_schema.key_column_usage WHERE table_name = ? AND column_name = ? AND position_in_unique_constraint IS NOT NULL LIMIT 1',
            [$table, $column],
        );

        return count($result) > 0;
    }
};

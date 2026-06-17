<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class PermissionRegistryService
{
    /** @return list<string> */
    public function registryKeys(): array
    {
        return array_values(array_unique(config('permissions.registry', [])));
    }

    public function ensureRegisteredInDatabase(): void
    {
        foreach ($this->registryKeys() as $key) {
            Permission::firstOrCreate(['name' => $key, 'guard_name' => 'web']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function allWebPermissions(): Collection
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();
    }

    /** @return array{in_db_only: list<string>, in_registry_only: list<string>, total_db: int, total_registry: int, total_ui_modules: int} */
    public function syncReport(): array
    {
        $db = $this->allWebPermissions()->pluck('name')->all();
        $registry = $this->registryKeys();
        $modules = CrmRoleCatalogService::modulePermissionKeys();

        return [
            'in_db_only' => array_values(array_diff($db, $registry)),
            'in_registry_only' => array_values(array_diff($registry, $db)),
            'not_in_ui_modules' => array_values(array_diff($db, $modules)),
            'total_db' => count($db),
            'total_registry' => count($registry),
            'total_ui_modules' => count(array_intersect($db, $modules)),
        ];
    }
}

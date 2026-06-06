<?php

namespace App\Services;

use App\Helpers\RoleHelper;
use App\Services\MarketingEmployeeService;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CrmRoleCatalogService
{
    public static function assignableRoleNames(): array
    {
        return config('crm_roles.assignable_roles', []);
    }

    public static function canonicalRole(string $roleName): string
    {
        $aliases = config('crm_roles.role_aliases', []);

        return $aliases[$roleName] ?? $roleName;
    }

    public static function roleMeta(string $roleName): array
    {
        $canonical = self::canonicalRole($roleName);
        $roles = config('crm_roles.roles', []);

        if (isset($roles[$canonical])) {
            return $roles[$canonical];
        }

        foreach ($roles as $key => $meta) {
            if (in_array($roleName, $meta['legacy_names'] ?? [], true)) {
                return $meta;
            }
        }

        return [
            'label' => $roleName,
            'description' => 'دور قديم — يُنصح بتحديثه',
            'color' => '#6b7280',
            'workspace' => 'legacy',
            'assignable' => false,
        ];
    }

    public static function roleLabel(string $roleName): string
    {
        return self::roleMeta($roleName)['label'] ?? RoleHelper::getRoleName($roleName);
    }

    public static function activeRoles(): Collection
    {
        $names = array_keys(config('crm_roles.roles', []));

        return Role::with('permissions')
            ->whereIn('name', $names)
            ->get()
            ->sortBy(fn (Role $r) => array_search($r->name, $names, true));
    }

    public static function assignableRoles(): Collection
    {
        return Role::with('permissions')
            ->whereIn('name', self::assignableRoleNames())
            ->get()
            ->sortBy(fn (Role $r) => array_search($r->name, self::assignableRoleNames(), true));
    }

    public static function activePermissions(): Collection
    {
        $keys = collect(config('crm_roles.permission_groups', []))
            ->flatMap(fn ($g) => $g['permissions'])
            ->unique()
            ->values()
            ->all();

        return Permission::whereIn('name', $keys)->orderBy('name')->get();
    }

    public static function permissionGroups(): array
    {
        $active = self::activePermissions()->pluck('name')->all();
        $groups = config('crm_roles.permission_groups', []);
        $result = [];

        foreach ($groups as $key => $group) {
            $perms = array_values(array_intersect($group['permissions'], $active));
            if ($perms !== []) {
                $result[$key] = [
                    'label' => $group['label'],
                    'permissions' => $perms,
                ];
            }
        }

        return $result;
    }

    public static function resolveUserDisplayRole(\App\Models\User $user): ?string
    {
        $priority = ['super_admin', 'admin', 'sales_manager', 'manager', 'marketing_manager', 'sales_rep', 'sales_agent', 'marketing_rep', 'employee', 'hr', 'client'];

        foreach ($priority as $name) {
            if ($user->hasRole($name)) {
                return self::canonicalRole($name);
            }
        }

        return $user->roles->first()?->name;
    }

    public static function assignRoleToUser(\App\Models\User $user, string $roleName): void
    {
        \App\Models\UserPermission::where('user_id', $user->id)->delete();

        if (in_array($roleName, ['sales_manager', 'manager'], true)) {
            CrmEmployeeService::assignSalesRole($user, CrmEmployeeService::ROLE_MANAGER);
        } elseif (in_array($roleName, ['sales_rep', 'sales_agent', 'employee'], true)) {
            CrmEmployeeService::assignSalesRole($user, CrmEmployeeService::ROLE_EMPLOYEE);
        } elseif (in_array($roleName, ['marketing_manager'], true)) {
            MarketingEmployeeService::assignMarketingRole($user, MarketingEmployeeService::ROLE_MANAGER);
        } elseif (in_array($roleName, ['marketing_rep'], true)) {
            MarketingEmployeeService::assignMarketingRole($user, MarketingEmployeeService::ROLE_REP);
        } else {
            $strip = array_merge(
                CrmEmployeeService::LEGACY_MANAGER_ROLES,
                CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
                MarketingEmployeeService::LEGACY_MANAGER_ROLES,
                MarketingEmployeeService::LEGACY_REP_ROLES,
                ['manager', 'employee'],
            );
            foreach (array_unique($strip) as $name) {
                if ($user->hasRole($name)) {
                    $user->removeRole($name);
                }
            }
            $user->syncRoles([$roleName]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

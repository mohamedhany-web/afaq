<?php

namespace App\Services;

use App\Helpers\RoleHelper;
use App\Services\MarketingEmployeeService;
use App\Services\OperationsEmployeeService;
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

    public static function workspaceGroups(): array
    {
        return config('crm_roles.workspace_groups', []);
    }

    /** @return list<string> */
    public static function rolesForWorkspaceGroup(string $groupKey): array
    {
        $roles = config("crm_roles.workspace_groups.{$groupKey}.roles", []);

        return collect($roles)
            ->flatMap(function (string $role) {
                $legacy = config("crm_roles.roles.{$role}.legacy_names", []);

                return array_merge([$role], $legacy);
            })
            ->unique()
            ->values()
            ->all();
    }

    public static function workspaceGroupForRole(string $roleName): ?string
    {
        $canonical = self::canonicalRole($roleName);

        foreach (self::workspaceGroups() as $key => $group) {
            foreach ($group['roles'] as $role) {
                if ($canonical === $role) {
                    return $key;
                }
                $legacy = config("crm_roles.roles.{$role}.legacy_names", []);
                if (in_array($roleName, $legacy, true)) {
                    return $key;
                }
            }
        }

        return null;
    }

    public static function workspaceGroupMeta(string $groupKey): ?array
    {
        $group = config("crm_roles.workspace_groups.{$groupKey}");

        return is_array($group) ? $group : null;
    }

    /** @return array<string, array{label: string, description: string, color: string, count: int}> */
    public static function userCountsByWorkspaceGroup(): array
    {
        $result = [];

        foreach (self::workspaceGroups() as $key => $group) {
            $roleNames = self::rolesForWorkspaceGroup($key);
            $result[$key] = [
                'label' => $group['label'],
                'description' => $group['description'] ?? '',
                'color' => $group['color'] ?? '#6b7280',
                'count' => $roleNames === []
                    ? 0
                    : \App\Models\User::role($roleNames)->count(),
            ];
        }

        return $result;
    }

    /** @return array<string, array<string, mixed>> */
    public static function roleAssignmentHints(): array
    {
        $hints = [];
        foreach (config('crm_roles.roles', []) as $roleKey => $meta) {
            $groupKey = self::workspaceGroupForRole($roleKey);
            $group = $groupKey ? self::workspaceGroupMeta($groupKey) : null;
            $hints[$roleKey] = [
                'label' => $meta['label'] ?? $roleKey,
                'description' => $meta['description'] ?? '',
                'color' => $meta['color'] ?? '#6b7280',
                'workspace_label' => $group['label'] ?? '—',
                'needs_employee' => $group['needs_employee'] ?? !in_array($roleKey, ['client', 'super_admin', 'admin'], true),
                'default_department' => $group['default_department'] ?? null,
            ];
        }

        return $hints;
    }

    public static function resolveUserDisplayRole(\App\Models\User $user): ?string
    {
        $priority = ['super_admin', 'admin', 'sales_manager', 'manager', 'sales_team_leader', 'marketing_manager', 'operation_manager', 'sales_rep', 'sales_agent', 'marketing_rep', 'employee', 'hr', 'client'];

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
        } elseif (in_array($roleName, ['sales_team_leader'], true)) {
            CrmEmployeeService::assignSalesRole($user, CrmEmployeeService::ROLE_TEAM_LEADER);
        } elseif (in_array($roleName, ['sales_rep', 'sales_agent', 'employee'], true)) {
            CrmEmployeeService::assignSalesRole($user, CrmEmployeeService::ROLE_EMPLOYEE);
        } elseif (in_array($roleName, ['marketing_manager'], true)) {
            MarketingEmployeeService::assignMarketingRole($user, MarketingEmployeeService::ROLE_MANAGER);
        } elseif (in_array($roleName, ['marketing_rep'], true)) {
            MarketingEmployeeService::assignMarketingRole($user, MarketingEmployeeService::ROLE_REP);
        } elseif (in_array($roleName, ['operation_manager'], true)) {
            OperationsEmployeeService::assignOperationsRole($user);
        } else {
            $strip = array_merge(
                CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
                CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
                CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
                MarketingEmployeeService::LEGACY_MANAGER_ROLES,
                MarketingEmployeeService::LEGACY_REP_ROLES,
                OperationsEmployeeService::LEGACY_MANAGER_ROLES,
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

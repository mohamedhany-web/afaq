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

    public static function permissionModules(): array
    {
        return config('crm_roles.permission_modules', []);
    }

    /** @return list<string> */
    public static function modulePermissionKeys(): array
    {
        return collect(self::permissionModules())
            ->flatMap(function (array $module) {
                $keys = array_filter([
                    $module['view'] ?? null,
                    $module['create'] ?? null,
                    $module['edit'] ?? null,
                    $module['delete'] ?? null,
                ]);

                foreach ($module['extras'] ?? [] as $extraKey => $label) {
                    $keys[] = is_int($extraKey) ? $label : $extraKey;
                }

                return $keys;
            })
            ->unique()
            ->values()
            ->all();
    }

    public static function activePermissions(): Collection
    {
        return app(PermissionRegistryService::class)->allWebPermissions();
    }

    public static function permissionGroups(): array
    {
        $active = self::activePermissions()->pluck('name')->all();
        $moduleKeys = self::modulePermissionKeys();
        $groupLabels = [
            'admin' => 'الإدارة',
            'crm' => 'CRM العقاري',
            'operations' => 'العمليات',
            'hr' => 'الموارد البشرية',
            'finance' => 'المالية',
            'marketing' => 'التسويق',
            'support' => 'الدعم',
            'portal' => 'بوابة العميل',
            'legacy' => 'وحدات قديمة',
        ];

        $grouped = [];
        foreach (self::permissionModules() as $module) {
            $groupKey = $module['group'] ?? 'other';
            $perms = array_values(array_filter(array_merge(
                array_filter([
                    $module['view'] ?? null,
                    $module['create'] ?? null,
                    $module['edit'] ?? null,
                    $module['delete'] ?? null,
                ]),
                array_keys($module['extras'] ?? [])
            ), fn ($p) => in_array($p, $active, true)));

            if ($perms === []) {
                continue;
            }

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'label' => $groupLabels[$groupKey] ?? $groupKey,
                    'modules' => [],
                    'permissions' => [],
                ];
            }

            $grouped[$groupKey]['modules'][] = $module;
            $grouped[$groupKey]['permissions'] = array_values(array_unique(array_merge(
                $grouped[$groupKey]['permissions'],
                $perms
            )));
        }

        $uncategorized = array_values(array_diff($active, $moduleKeys));
        if ($uncategorized !== []) {
            $extras = [];
            foreach ($uncategorized as $key) {
                $extras[$key] = \App\Helpers\RoleHelper::getPermissionName($key);
            }

            $grouped['uncategorized'] = [
                'label' => 'صلاحيات غير مصنّفة — أضفها لـ permission_modules',
                'modules' => [[
                    'key' => '_uncategorized',
                    'label' => 'يجب تصنيفها في config/crm_roles.php',
                    'group' => 'uncategorized',
                    'view' => null,
                    'create' => null,
                    'edit' => null,
                    'delete' => null,
                    'extras' => $extras,
                ]],
                'permissions' => $uncategorized,
            ];
        }

        return $grouped;
    }

    /** @return array<string, list<string>> */
    public static function permissionsByModuleKey(): array
    {
        $result = [];
        foreach (self::permissionModules() as $module) {
            $keys = array_values(array_filter(array_merge(
                array_filter([
                    $module['view'] ?? null,
                    $module['create'] ?? null,
                    $module['edit'] ?? null,
                    $module['delete'] ?? null,
                ]),
                array_keys($module['extras'] ?? [])
            )));
            $result[$module['key']] = $keys;
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
            $existingRoles = $roleNames === []
                ? []
                : Role::query()
                    ->where('guard_name', 'web')
                    ->whereIn('name', $roleNames)
                    ->pluck('name')
                    ->all();

            $result[$key] = [
                'label' => $group['label'],
                'description' => $group['description'] ?? '',
                'color' => $group['color'] ?? '#6b7280',
                'count' => $existingRoles === []
                    ? 0
                    : \App\Models\User::role($existingRoles)->count(),
            ];
        }

        return $result;
    }

    /** @return list<string> */
    public static function existingRoleNames(array $roleNames, string $guard = 'web'): array
    {
        if ($roleNames === []) {
            return [];
        }

        return Role::query()
            ->where('guard_name', $guard)
            ->whereIn('name', $roleNames)
            ->pluck('name')
            ->all();
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
            if (Role::where('name', $name)->where('guard_name', 'web')->exists() && $user->hasRole($name)) {
                return self::canonicalRole($name);
            }
        }

        return $user->roles->first()?->name;
    }

    public static function assignRoleToUser(\App\Models\User $user, string $roleName): void
    {
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

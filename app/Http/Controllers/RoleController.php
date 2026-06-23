<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CrmRoleCatalogService;
use App\Services\PermissionRegistryService;
use App\Services\PermissionVisibilityService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request, PermissionRegistryService $registry)
    {
        $registry->ensureRegisteredInDatabase();
        $permissionSyncReport = $registry->syncReport();

        $roles = CrmRoleCatalogService::activeRoles();
        $workspaceGroups = CrmRoleCatalogService::workspaceGroups();
        $workspaceFilter = $request->get('workspace');
        $search = trim((string) $request->get('q', ''));

        $usersQuery = User::with(['roles', 'employee.department'])->orderBy('name');

        if ($workspaceFilter && isset($workspaceGroups[$workspaceFilter])) {
            $roleNames = CrmRoleCatalogService::rolesForWorkspaceGroup($workspaceFilter);
            if ($roleNames !== []) {
                $usersQuery->role($roleNames);
            }
        }

        if ($search !== '') {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->get();
        $workspaceCounts = CrmRoleCatalogService::userCountsByWorkspaceGroup();

        $stats = [
            'total_roles' => $roles->count(),
            'total_permissions' => CrmRoleCatalogService::activePermissions()->count(),
            'total_users' => User::count(),
            'workspace_users' => $workspaceFilter && isset($workspaceCounts[$workspaceFilter])
                ? $workspaceCounts[$workspaceFilter]['count']
                : $users->count(),
        ];

        return view('roles.index', compact(
            'roles', 'users', 'stats', 'workspaceGroups',
            'workspaceFilter', 'search', 'workspaceCounts', 'permissionSyncReport'
        ));
    }

    public function assignRole(Request $request, User $user)
    {
        $allowed = CrmRoleCatalogService::assignableRoleNames();

        $request->validate([
            'role' => 'required|in:' . implode(',', $allowed),
        ]);

        if ($user->hasRole('super_admin') && $request->role !== 'super_admin' && !auth()->user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'لا يمكن تغيير دور مدير النظام');
        }

        CrmRoleCatalogService::assignRoleToUser($user, $request->role);

        return redirect()->route('roles.user-permissions', $user)->with('success', 'تم تعيين الدور: ' . CrmRoleCatalogService::roleLabel($request->role));
    }

    public function assignCustomPermissions(Request $request, User $user)
    {
        $activeKeys = CrmRoleCatalogService::activePermissions()->pluck('name')->all();

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'in:' . implode(',', $activeKeys),
        ]);

        $allPermissions = $activeKeys;
        $selectedPermissions = $request->permissions ?? [];

        $displayRoleName = CrmRoleCatalogService::resolveUserDisplayRole($user);
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->unique()->values()->all();

        \App\Models\UserPermission::where('user_id', $user->id)
            ->whereIn('permission_key', $allPermissions)
            ->delete();

        foreach ($allPermissions as $permission) {
            $isSelected = in_array($permission, $selectedPermissions);
            $isFromRole = in_array($permission, $rolePermissions);

            if ($isSelected) {
                if (!$isFromRole) {
                    $user->givePermissionTo($permission);
                    \App\Models\UserPermission::create([
                        'user_id' => $user->id,
                        'permission_key' => $permission,
                        'is_enabled' => true,
                    ]);
                } else {
                    \App\Models\UserPermission::where('user_id', $user->id)
                        ->where('permission_key', $permission)
                        ->delete();
                }
            } else {
                if ($isFromRole) {
                    \App\Models\UserPermission::create([
                        'user_id' => $user->id,
                        'permission_key' => $permission,
                        'is_enabled' => false,
                    ]);
                } else {
                    \App\Models\UserPermission::where('user_id', $user->id)
                        ->where('permission_key', $permission)
                        ->delete();
                    if ($user->hasPermissionTo($permission)) {
                        $user->revokePermissionTo($permission);
                    }
                }
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.user-permissions', $user)->with('success', 'تم تحديث الصلاحيات بنجاح');
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        if (!in_array($role->name, array_keys(config('crm_roles.roles', [])), true)) {
            abort(403, 'لا يمكن تعديل هذا الدور القديم من الواجهة.');
        }

        $activeKeys = CrmRoleCatalogService::activePermissions()->pluck('name')->all();

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'in:' . implode(',', $activeKeys),
        ]);

        $role->syncPermissions($request->permissions ?? []);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.role-permissions', $role)->with('success', 'تم تحديث صلاحيات الدور بنجاح');
    }

    public function rolePermissions(Role $role, PermissionRegistryService $registry)
    {
        $registry->ensureRegisteredInDatabase();
        $permissionSyncReport = $registry->syncReport();

        if (!in_array($role->name, array_keys(config('crm_roles.roles', [])), true)) {
            abort(403, 'لا يمكن تعديل هذا الدور القديم من الواجهة.');
        }

        $permissions = CrmRoleCatalogService::activePermissions();
        $permissionGroups = CrmRoleCatalogService::permissionGroups();
        $permissionModules = CrmRoleCatalogService::permissionModules();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $meta = CrmRoleCatalogService::roleMeta($role->name);

        return view('roles.role-permissions', compact(
            'role', 'permissions', 'permissionGroups', 'permissionModules',
            'rolePermissions', 'meta', 'permissionSyncReport'
        ));
    }

    /** @return array{rolePermissions: list<string>, userPermissions: list<string>, customPermissionsMap: array<string, bool>} */
    protected function buildUserPermissionState(User $user, $permissions, ?Role $userRole): array
    {
        $rolePermissions = $userRole ? $userRole->permissions->pluck('name')->toArray() : [];
        $userDirectPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        $allCustomPermissions = \App\Models\UserPermission::where('user_id', $user->id)->get();
        $customPermissionsMap = [];

        foreach ($allCustomPermissions as $cp) {
            if (Permission::where('name', $cp->permission_key)->exists()) {
                $customPermissionsMap[$cp->permission_key] = $cp->is_enabled;
            }
        }

        $userPermissions = [];
        foreach ($permissions as $permission) {
            $permName = $permission->name;
            if ($user->can($permName)) {
                $userPermissions[] = $permName;
            }
        }

        return compact('rolePermissions', 'userPermissions', 'customPermissionsMap');
    }

    public function userPermissions(User $user, PermissionVisibilityService $visibility, PermissionRegistryService $registry)
    {
        $registry->ensureRegisteredInDatabase();
        $permissionSyncReport = $registry->syncReport();

        $user->load(['roles.permissions', 'customPermissions', 'permissions']);

        $roles = CrmRoleCatalogService::assignableRoles();
        $permissions = CrmRoleCatalogService::activePermissions();
        $permissionGroups = CrmRoleCatalogService::permissionGroups();
        $permissionModules = CrmRoleCatalogService::permissionModules();
        $displayRole = CrmRoleCatalogService::resolveUserDisplayRole($user);
        $userRole = $roles->firstWhere('name', $displayRole) ?? $user->roles->first();

        $state = $this->buildUserPermissionState($user, $permissions, $userRole);
        $rolePermissions = $state['rolePermissions'];
        $userPermissions = $state['userPermissions'];
        $customPermissionsMap = $state['customPermissionsMap'];

        $sidebarPreview = $visibility->sidebarPreview($user);
        $workspaceGroup = $displayRole ? CrmRoleCatalogService::workspaceGroupForRole($displayRole) : null;
        $workspaceMeta = $workspaceGroup ? CrmRoleCatalogService::workspaceGroupMeta($workspaceGroup) : null;

        return view('roles.user-permissions', compact(
            'user', 'roles', 'permissions', 'permissionGroups', 'permissionModules',
            'userRole', 'userPermissions', 'rolePermissions',
            'customPermissionsMap', 'displayRole', 'sidebarPreview',
            'workspaceGroup', 'workspaceMeta', 'permissionSyncReport'
        ));
    }
}

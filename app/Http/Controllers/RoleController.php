<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CrmRoleCatalogService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = CrmRoleCatalogService::activeRoles();
        $users = User::with('roles')->orderBy('name')->get();

        $stats = [
            'total_roles' => $roles->count(),
            'total_permissions' => CrmRoleCatalogService::activePermissions()->count(),
            'total_users' => $users->count(),
            'crm_users' => $users->filter(fn (User $u) => in_array(
                CrmRoleCatalogService::resolveUserDisplayRole($u),
                ['sales_manager', 'sales_rep', 'admin', 'super_admin'],
                true
            ))->count(),
        ];

        return view('roles.index', compact('roles', 'users', 'stats'));
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

        return redirect()->back()->with('success', 'تم تعيين الدور: ' . CrmRoleCatalogService::roleLabel($request->role));
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

        $rolePermissions = [];
        if ($user->roles->first()) {
            $rolePermissions = $user->roles->first()->permissions->pluck('name')->toArray();
        }

        \App\Models\UserPermission::where('user_id', $user->id)->delete();

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
                } elseif ($user->hasPermissionTo($permission)) {
                    $user->revokePermissionTo($permission);
                }
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->back()->with('success', 'تم تحديث الصلاحيات بنجاح');
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

        return redirect()->back()->with('success', 'تم تحديث صلاحيات الدور بنجاح');
    }

    public function userPermissions(User $user)
    {
        $roles = CrmRoleCatalogService::assignableRoles();
        $permissions = CrmRoleCatalogService::activePermissions();
        $permissionGroups = CrmRoleCatalogService::permissionGroups();
        $displayRole = CrmRoleCatalogService::resolveUserDisplayRole($user);
        $userRole = $roles->firstWhere('name', $displayRole) ?? $user->roles->first();

        $rolePermissions = $userRole ? $userRole->permissions->pluck('name')->toArray() : [];
        $userDirectPermissions = $user->getAllPermissions()->pluck('name')->toArray();

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
            if (isset($customPermissionsMap[$permName])) {
                if ($customPermissionsMap[$permName]) {
                    $userPermissions[] = $permName;
                }
            } elseif (in_array($permName, $userDirectPermissions)) {
                $userPermissions[] = $permName;
            }
        }

        return view('roles.user-permissions', compact(
            'user', 'roles', 'permissions', 'permissionGroups',
            'userRole', 'userPermissions', 'rolePermissions',
            'customPermissionsMap', 'displayRole'
        ));
    }
}

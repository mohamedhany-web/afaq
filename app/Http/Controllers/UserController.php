<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with(['employee', 'roles'])->paginate(15);
        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        if (!auth()->user()->can('create-users')) {
            abort(403, 'غير مصرح لك بإنشاء مستخدمين');
        }

        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();
        
        return view('users.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create-users')) {
            abort(403, 'غير مصرح لك بإنشاء مستخدمين');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            // Employee data
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Assign roles
            $user->assignRole($request->roles);

            // Generate employee ID automatically
            $employeeId = Employee::generateEmployeeIdBySettings();
            
            // Create employee
            Employee::create([
                'user_id' => $user->id,
                'employee_id' => $employeeId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'salary' => $request->salary,
                'hire_date' => $request->hire_date,
                'employment_type' => $request->employment_type,
                'status' => 'active',
                'daily_hours' => $request->input('daily_hours', config('work_day.default_daily_hours', 8)),
            ]);

            // Set custom permissions
            if ($request->has('custom_permissions')) {
                foreach ($request->custom_permissions as $permissionKey) {
                    $user->setCustomPermission($permissionKey, true);
                }
            }

            return redirect()->route('users.index')
                ->with('success', "تم إنشاء المستخدم بنجاح. الرقم التوظيفي: $employeeId");

        } catch (\Exception $e) {
            // If employee creation fails, delete the user
            if (isset($user)) {
                $user->delete();
            }

            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load(['employee.department', 'roles']);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!auth()->user()->can('edit-users')) {
            abort(403, 'غير مصرح لك بتعديل المستخدمين');
        }

        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();
        $user->load(['employee', 'roles']);
        
        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can('edit-users')) {
            abort(403, 'غير مصرح لك بتعديل المستخدمين');
        }
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'custom_permissions' => 'nullable|array',
            'custom_permissions.*' => 'string',
        ];

        // Add password validation only if password is provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Add employee data validation only if employee exists
        if ($user->employee) {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'department_id' => 'required|exists:departments,id',
                'position' => 'required|string|max:255',
                'salary' => 'required|numeric|min:0',
                'employment_type' => 'required|in:full_time,part_time,contract,intern',
                'status' => 'required|in:active,inactive,terminated',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update user basic data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Sync roles
        $user->syncRoles($request->roles);

        // Update employee data if exists
        if ($user->employee) {
            $user->employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'salary' => $request->salary,
                'employment_type' => $request->employment_type,
                'status' => $request->status,
            ]);
        }

        // Clear existing custom permissions and set new ones
        \App\Models\UserPermission::where('user_id', $user->id)->delete();
        
        if ($request->has('custom_permissions')) {
            foreach ($request->custom_permissions as $permissionKey) {
                $user->setCustomPermission($permissionKey, true);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->can('delete-users')) {
            abort(403, 'غير مصرح لك بحذف المستخدمين');
        }

        if ($user->hasRole('super_admin')) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف مدير النظام الأعلى');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        if (!$user->hasRole($request->role)) {
            $user->assignRole($request->role);
            
            return redirect()->back()
                ->with('success', 'تم تعيين الدور بنجاح');
        }

        return redirect()->back()
            ->with('warning', 'المستخدم لديه هذا الدور بالفعل');
    }

    public function removeRole(User $user, Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->back()
                ->with('error', 'لا يمكن إزالة دور مدير النظام الأعلى');
        }

        $user->removeRole($role);

        return redirect()->back()
            ->with('success', 'تم إزالة الدور بنجاح');
    }
}
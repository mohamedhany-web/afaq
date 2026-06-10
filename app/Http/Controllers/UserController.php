<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\CrmRoleCatalogService;
use App\Services\MarketingEmployeeService;
use App\Services\OperationsEmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with(['employee.department', 'roles'])
            ->when($request->search, function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($sub) use ($s) {
                    $sub->where('name', 'like', $s)->orWhere('email', 'like', $s);
                });
            })
            ->when($request->role, fn ($q) => $q->role($request->role))
            ->when($request->status === 'verified', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->when($request->status === 'pending', fn ($q) => $q->whereNull('email_verified_at'))
            ->when($request->status === 'with_employee', fn ($q) => $q->whereHas('employee'))
            ->when($request->status === 'without_employee', fn ($q) => $q->whereDoesntHave('employee'))
            ->latest();

        $stats = [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'with_employee' => User::whereHas('employee')->count(),
            'admins' => User::role(['super_admin', 'admin'])->count(),
        ];

        $users = $query->paginate(15)->withQueryString();
        $assignableRoles = $this->assignableRolesForActor();

        return view('users.index', compact('users', 'stats', 'assignableRoles'));
    }

    public function create()
    {
        $this->authorizeCreate();

        return view('users.create', [
            'assignableRoles' => $this->assignableRolesForActor(),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();

        $assignable = $this->assignableRoleNames();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in($assignable)],
            'create_employee' => 'nullable|boolean',
            'first_name' => 'required_if:create_employee,1|nullable|string|max:255',
            'last_name' => 'required_if:create_employee,1|nullable|string|max:255',
            'phone' => 'required_if:create_employee,1|nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            CrmRoleCatalogService::assignRoleToUser($user, $request->role);

            $employeeId = null;
            if ($request->boolean('create_employee')) {
                $departmentId = $request->department_id ?: $this->defaultDepartmentIdForRole($request->role);

                $employee = Employee::create([
                    'user_id' => $user->id,
                    'employee_id' => Employee::generateEmployeeIdBySettings(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'department_id' => $departmentId,
                    'position' => $request->position ?: CrmRoleCatalogService::roleLabel($request->role),
                    'salary' => $request->salary ?? 0,
                    'hire_date' => $request->hire_date ?? now()->toDateString(),
                    'employment_type' => $request->employment_type ?? 'full_time',
                    'status' => 'active',
                    'daily_hours' => config('work_day.default_daily_hours', 8),
                    'reports_to_user_id' => app(\App\Services\OrganizationalHierarchyService::class)
                        ->defaultReportsToUserId(Department::find($departmentId)?->code),
                ]);
                $employeeId = $employee->employee_id;
            }

            $message = 'تم إنشاء المستخدم بنجاح';
            if ($employeeId) {
                $message .= " — الرقم التوظيفي: {$employeeId}";
            }

            return redirect()->route('users.index')->with('success', $message);
        } catch (\Throwable $e) {
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

        $displayRole = CrmRoleCatalogService::resolveUserDisplayRole($user);

        return view('users.show', [
            'user' => $user,
            'displayRole' => $displayRole,
        ]);
    }

    public function edit(User $user)
    {
        if (!auth()->user()->can('edit-users')) {
            abort(403);
        }

        $user->load(['employee.department', 'roles']);

        return view('users.edit', [
            'user' => $user,
            'assignableRoles' => $this->assignableRolesForActor(),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
            'currentRole' => CrmRoleCatalogService::resolveUserDisplayRole($user),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->can('edit-users')) {
            abort(403);
        }

        $assignable = $this->assignableRoleNames();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => ['required', 'string', Rule::in($assignable)],
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($user->hasRole('super_admin') && $request->role !== 'super_admin' && !auth()->user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'لا يمكن تغيير دور مدير النظام الأعلى.');
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        CrmRoleCatalogService::assignRoleToUser($user, $request->role);

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

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->can('delete-users')) {
            abort(403);
        }

        if ($user->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'لا يمكن حذف مدير النظام الأعلى');
        }

        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الحالي');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    protected function authorizeCreate(): void
    {
        if (!auth()->user()->can('create-users')) {
            abort(403, 'غير مصرح لك بإنشاء مستخدمين');
        }
    }

    /** @return list<string> */
    protected function assignableRoleNames(): array
    {
        $roles = config('crm_roles.assignable_roles', []);

        if (!auth()->user()->hasRole('super_admin')) {
            $roles = array_values(array_diff($roles, ['super_admin']));
        }

        return $roles;
    }

    protected function assignableRolesForActor()
    {
        return CrmRoleCatalogService::assignableRoles()
            ->filter(fn ($role) => in_array($role->name, $this->assignableRoleNames(), true));
    }

    protected function defaultDepartmentIdForRole(string $role): int
    {
        return match ($role) {
            'sales_manager', 'sales_team_leader', 'sales_rep' => CrmEmployeeService::salesDepartment()->id,
            'marketing_manager', 'marketing_rep' => MarketingEmployeeService::marketingDepartment()->id,
            'operation_manager' => OperationsEmployeeService::operationsDepartment()->id,
            default => CrmEmployeeService::salesDepartment()->id,
        };
    }
}

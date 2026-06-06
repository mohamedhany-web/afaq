<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\CrmScopeService;
use App\Services\EmployeeRoleService;
use App\Services\MarketingEmployeeService;
use App\Services\MarketingScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $marketingOnly = $request->boolean('marketing_only');
        $salesDepartment = $marketingOnly
            ? MarketingEmployeeService::marketingDepartment()
            : CrmEmployeeService::salesDepartment();
        $salesOnly = $request->boolean('sales_only');
        $user = auth()->user();

        $query = ($marketingOnly
            ? MarketingScopeService::for($user)->employeesQuery()
            : ($salesOnly && $user->canAccessCrm()
                ? CrmScopeService::for($user)->employeesQuery()
                : Employee::query()->where('department_id', $salesDepartment->id)))
            ->with(['user.roles', 'department'])
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('employee_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($marketingOnly && $request->crm_role === 'marketing_manager', function ($q) {
                $q->whereHas('user.roles', fn ($r) => $r->whereIn('name', MarketingEmployeeService::LEGACY_MANAGER_ROLES));
            })
            ->when($marketingOnly && $request->crm_role === 'marketing_rep', function ($q) {
                $q->whereHas('user.roles', fn ($r) => $r->whereIn('name', MarketingEmployeeService::LEGACY_REP_ROLES));
            })
            ->when(!$marketingOnly && $request->crm_role === 'manager', function ($q) {
                $q->whereHas('user.roles', fn ($r) => $r->whereIn('name', CrmEmployeeService::LEGACY_MANAGER_ROLES));
            })
            ->when(!$marketingOnly && $request->crm_role === 'employee', function ($q) {
                $q->whereHas('user.roles', fn ($r) => $r->whereIn('name', CrmEmployeeService::LEGACY_EMPLOYEE_ROLES));
            });

        $statsBase = clone $query;
        $managerRoles = $marketingOnly
            ? MarketingEmployeeService::LEGACY_MANAGER_ROLES
            : CrmEmployeeService::LEGACY_MANAGER_ROLES;
        $repRoles = $marketingOnly
            ? MarketingEmployeeService::LEGACY_REP_ROLES
            : CrmEmployeeService::LEGACY_EMPLOYEE_ROLES;

        $stats = [
            'total' => (clone $statsBase)->count(),
            'active' => (clone $statsBase)->where('status', 'active')->count(),
            'managers' => (clone $statsBase)->whereHas('user.roles', fn ($r) => $r->whereIn('name', $managerRoles))->count(),
            'agents' => (clone $statsBase)->whereHas('user.roles', fn ($r) => $r->whereIn('name', $repRoles))->count(),
        ];

        $employees = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'salesDepartment' => $salesDepartment,
            'stats' => $stats,
            'salesOnly' => $salesOnly,
            'roleLabels' => $marketingOnly ? MarketingEmployeeService::ROLE_LABELS : CrmEmployeeService::ROLE_LABELS,
            'canCreate' => $user->can('create-employees'),
            'canEdit' => $user->can('edit-employees'),
            'canDelete' => $user->can('delete-employees'),
            'marketingOnly' => $marketingOnly,
        ]);
    }

    public function create(Request $request)
    {
        if (!auth()->user()->can('create-employees')) {
            abort(403, 'غير مصرح لك بإنشاء موظفين');
        }

        $marketingOnly = $request->boolean('marketing_only');
        $salesDepartment = $marketingOnly
            ? MarketingEmployeeService::marketingDepartment()
            : CrmEmployeeService::salesDepartment();
        $users = User::doesntHave('employee')->orderBy('name')->get();
        $roleLabels = $marketingOnly ? MarketingEmployeeService::ROLE_LABELS : CrmEmployeeService::ROLE_LABELS;

        return view('employees.create', compact('salesDepartment', 'users', 'roleLabels', 'request') + [
            'salesOnly' => $request->boolean('sales_only'),
            'marketingOnly' => $marketingOnly,
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create-employees')) {
            abort(403, 'غير مصرح لك بإنشاء موظفين');
        }

        $marketingOnly = $request->boolean('marketing_only');
        $allowedRoles = $marketingOnly
            ? implode(',', [MarketingEmployeeService::ROLE_MANAGER, MarketingEmployeeService::ROLE_REP])
            : 'manager,employee';

        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'create_new_user' => 'nullable|boolean',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|string|max:20',
            'crm_role' => 'required|in:' . $allowedRoles,
            'position' => 'nullable|string|max:255',
            'salary' => 'required|numeric|min:0',
            'daily_hours' => 'required|numeric|min:1|max:12',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ]);

        $validator->sometimes('password', 'required|string|min:8|confirmed', function ($input) {
            return (filter_var($input->create_new_user, FILTER_VALIDATE_BOOLEAN) || $input->create_new_user === '1' || $input->create_new_user === 1)
                && empty($input->user_id);
        });

        if (!$request->user_id && !$request->create_new_user) {
            $validator->errors()->add('user_id', 'يجب اختيار مستخدم موجود أو إنشاء مستخدم جديد');

            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $userId = $request->user_id;
            $employeeEmail = $request->email;

            if ($request->create_new_user && !$userId) {
                if (User::where('email', $request->email)->exists()) {
                    return redirect()->back()
                        ->withErrors(['email' => 'البريد الإلكتروني مستخدم بالفعل في جدول المستخدمين'])
                        ->withInput();
                }

                $fullName = trim($request->first_name . ' ' . $request->last_name);

                $user = User::create([
                    'name' => $fullName,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                ]);

                if ($marketingOnly) {
                    MarketingEmployeeService::assignMarketingRole($user, $request->crm_role);
                } else {
                    CrmEmployeeService::assignSalesRole($user, $request->crm_role);
                }

                $userId = $user->id;
                $employeeEmail = $user->email;
            } elseif ($userId) {
                $existingUser = User::find($userId);
                if ($existingUser) {
                    if ($marketingOnly) {
                        MarketingEmployeeService::assignMarketingRole($existingUser, $request->crm_role);
                    } else {
                        CrmEmployeeService::assignSalesRole($existingUser, $request->crm_role);
                    }
                }
            }

            if ($userId && Employee::where('user_id', $userId)->exists()) {
                return redirect()->back()
                    ->withErrors(['user_id' => 'المستخدم المختار لديه موظف مرتبط به بالفعل'])
                    ->withInput();
            }

            $department = $marketingOnly
                ? MarketingEmployeeService::marketingDepartment()
                : CrmEmployeeService::salesDepartment();
            $position = $request->position ?: ($marketingOnly
                ? MarketingEmployeeService::positionForRole($request->crm_role)
                : CrmEmployeeService::positionForRole($request->crm_role));

            $employee = $this->createEmployeeRecord($request, $userId, $employeeEmail, $department, $position);

            if ($marketingOnly && $request->crm_role === MarketingEmployeeService::ROLE_MANAGER) {
                $department->update(['manager_id' => $employee->id]);
            }
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $fullName = trim($request->first_name . ' ' . $request->last_name);
                    if ($user->name !== $fullName) {
                        $user->update(['name' => $fullName]);
                    }
                }
            }

            $redirectParams = ['employee' => $employee];
            if ($request->boolean('sales_only')) {
                $redirectParams['sales_only'] = 1;
            }
            if ($marketingOnly) {
                $redirectParams['marketing_only'] = 1;
            }

            return redirect()->route('employees.show', $redirectParams)
                ->with('success', 'تم إنشاء الموظف بنجاح. الرقم التوظيفي: ' . $employee->employee_id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء الموظف: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Request $request, Employee $employee)
    {
        $this->authorizeSalesEmployee($employee);

        $employee->load(['user.roles', 'department', 'attendances', 'leaves']);

        $stats = [
            'total_attendance_days' => $employee->attendances()->count(),
            'total_leaves' => $employee->leaves()->count(),
            'pending_leaves' => $employee->leaves()->where('status', 'pending')->count(),
            'approved_leaves' => $employee->leaves()->where('status', 'approved')->count(),
        ];

        $user = auth()->user();

        $marketingOnly = $request->boolean('marketing_only') || EmployeeRoleService::isMarketingEmployee($employee);
        $roleMeta = EmployeeRoleService::resolve($employee);

        return view('employees.show', [
            'employee' => $employee,
            'stats' => $stats,
            'salesOnly' => $request->boolean('sales_only'),
            'marketingOnly' => $marketingOnly,
            'roleMeta' => $roleMeta,
            'canEdit' => $user->can('edit-employees'),
            'canDelete' => $user->can('delete-employees'),
        ]);
    }

    public function edit(Request $request, Employee $employee)
    {
        $this->authorizeSalesEmployee($employee);

        if (!auth()->user()->can('edit-employees')) {
            abort(403);
        }

        $marketingOnly = $request->boolean('marketing_only') || EmployeeRoleService::isMarketingEmployee($employee);
        $salesDepartment = $marketingOnly
            ? MarketingEmployeeService::marketingDepartment()
            : CrmEmployeeService::salesDepartment();
        $roleLabels = EmployeeRoleService::roleLabelsForDepartment($employee->department?->code);
        $currentRole = EmployeeRoleService::currentRoleKey($employee);

        return view('employees.edit', compact('employee', 'salesDepartment', 'roleLabels', 'currentRole', 'request') + [
            'salesOnly' => $request->boolean('sales_only'),
            'marketingOnly' => $marketingOnly,
            'canDelete' => auth()->user()->can('delete-employees') && !($employee->user?->hasRole('super_admin')),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeSalesEmployee($employee);

        if (!auth()->user()->can('edit-employees')) {
            abort(403);
        }

        $marketingOnly = $request->boolean('marketing_only') || EmployeeRoleService::isMarketingEmployee($employee);
        $allowedRoles = $marketingOnly
            ? implode(',', [MarketingEmployeeService::ROLE_MANAGER, MarketingEmployeeService::ROLE_REP])
            : 'manager,employee';
        $isSuperAdminUser = $employee->user?->hasRole('super_admin') ?? false;

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|unique:employees,employee_id,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'crm_role' => $isSuperAdminUser
                ? 'nullable|in:' . $allowedRoles
                : 'required|in:' . $allowedRoles,
            'position' => 'nullable|string|max:255',
            'salary' => 'required|numeric|min:0',
            'daily_hours' => 'required|numeric|min:1|max:12',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'status' => 'required|in:active,inactive,terminated',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'hire_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $department = $marketingOnly
            ? MarketingEmployeeService::marketingDepartment()
            : CrmEmployeeService::salesDepartment();
        $crmRole = $request->crm_role ?? EmployeeRoleService::currentRoleKey($employee);
        $position = $request->position ?: ($marketingOnly
            ? MarketingEmployeeService::positionForRole($crmRole)
            : CrmEmployeeService::positionForRole($crmRole));

        $employee->update([
            'employee_id' => $request->employee_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department_id' => $department->id,
            'position' => $position,
            'salary' => $request->salary,
            'daily_hours' => $request->daily_hours,
            'employment_type' => $request->employment_type ?? $employee->employment_type ?? 'full_time',
            'hire_date' => $request->hire_date ?? $employee->hire_date,
            'status' => $request->status,
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
        ]);

        if ($employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user) {
                $fullName = trim($request->first_name . ' ' . $request->last_name);

                if (!$user->hasRole('super_admin') && $request->filled('crm_role')) {
                    if ($marketingOnly) {
                        MarketingEmployeeService::assignMarketingRole($user, $request->crm_role);
                    } else {
                        CrmEmployeeService::assignSalesRole($user, $request->crm_role);
                    }
                }

                $emailExists = User::where('email', $request->email)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if (!$emailExists) {
                    $user->update([
                        'name' => $fullName,
                        'email' => $request->email,
                    ]);
                } else {
                    $user->update(['name' => $fullName]);
                }
            }
        }

        $redirectParams = ['employee' => $employee];
        if ($request->boolean('sales_only')) {
            $redirectParams['sales_only'] = 1;
        }
        if ($marketingOnly) {
            $redirectParams['marketing_only'] = 1;
        }

        return redirect()->route('employees.show', $redirectParams)
            ->with('success', 'تم تحديث الموظف بنجاح');
    }

    public function destroy(Request $request, Employee $employee)
    {
        $this->authorizeSalesEmployee($employee);

        if (!auth()->user()->can('delete-employees')) {
            abort(403);
        }

        if ($employee->user && $employee->user->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'لا يمكن حذف مدير النظام الأعلى');
        }

        $employee->delete();

        return redirect()->route('employees.index', $this->indexParams($request))
            ->with('success', 'تم حذف الموظف بنجاح');
    }

    protected function authorizeSalesEmployee(Employee $employee): void
    {
        $salesDeptId = CrmEmployeeService::salesDepartment()->id;
        $marketingDeptId = MarketingEmployeeService::marketingDepartment()->id;

        if (!in_array((int) $employee->department_id, [(int) $salesDeptId, (int) $marketingDeptId], true)) {
            abort(404);
        }

        $user = auth()->user();
        if ((int) $employee->department_id === (int) $salesDeptId
            && $user->canAccessCrm()
            && !CrmScopeService::for($user)->canViewEmployee($employee)) {
            abort(403, 'لا يمكنك عرض بيانات هذا الموظف.');
        }
    }

    protected function indexParams(Request $request): array
    {
        $params = $request->only(['search', 'status', 'crm_role']);
        if ($request->boolean('sales_only')) {
            $params['sales_only'] = 1;
        }
        if ($request->boolean('marketing_only')) {
            $params['marketing_only'] = 1;
        }

        return array_filter($params, fn ($v) => $v !== null && $v !== '');
    }

    /**
     * إنشاء سجل الموظف مع إعادة المحاولة عند تعارض الرقم التوظيفي.
     */
    protected function createEmployeeRecord(Request $request, ?int $userId, string $employeeEmail, $salesDepartment, string $position): Employee
    {
        $attributes = [
            'user_id' => $userId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $employeeEmail,
            'phone' => $request->phone,
            'department_id' => $salesDepartment->id,
            'position' => $position,
            'salary' => $request->salary,
            'daily_hours' => $request->daily_hours,
            'hire_date' => $request->hire_date,
            'employment_type' => $request->employment_type,
            'status' => 'active',
            'address' => $request->address,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
        ];

        $attempts = 0;
        while ($attempts < 5) {
            try {
                return Employee::create(array_merge($attributes, [
                    'employee_id' => Employee::generateEmployeeIdBySettings(),
                ]));
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() !== '23000' || !str_contains($e->getMessage(), 'employees_employee_id_unique')) {
                    throw $e;
                }
                $attempts++;
            }
        }

        throw new \RuntimeException('تعذر توليد رقم توظيفي فريد. حاول مرة أخرى.');
    }
}

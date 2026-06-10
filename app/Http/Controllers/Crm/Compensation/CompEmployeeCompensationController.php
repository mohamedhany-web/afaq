<?php

namespace App\Http\Controllers\Crm\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Compensation\CompCommissionPlan;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\Compensation\CompKpiTemplate;
use App\Models\User;
use App\Services\Compensation\CompensationAuditService;
use App\Services\CrmEmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompEmployeeCompensationController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $profiles = CompEmployeeProfile::with(['user', 'kpiTemplate', 'commissionPlan'])->latest()->paginate(20);
        $users = User::role(array_merge(
            CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
            CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES
        ))->orderBy('name')->get(['id', 'name']);

        return view('crm.compensation.admin.profiles.index', compact('profiles', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:comp_employee_profiles,user_id',
            'base_salary' => 'required|numeric|min:0',
            'kpi_template_id' => 'nullable|exists:comp_kpi_templates,id',
            'commission_plan_id' => 'nullable|exists:comp_commission_plans,id',
            'effective_from' => 'nullable|date',
        ]);

        $profile = CompEmployeeProfile::create($data + ['is_active' => true]);
        CompensationAuditService::log('compensation.profile.created', CompEmployeeProfile::class, $profile->id);

        return back()->with('success', 'تم ربط هيكل التعويض بالموظف');
    }

    public function update(Request $request, CompEmployeeProfile $profile)
    {
        $this->authorizeAdmin();
        $old = $profile->toArray();

        $data = $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'kpi_template_id' => 'nullable|exists:comp_kpi_templates,id',
            'commission_plan_id' => 'nullable|exists:comp_commission_plans,id',
            'effective_from' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $profile->update($data);
        CompensationAuditService::log('compensation.profile.updated', CompEmployeeProfile::class, $profile->id, $old, $profile->fresh()->toArray());

        return back()->with('success', 'تم تحديث هيكل التعويض');
    }

    protected function authorizeAdmin(): void
    {
        if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }
    }
}

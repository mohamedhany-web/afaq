<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAbsenceReview;
use App\Models\Employee;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\EmployeeComplianceService;
use App\Services\Operations\OperationsKpiService;
use Illuminate\Support\Facades\Auth;

class OperationsTeamController extends Controller
{
    public function __construct(
        protected OperationsKpiService $kpis,
        protected EmployeeComplianceService $compliance,
    ) {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $kpiData = $this->kpis->collect();
        $salesDeptId = CrmEmployeeService::salesDepartment()->id;

        $reps = User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            ->whereHas('employee', fn ($q) => $q->where('department_id', $salesDeptId)->where('status', 'active'))
            ->with('employee.department')
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                $snapshot = $this->compliance->evaluate($user);

                return [
                    'user' => $user,
                    'compliance' => $snapshot['overall_score'] ?? 0,
                    'attendance' => $snapshot['attendance_compliance'] ?? 0,
                    'flags' => $snapshot['flags'] ?? [],
                ];
            });

        $managers = User::role(CrmEmployeeService::LEGACY_MANAGER_ROLES)
            ->whereHas('employee', fn ($q) => $q->where('department_id', $salesDeptId))
            ->orderBy('name')
            ->get();

        return view('operations.team.index', [
            'teamKpis' => $kpiData['groups']['team_performance'] ?? null,
            'reportingKpis' => $kpiData['groups']['reporting_management'] ?? null,
            'revenueKpis' => $kpiData['groups']['revenue_impact'] ?? null,
            'reps' => $reps,
            'managers' => $managers,
            'pendingAbsence' => AttendanceAbsenceReview::where('status', 'pending')->count(),
            'activeEmployees' => Employee::where('status', 'active')->count(),
        ]);
    }
}

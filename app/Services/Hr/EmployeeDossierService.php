<?php

namespace App\Services\Hr;

use App\Models\Attendance;
use App\Models\CustodyAssignment;
use App\Models\Employee;
use App\Models\EmployeeAdminNote;
use App\Models\EmployeeContract;
use App\Models\EmployeeDocument;
use App\Models\User;
use App\Services\Compensation\CompensationKpiScoringService;
use App\Services\Compensation\CompensationPayrollService;
use App\Services\CrmScopeService;
use App\Services\EmployeeComplianceService;
use App\Services\EmployeeRoleService;
use App\Services\EmployeeScheduleService;
use App\Services\MarketingScopeService;
use App\Services\OperationsScopeService;
use Carbon\Carbon;

class EmployeeDossierService
{
    public function __construct(
        protected EmployeeComplianceService $compliance,
        protected EmployeeScheduleService $schedule,
        protected CompensationPayrollService $payroll,
        protected CompensationKpiScoringService $kpiScoring,
    ) {}

    public function canView(User $viewer, Employee $employee): bool
    {
        if ($viewer->hasRole(['super_admin', 'admin']) || $viewer->canAccessHr()) {
            return true;
        }

        if ($employee->user_id && (int) $employee->user_id === (int) $viewer->id) {
            return true;
        }

        if ($viewer->canAccessCrm() && CrmScopeService::for($viewer)->canViewEmployee($employee)) {
            return true;
        }

        if ($viewer->canAccessMarketing()) {
            $ids = MarketingScopeService::for($viewer)->teamUserIds();

            return in_array((int) $employee->user_id, array_map('intval', $ids), true);
        }

        if ($viewer->canAccessOperations()) {
            return OperationsScopeService::for($viewer)->employeesQuery()
                ->where('employees.id', $employee->id)
                ->exists();
        }

        return $viewer->can('view-employees');
    }

    public function canManageNotes(User $viewer): bool
    {
        return $viewer->canAccessHr() || $viewer->hasRole(['super_admin', 'admin', 'sales_manager']);
    }

    public function canManageDocuments(User $viewer, Employee $employee): bool
    {
        return $viewer->canAccessHr()
            || $viewer->hasRole(['super_admin', 'admin'])
            || ($employee->user_id && (int) $employee->user_id === (int) $viewer->id && $viewer->can('view-employees'));
    }

    public function build(Employee $employee, ?Carbon $periodStart = null, ?Carbon $periodEnd = null): array
    {
        $periodStart ??= now()->startOfMonth();
        $periodEnd ??= now()->endOfDay();

        $employee->load(['user.roles', 'department', 'reportsTo']);

        $documents = EmployeeDocument::where('employee_id', $employee->id)
            ->with('uploadedBy')
            ->orderByDesc('created_at')
            ->get();

        $cv = $documents->firstWhere('document_type', 'resume');

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderByDesc('date')
            ->limit(60)
            ->get();

        $attendanceSummary = [
            'total' => $attendances->count(),
            'present' => $attendances->whereIn('status', ['present'])->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'total_hours' => round((float) $attendances->sum('total_hours'), 1),
        ];

        $compliance = null;
        $kpi = null;

        if ($employee->user) {
            $compliance = $this->compliance->evaluate($employee->user, $periodStart, $periodEnd);

            try {
                $period = $this->payroll->currentPeriod();
                $kpi = $this->kpiScoring->evaluateUser($employee->user, $period);
            } catch (\Throwable) {
                $kpi = null;
            }
        }

        $notes = EmployeeAdminNote::where('employee_id', $employee->id)
            ->with('author')
            ->orderByDesc('created_at')
            ->get();

        return [
            'employee' => $employee,
            'roleMeta' => EmployeeRoleService::resolve($employee),
            'personal' => [
                'full_name' => trim($employee->first_name . ' ' . $employee->last_name),
                'email' => $employee->email,
                'phone' => $employee->phone,
                'national_id' => $employee->national_id,
                'address' => $employee->address,
                'emergency_contact' => $employee->emergency_contact,
                'emergency_phone' => $employee->emergency_phone,
            ],
            'employment' => [
                'employee_id' => $employee->employee_id,
                'position' => $employee->position,
                'department' => $employee->department?->name,
                'employment_type' => $employee->employment_type,
                'hire_date' => $employee->hire_date,
                'salary' => $employee->salary,
                'status' => $employee->status,
                'reports_to' => $employee->reportsTo?->name,
                'schedule' => $this->schedule->scheduleLabel($employee),
                'off_days' => $this->schedule->offDaysLabel($employee),
                'daily_hours' => $this->schedule->requiredDailyHours($employee),
            ],
            'cv' => $cv,
            'documents' => $documents,
            'contracts' => EmployeeContract::where('employee_id', $employee->id)
                ->orderByDesc('start_date')
                ->limit(10)
                ->get(),
            'custody' => CustodyAssignment::where('employee_id', $employee->id)
                ->orderByDesc('issued_at')
                ->limit(10)
                ->get(),
            'attendances' => $attendances,
            'attendance_summary' => $attendanceSummary,
            'performance' => [
                'compliance' => $compliance,
                'kpi' => $kpi,
            ],
            'notes' => $notes,
            'period' => ['start' => $periodStart, 'end' => $periodEnd],
        ];
    }
}

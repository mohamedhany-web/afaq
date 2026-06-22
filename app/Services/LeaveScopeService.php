<?php

namespace App\Services;

use App\Models\Employee;
use App\Services\CrmEmployeeService;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class LeaveScopeService
{
    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function mode(): string
    {
        if ($this->user->canAccessOperations()) {
            return 'operations';
        }

        if ($this->user->hasRole(['super_admin', 'admin', 'hr'])) {
            return 'admin';
        }

        if ($this->isLineManager()) {
            return 'manager';
        }

        return 'self';
    }

    public function isLineManager(): bool
    {
        if ($this->user->hasRole(['super_admin', 'admin', 'hr'])) {
            return false;
        }

        if ($this->user->can('approve-leaves') && $this->user->isSalesManager()) {
            return true;
        }

        if ($this->user->can('approve-leaves') && $this->user->isMarketingManager()) {
            return true;
        }

        return false;
    }

    public function canApprove(): bool
    {
        if ($this->user->canAccessOperations()) {
            return true;
        }

        return $this->user->can('approve-leaves')
            && ($this->user->hasRole(['super_admin', 'admin', 'hr']) || $this->isLineManager());
    }

    public function canApproveLeave(Leave $leave): bool
    {
        if ($leave->status !== 'pending') {
            return false;
        }

        if ($this->user->canAccessOperations()) {
            return true;
        }

        if (!$this->canApprove()) {
            return false;
        }

        if ($this->user->hasRole(['super_admin', 'admin', 'hr'])) {
            return true;
        }

        $employeeUserId = $leave->employee?->user_id;

        return $employeeUserId
            && in_array((int) $employeeUserId, $this->managedUserIds(), true)
            && (int) $employeeUserId !== (int) $this->user->id;
    }

    /** @return int[] */
    public function managedUserIds(): array
    {
        if ($this->user->isSalesManager()
            || $this->user->hasRole(CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES)
            || $this->user->hasRole(CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES)) {
            return CrmScopeService::for($this->user)->managedTeamMemberUserIds();
        }

        if ($this->user->isMarketingManager()) {
            return MarketingScopeService::for($this->user)->teamUserIds();
        }

        return [];
    }

    public function leavesQuery(): Builder
    {
        $query = Leave::query()->with(['employee.user', 'approvedBy']);

        if ($this->mode() === 'admin' || $this->mode() === 'operations') {
            return $query;
        }

        if ($this->mode() === 'manager') {
            $employeeIds = Employee::query()
                ->whereIn('user_id', $this->managedUserIds())
                ->pluck('id');

            return $query->where(function ($q) use ($employeeIds) {
                $q->whereIn('employee_id', $employeeIds);
                if ($own = $this->employee()?->id) {
                    $q->orWhere('employee_id', $own);
                }
            });
        }

        $employee = $this->employee();

        return $employee
            ? $query->where('employee_id', $employee->id)
            : $query->whereRaw('1 = 0');
    }

    public function employee(): ?Employee
    {
        return Employee::where('user_id', $this->user->id)->first();
    }

    public function stats(): array
    {
        $year = now()->year;
        $employee = $this->employee();

        if ($this->mode() === 'admin' || $this->mode() === 'operations') {
            return [
                'pending' => Leave::where('status', 'pending')->count(),
                'approved_month' => Leave::approved()->whereMonth('start_date', now()->month)->whereYear('start_date', $year)->count(),
                'rejected_month' => Leave::where('status', 'rejected')->whereMonth('start_date', now()->month)->whereYear('start_date', $year)->count(),
                'total_days_year' => Leave::approved()->whereYear('start_date', $year)->sum('total_days'),
                'remaining_annual' => null,
            ];
        }

        if ($this->mode() === 'manager') {
            $teamEmployeeIds = Employee::whereIn('user_id', $this->managedUserIds())->pluck('id');

            return [
                'pending' => Leave::where('status', 'pending')->whereIn('employee_id', $teamEmployeeIds)->count(),
                'approved_month' => Leave::approved()->whereIn('employee_id', $teamEmployeeIds)->whereMonth('start_date', now()->month)->whereYear('start_date', $year)->count(),
                'rejected_month' => Leave::where('status', 'rejected')->whereIn('employee_id', $teamEmployeeIds)->whereMonth('start_date', now()->month)->whereYear('start_date', $year)->count(),
                'total_days_year' => $employee
                    ? Leave::approved()->where('employee_id', $employee->id)->whereYear('start_date', $year)->sum('total_days')
                    : 0,
                'remaining_annual' => $employee ? $this->remainingAnnualDays($employee) : 0,
            ];
        }

        if (!$employee) {
            return ['pending' => 0, 'approved_month' => 0, 'rejected_month' => 0, 'total_days_year' => 0, 'remaining_annual' => 0];
        }

        return [
            'pending' => Leave::where('employee_id', $employee->id)->where('status', 'pending')->count(),
            'approved_month' => Leave::approved()->where('employee_id', $employee->id)->whereMonth('start_date', now()->month)->whereYear('start_date', $year)->count(),
            'rejected_month' => Leave::where('employee_id', $employee->id)->where('status', 'rejected')->whereMonth('start_date', now()->month)->whereYear('start_date', $year)->count(),
            'total_days_year' => Leave::approved()->where('employee_id', $employee->id)->whereYear('start_date', $year)->sum('total_days'),
            'remaining_annual' => $this->remainingAnnualDays($employee),
        ];
    }

    public function remainingAnnualDays(Employee $employee): int
    {
        $used = (int) Leave::where('employee_id', $employee->id)
            ->where('leave_type', 'annual')
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        return max(0, (int) config('leaves.annual_limit_days', 21) - $used);
    }
}

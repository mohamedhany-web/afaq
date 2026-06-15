<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\ExitPermit;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\CrmScopeService;
use App\Services\MarketingScopeService;
use Illuminate\Database\Eloquent\Builder;

class ExitPermitScopeService
{
    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function mode(): string
    {
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
        return $this->user->can('approve-leaves')
            && ($this->user->hasRole(['super_admin', 'admin', 'hr']) || $this->isLineManager());
    }

    public function canApprovePermit(ExitPermit $permit): bool
    {
        if (!$this->canApprove() || !$permit->isPending()) {
            return false;
        }

        if ($this->user->hasRole(['super_admin', 'admin', 'hr'])) {
            return true;
        }

        $employeeUserId = $permit->employee?->user_id;

        return $employeeUserId
            && in_array((int) $employeeUserId, $this->managedUserIds(), true)
            && (int) $employeeUserId !== (int) $this->user->id;
    }

    public function canRequest(): bool
    {
        return $this->employee() !== null
            && ($this->user->can('create-leaves') || $this->user->hasRole(['hr', 'admin', 'super_admin']));
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

    public function permitsQuery(): Builder
    {
        $query = ExitPermit::query()->with(['employee.user', 'employee.department', 'approvedBy']);

        if ($this->mode() === 'admin') {
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
        $month = now()->month;
        $year = now()->year;

        if ($this->mode() === 'admin') {
            return [
                'pending' => ExitPermit::pending()->count(),
                'approved_month' => ExitPermit::approved()
                    ->whereMonth('permit_date', $month)
                    ->whereYear('permit_date', $year)
                    ->count(),
                'rejected_month' => ExitPermit::where('status', 'rejected')
                    ->whereMonth('permit_date', $month)
                    ->whereYear('permit_date', $year)
                    ->count(),
            ];
        }

        if ($this->mode() === 'manager') {
            $teamEmployeeIds = Employee::whereIn('user_id', $this->managedUserIds())->pluck('id');

            return [
                'pending' => ExitPermit::pending()->whereIn('employee_id', $teamEmployeeIds)->count(),
                'approved_month' => ExitPermit::approved()
                    ->whereIn('employee_id', $teamEmployeeIds)
                    ->whereMonth('permit_date', $month)
                    ->whereYear('permit_date', $year)
                    ->count(),
                'rejected_month' => ExitPermit::where('status', 'rejected')
                    ->whereIn('employee_id', $teamEmployeeIds)
                    ->whereMonth('permit_date', $month)
                    ->whereYear('permit_date', $year)
                    ->count(),
            ];
        }

        $employee = $this->employee();
        if (!$employee) {
            return ['pending' => 0, 'approved_month' => 0, 'rejected_month' => 0];
        }

        return [
            'pending' => ExitPermit::where('employee_id', $employee->id)->pending()->count(),
            'approved_month' => ExitPermit::approved()
                ->where('employee_id', $employee->id)
                ->whereMonth('permit_date', $month)
                ->whereYear('permit_date', $year)
                ->count(),
            'rejected_month' => ExitPermit::where('employee_id', $employee->id)
                ->where('status', 'rejected')
                ->whereMonth('permit_date', $month)
                ->whereYear('permit_date', $year)
                ->count(),
        ];
    }
}

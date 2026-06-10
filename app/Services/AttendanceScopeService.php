<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AttendanceScopeService
{
    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function mode(): string
    {
        if ($this->user->hasRole(['super_admin', 'admin', 'hr', 'operation_manager'])) {
            return 'admin';
        }

        if ($this->user->isSalesDepartmentManager()
            || $this->user->isSalesTeamLeader()
            || $this->user->isMarketingManager()
            || $this->user->hasRole(MarketingEmployeeService::LEGACY_MANAGER_ROLES)) {
            return 'manager';
        }

        return 'self';
    }

    public function canViewRoster(): bool
    {
        return $this->mode() !== 'self' || $this->employee() !== null;
    }

    public function canViewAllEmployees(): bool
    {
        return $this->mode() === 'admin';
    }

    public function visibleEmployeesQuery(): Builder
    {
        $query = Employee::query()
            ->where('status', 'active')
            ->with(['department', 'user.roles']);

        if ($this->mode() === 'admin') {
            return $query;
        }

        if ($this->mode() === 'manager') {
            $userIds = $this->managedUserIds();

            return $query->whereIn('user_id', $userIds);
        }

        $employee = $this->employee();

        return $employee
            ? $query->where('id', $employee->id)
            : $query->whereRaw('1 = 0');
    }

    /** @return int[] */
    public function managedUserIds(): array
    {
        $ids = collect([(int) $this->user->id]);

        if ($this->user->isSalesManager()
            || $this->user->hasRole(CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES)
            || $this->user->hasRole(CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES)) {
            $ids->push(...CrmScopeService::for($this->user)->managedTeamMemberUserIds());
        }

        if ($this->user->isMarketingManager() || $this->user->hasRole(MarketingEmployeeService::LEGACY_MANAGER_ROLES)) {
            $ids->push(...MarketingScopeService::for($this->user)->teamUserIds());
        }

        return $ids->unique()->values()->all();
    }

    public function employee(): ?Employee
    {
        return Employee::where('user_id', $this->user->id)->first();
    }

    public function departmentsForFilter()
    {
        $deptIds = $this->visibleEmployeesQuery()
            ->whereNotNull('department_id')
            ->distinct()
            ->pluck('department_id');

        return \App\Models\Department::whereIn('id', $deptIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}

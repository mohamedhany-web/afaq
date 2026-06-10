<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\OperationsPeriodReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OperationsScopeService
{
    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function isAdminScope(): bool
    {
        return $this->user->hasRole(['super_admin', 'admin']);
    }

    public function operationsManagerIds(): array
    {
        return User::role(OperationsEmployeeService::LEGACY_MANAGER_ROLES)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function employeesQuery(): Builder
    {
        $deptId = OperationsEmployeeService::operationsDepartment()->id;

        return Employee::query()
            ->where('department_id', $deptId)
            ->whereHas('user.roles', fn ($r) => $r->whereIn('name', OperationsEmployeeService::LEGACY_MANAGER_ROLES))
            ->with(['user.roles', 'department']);
    }

    public function reportsQuery(): Builder
    {
        $query = OperationsPeriodReport::query();

        if ($this->isAdminScope()) {
            return $query;
        }

        return $query->where('user_id', $this->user->id);
    }
}

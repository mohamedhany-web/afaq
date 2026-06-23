<?php

namespace App\Policies;

use App\Models\DepartmentReport;
use App\Models\User;

class DepartmentReportPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->adminBypassUnlessDenied('view-reports') || $user->can('view-reports')) {
            return true;
        }

        return DepartmentAccess::isDepartmentManager($user);
    }

    public function view(User $user, DepartmentReport $report): bool
    {
        if ($user->adminBypassUnlessDenied('view-reports') || $user->can('view-reports')) {
            return true;
        }

        $managedDeptId = DepartmentAccess::managedDepartmentId($user);
        return $managedDeptId && (int) $report->department_id === (int) $managedDeptId;
    }

    public function create(User $user): bool
    {
        if ($user->adminBypassUnlessDenied('generate-reports')) {
            return true;
        }

        return DepartmentAccess::isDepartmentManager($user);
    }

    public function update(User $user, DepartmentReport $report): bool
    {
        if ($user->adminBypassUnlessDenied('generate-reports')) {
            return true;
        }

        $managedDeptId = DepartmentAccess::managedDepartmentId($user);
        if (!$managedDeptId || (int) $report->department_id !== (int) $managedDeptId) {
            return false;
        }

        // If submitted, manager can no longer edit
        return $report->status !== 'submitted';
    }

    public function submit(User $user, DepartmentReport $report): bool
    {
        return $this->update($user, $report);
    }
}


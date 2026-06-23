<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Policies\Concerns\RespectsPermissionOverrides;

class ProjectPolicy
{
    use RespectsPermissionOverrides;

    public function viewAny(User $user): bool
    {
        if ($this->adminUnlessDenied($user, 'view-all-projects')) {
            return true;
        }

        if ($user->can('view-all-projects')) {
            return true;
        }

        if (DepartmentAccess::isDepartmentManager($user)) {
            return ! $user->isPermissionExplicitlyDisabled('view-all-projects');
        }

        return $user->can('view-own-projects');
    }

    public function view(User $user, Project $project): bool
    {
        if ($this->adminUnlessDenied($user, 'view-all-projects') || $user->can('view-all-projects')) {
            return true;
        }

        $managedDeptId = DepartmentAccess::managedDepartmentId($user);
        if ($managedDeptId) {
            return (int) $project->department_id === (int) $managedDeptId;
        }

        if ($user->can('view-own-projects')) {
            if ((int) $project->project_manager_id === (int) $user->id) {
                return true;
            }

            return $project->teamMembers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($this->adminUnlessDenied($user, 'create-projects')) {
            return true;
        }

        return $user->can('create-projects');
    }

    public function update(User $user, Project $project): bool
    {
        if ($this->adminUnlessDenied($user, 'edit-projects')) {
            return true;
        }

        if ($user->can('edit-projects')) {
            return true;
        }

        return $user->can('edit-own-projects')
            && (int) $project->project_manager_id === (int) $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        if (! $project->isDeletable()) {
            return false;
        }

        if ($this->adminUnlessDenied($user, 'delete-projects')) {
            return true;
        }

        return $user->can('delete-projects');
    }
}

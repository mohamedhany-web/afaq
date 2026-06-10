<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        // Admins can view everything
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return true;
        }

        // Existing permission model
        if ($user->can('view-all-projects')) {
            return true;
        }

        // Department manager can view department projects
        if (DepartmentAccess::isDepartmentManager($user)) {
            return true;
        }

        // Team members / PMs handled by query filtering in controllers
        return $user->can('view-own-projects');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin') || $user->can('view-all-projects')) {
            return true;
        }

        // Department manager: only within their department
        $managedDeptId = DepartmentAccess::managedDepartmentId($user);
        if ($managedDeptId) {
            return (int) $project->department_id === (int) $managedDeptId;
        }

        // PM / team member
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
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->usesCrmWorkspace()) {
            return true;
        }

        return $user->hasPermissionTo('create-projects');
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->usesCrmWorkspace() || $user->usesMarketingWorkspace()) {
            return true;
        }

        if ($user->can('edit-projects')) {
            return true;
        }

        return $user->can('edit-own-projects') && (int) $project->project_manager_id === (int) $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        if (!$project->isDeletable()) {
            return false;
        }

        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->usesCrmWorkspace() || $user->usesMarketingWorkspace()) {
            return true;
        }

        return $user->hasPermissionTo('edit-projects');
    }
}


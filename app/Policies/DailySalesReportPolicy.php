<?php

namespace App\Policies;

use App\Models\DailySalesReport;
use App\Models\User;
use App\Services\CrmRoleResolver;
use App\Services\CrmScopeService;

class DailySalesReportPolicy
{
    public function viewAny(User $user): bool
    {
        if (!$user->canAccessCrm()) {
            return false;
        }

        return CrmRoleResolver::for($user)->isRep()
            || CrmRoleResolver::for($user)->isManager()
            || CrmRoleResolver::for($user)->isAdmin();
    }

    public function view(User $user, DailySalesReport $report): bool
    {
        if (!$user->canAccessCrm()) {
            return false;
        }

        $role = CrmRoleResolver::for($user);

        if ($role->isRep() && (int) $report->user_id === (int) $user->id) {
            return true;
        }

        $scope = CrmScopeService::for($user);

        if ($role->isAdmin()) {
            return $report->isSubmitted();
        }

        if ($role->isManager()) {
            return $report->isSubmitted()
                && in_array($report->user_id, $scope->managedTeamMemberUserIds(), true);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return CrmRoleResolver::for($user)->canCreateDailySalesReport();
    }

    public function update(User $user, DailySalesReport $report): bool
    {
        return CrmRoleResolver::for($user)->canCreateDailySalesReport()
            && (int) $report->user_id === (int) $user->id
            && $report->isDraft();
    }

    public function submit(User $user, DailySalesReport $report): bool
    {
        return $this->update($user, $report);
    }
}

<?php

namespace App\Policies;

use App\Models\MarketingPeriodReport;
use App\Models\User;
use App\Services\MarketingRoleResolver;
use App\Services\MarketingScopeService;

class MarketingPeriodReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessMarketing();
    }

    public function view(User $user, MarketingPeriodReport $report): bool
    {
        if (!$user->canAccessMarketing()) {
            return false;
        }

        if ((int) $report->user_id === (int) $user->id) {
            return true;
        }

        $resolver = MarketingRoleResolver::for($user);

        if ($resolver->isAdmin()) {
            return $report->isSubmitted();
        }

        if ($resolver->isManager()) {
            $teamIds = MarketingScopeService::for($user)->teamUserIds();

            return $report->isSubmitted() && in_array($report->user_id, $teamIds, true);
        }

        return false;
    }

    public function update(User $user, MarketingPeriodReport $report): bool
    {
        return (int) $report->user_id === (int) $user->id && $report->isDraft();
    }

    public function submit(User $user, MarketingPeriodReport $report): bool
    {
        return $this->update($user, $report);
    }
}

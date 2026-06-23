<?php

namespace App\Policies;

use App\Models\OperationsPeriodReport;
use App\Models\User;

class OperationsPeriodReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessOperations();
    }

    public function view(User $user, OperationsPeriodReport $report): bool
    {
        if (!$user->canAccessOperations()) {
            return false;
        }

        if ((int) $report->user_id === (int) $user->id) {
            return true;
        }

        return $user->hasRole(['super_admin', 'admin']) && $report->isSubmitted();
    }

    public function update(User $user, OperationsPeriodReport $report): bool
    {
        return (int) $report->user_id === (int) $user->id && $report->isDraft();
    }

    public function submit(User $user, OperationsPeriodReport $report): bool
    {
        return $this->update($user, $report);
    }

    public function annotate(User $user, OperationsPeriodReport $report): bool
    {
        if ($user->adminBypassUnlessDenied('annotate-operations-reports') || $user->can('annotate-operations-reports')) {
            return $report->isSubmitted();
        }

        return false;
    }
}

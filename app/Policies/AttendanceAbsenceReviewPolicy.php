<?php

namespace App\Policies;

use App\Models\AttendanceAbsenceReview;
use App\Models\User;
use App\Services\OrganizationalHierarchyService;

class AttendanceAbsenceReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return app(OrganizationalHierarchyService::class)->canReviewAttendance($user)
            || $user->can('view-attendance')
            || $user->canAccessOperations();
    }

    public function review(User $user, AttendanceAbsenceReview $review): bool
    {
        if (!$review->isPending()) {
            return false;
        }

        if ($user->canAccessOperations() || $user->canAccessHr()) {
            return true;
        }

        return app(OrganizationalHierarchyService::class)->canReviewAttendance($user);
    }

    public function revoke(User $user, AttendanceAbsenceReview $review): bool
    {
        if (! in_array($review->status, [
            AttendanceAbsenceReview::STATUS_CONFIRMED_ABSENT,
            AttendanceAbsenceReview::STATUS_AUTO_CONFIRMED,
        ], true)) {
            return false;
        }

        return app(OrganizationalHierarchyService::class)->canReviewAttendance($user)
            || $user->canAccessHr()
            || $user->canAccessOperations();
    }
}

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
            || $user->can('view-attendance');
    }

    public function review(User $user, AttendanceAbsenceReview $review): bool
    {
        if (!$review->isPending()) {
            return false;
        }

        return app(OrganizationalHierarchyService::class)->canReviewAttendance($user);
    }
}

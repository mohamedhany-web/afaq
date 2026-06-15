<?php

namespace App\Policies;

use App\Models\AttendanceCheckoutReview;
use App\Models\User;
use App\Services\OrganizationalHierarchyService;

class AttendanceCheckoutReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return app(OrganizationalHierarchyService::class)->canReviewAttendance($user)
            || $user->canAccessOperations();
    }

    public function review(User $user, AttendanceCheckoutReview $review): bool
    {
        return $this->viewAny($user) && $review->isPending();
    }

    public function revoke(User $user, AttendanceCheckoutReview $review): bool
    {
        return $this->viewAny($user) && $review->isApproved();
    }
}

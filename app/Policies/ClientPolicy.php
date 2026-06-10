<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Services\CrmRecordApprovalService;

class ClientPolicy
{
    public function __construct(protected CrmRecordApprovalService $approval) {}

    public function viewAny(User $user): bool
    {
        return $this->approval->canSubmitChanges($user)
            || $user->hasRole(['super_admin', 'admin', 'hr']);
    }

    public function view(User $user, Client $client): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->approval->canSubmitChanges($user);
    }

    public function update(User $user, Client $client): bool
    {
        return $this->approval->canSubmitChanges($user);
    }

    public function delete(User $user, Client $client): bool
    {
        if ($client->projects()->count() > 0 || $client->sales()->count() > 0) {
            return false;
        }

        return $this->approval->canSubmitChanges($user);
    }
}

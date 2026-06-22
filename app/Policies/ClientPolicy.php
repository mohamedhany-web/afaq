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
        if ($this->approval->executesDirectly($user)) {
            return true;
        }

        if (! $user->can('view-clients')) {
            return false;
        }

        return $this->approval->canSubmitChanges($user) || $user->canAccessOperations();
    }

    public function view(User $user, Client $client): bool
    {
        if ($this->approval->executesDirectly($user)) {
            return true;
        }

        if (! $user->can('view-clients')) {
            return false;
        }

        if ($user->canAccessOperations()) {
            return true;
        }

        if ($user->usesMarketingWorkspace()) {
            return \App\Services\MarketingScopeService::for($user)
                ->leadsQuery()
                ->where('id', $client->id)
                ->exists();
        }

        return \App\Services\CrmScopeService::for($user)
            ->clientsQuery()
            ->where('id', $client->id)
            ->exists();
    }

    public function viewFullDetails(User $user, Client $client): bool
    {
        return $this->view($user, $client) && $this->approval->executesDirectly($user);
    }

    public function viewActivityLog(User $user, Client $client): bool
    {
        return $this->view($user, $client)
            && ($user->can('edit-clients') || $user->can('delete-clients') || $user->canAccessOperations() || $this->approval->executesDirectly($user));
    }

    public function viewDeletionLog(User $user): bool
    {
        return $user->can('delete-clients') || $user->canAccessOperations() || $this->approval->executesDirectly($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Client $client): bool
    {
        if (! $this->view($user, $client)) {
            return false;
        }

        if (! $user->can('edit-clients') && ! $this->approval->executesDirectly($user)) {
            return false;
        }

        return $this->approval->canSubmitChanges($user);
    }

    public function delete(User $user, Client $client): bool
    {
        if ($client->projects()->count() > 0 || $client->sales()->count() > 0) {
            return false;
        }

        if (! $this->view($user, $client)) {
            return false;
        }

        if (! $user->can('delete-clients') && ! $this->approval->executesDirectly($user)) {
            return false;
        }

        return $this->approval->canSubmitChanges($user);
    }

    public function transfer(User $user, Client $client): bool
    {
        if (! $this->view($user, $client)) {
            return false;
        }

        if ($this->approval->executesDirectly($user) || $user->canAccessOperations()) {
            return true;
        }

        return $user->can('transfer-clients');
    }

    public function bulkUpdate(User $user): bool
    {
        return $user->can('edit-clients') || $user->can('transfer-clients')
            || $user->canAccessOperations() || $this->approval->executesDirectly($user);
    }

    public function bulkDelete(User $user): bool
    {
        return $user->can('delete-clients') || $user->canAccessOperations() || $this->approval->executesDirectly($user);
    }
}

<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Services\CrmRecordApprovalService;

class ClientPolicy
{
    public function __construct(protected CrmRecordApprovalService $approval) {}

    /** قائمة كل العملاء المدخلين — الإدارة العليا فقط */
    public function viewAny(User $user): bool
    {
        return $this->approval->executesDirectly($user);
    }

    /** عرض عميل واحد ضمن نطاق الصلاحيات */
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

    /** ملف العميل الكامل (بيانات حساسة وتصنيفات) — الإدارة فقط */
    public function viewFullDetails(User $user, Client $client): bool
    {
        return $this->view($user, $client) && $this->approval->executesDirectly($user);
    }

    public function create(User $user): bool
    {
        return $this->approval->canSubmitChanges($user);
    }

    public function update(User $user, Client $client): bool
    {
        if (! $this->view($user, $client)) {
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

        return $this->approval->canSubmitChanges($user);
    }
}

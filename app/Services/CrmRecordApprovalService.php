<?php

namespace App\Services;

use App\Models\User;

class CrmRecordApprovalService
{
    /** تنفيذ مباشر بدون موافقة — الإدارة العليا فقط */
    public function executesDirectly(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    /** يستطيع إرسال طلبات إضافة/تعديل/حذف (مبيعات وتسويق — ليس عمليات) */
    public function canSubmitChanges(User $user): bool
    {
        if ($this->executesDirectly($user)) {
            return true;
        }

        if ($user->usesOperationsWorkspace()) {
            return false;
        }

        return $user->usesCrmWorkspace() || $user->usesMarketingWorkspace();
    }

    /** يحتاج موافقة الإدارة قبل التنفيذ */
    public function requiresApproval(User $user): bool
    {
        return $this->canSubmitChanges($user) && !$this->executesDirectly($user);
    }

    public function canApproveProjects(User $user): bool
    {
        return $this->executesDirectly($user)
            || $user->hasPermissionTo('approve-project-changes');
    }

    public function canApproveClients(User $user): bool
    {
        return $this->executesDirectly($user)
            || $user->hasPermissionTo('approve-client-changes');
    }
}

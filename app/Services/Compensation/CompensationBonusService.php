<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompBonusRule;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\User;

class CompensationBonusService
{
    public function pendingForUser(User $user, ?CompPayrollPeriod $period = null)
    {
        $query = CompAdjustment::query()->where('user_id', $user->id)->where('type', 'bonus');

        if ($period) {
            $query->where('period_id', $period->id);
        }

        return $query->latest()->get();
    }

    public function createManual(User $target, float $amount, string $reason, User $requester, CompPayrollPeriod $period, ?CompBonusRule $rule = null): CompAdjustment
    {
        $status = $requester->hasRole(['super_admin', 'admin']) ? 'approved' : 'pending';

        $adj = CompAdjustment::create([
            'type' => 'bonus',
            'user_id' => $target->id,
            'period_id' => $period->id,
            'rule_id' => $rule?->id,
            'amount' => $amount,
            'reason' => $reason,
            'status' => $status,
            'requested_by' => $requester->id,
            'reviewed_by' => $status === 'approved' ? $requester->id : null,
            'reviewed_at' => $status === 'approved' ? now() : null,
        ]);

        CompensationAuditService::log('bonus.created', CompAdjustment::class, $adj->id, null, $adj->toArray());

        return $adj;
    }
}

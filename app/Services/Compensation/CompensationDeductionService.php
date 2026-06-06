<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompDeductionRule;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\User;

class CompensationDeductionService
{
    public function pendingForUser(User $user, ?CompPayrollPeriod $period = null)
    {
        $query = CompAdjustment::query()->where('user_id', $user->id)->where('type', 'deduction');

        if ($period) {
            $query->where('period_id', $period->id);
        }

        return $query->latest()->get();
    }

    public function createManual(User $target, float $amount, string $reason, User $requester, CompPayrollPeriod $period, ?CompDeductionRule $rule = null): CompAdjustment
    {
        $status = $requester->hasRole(['super_admin', 'admin']) ? 'approved' : 'pending';

        $adj = CompAdjustment::create([
            'type' => 'deduction',
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

        CompensationAuditService::log('deduction.created', CompAdjustment::class, $adj->id, null, $adj->toArray());

        return $adj;
    }

    public function approve(CompAdjustment $adjustment, User $reviewer, ?string $notes = null): CompAdjustment
    {
        $old = $adjustment->only(['status']);
        $adjustment->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
        CompensationAuditService::log('deduction.approved', CompAdjustment::class, $adjustment->id, $old, $adjustment->only(['status']));

        return $adjustment;
    }

    public function reject(CompAdjustment $adjustment, User $reviewer, ?string $notes = null): CompAdjustment
    {
        $old = $adjustment->only(['status']);
        $adjustment->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
        CompensationAuditService::log('deduction.rejected', CompAdjustment::class, $adjustment->id, $old, $adjustment->only(['status']));

        return $adjustment;
    }
}

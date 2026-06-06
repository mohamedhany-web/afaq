<?php

namespace App\Http\Controllers\Crm\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Compensation\CompAdjustment;
use App\Services\Compensation\CompensationAuditService;
use App\Services\Compensation\CompensationPayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompAdjustmentController extends Controller
{
    public function store(Request $request, CompensationPayrollService $payroll)
    {
        $data = $request->validate([
            'type' => 'required|in:bonus,deduction',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:2000',
        ]);

        $actor = Auth::user();
        $this->authorizeRecommend($actor, (int) $data['user_id']);

        $period = $payroll->currentPeriod();
        $status = $actor->hasRole(['super_admin', 'admin']) ? 'approved' : 'pending';

        $adj = CompAdjustment::create([
            'type' => $data['type'],
            'user_id' => $data['user_id'],
            'period_id' => $period->id,
            'amount' => $data['amount'],
            'reason' => $data['reason'],
            'status' => $status,
            'requested_by' => $actor->id,
            'reviewed_by' => $status === 'approved' ? $actor->id : null,
            'reviewed_at' => $status === 'approved' ? now() : null,
        ]);

        CompensationAuditService::log('adjustment.created', CompAdjustment::class, $adj->id, null, $adj->toArray());

        if ($status === 'approved') {
            $payroll->calculateRun($adj->user, $period);
        }

        return back()->with('success', $status === 'approved' ? 'تم تطبيق التعديل' : 'تم إرسال الطلب للاعتماد');
    }

    public function review(Request $request, CompAdjustment $adjustment, CompensationPayrollService $payroll)
    {
        if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }

        $request->validate(['action' => 'required|in:approve,reject', 'review_notes' => 'nullable|string']);

        $old = $adjustment->only(['status']);
        $adjustment->update([
            'status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        CompensationAuditService::log('adjustment.' . $request->action, CompAdjustment::class, $adjustment->id, $old, $adjustment->fresh()->only(['status']));

        if ($request->action === 'approve' && $adjustment->period) {
            $payroll->calculateRun($adjustment->user, $adjustment->period);
        }

        return back()->with('success', 'تمت مراجعة الطلب');
    }

    protected function authorizeRecommend($actor, int $targetUserId): void
    {
        if ($actor->hasRole(['super_admin', 'admin'])) {
            return;
        }

        $scope = \App\Services\CrmScopeService::for($actor);
        if ($scope->isManagerScope() && in_array($targetUserId, $scope->managedTeamMemberUserIds(), true)) {
            return;
        }

        if ((int) $actor->id === $targetUserId) {
            abort(403, 'لا يمكنك إنشاء خصم على نفسك');
        }

        abort(403);
    }
}

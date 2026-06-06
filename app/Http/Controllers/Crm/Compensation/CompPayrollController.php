<?php

namespace App\Http\Controllers\Crm\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\Compensation\CompPayrollRun;
use App\Services\Compensation\CompensationPayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompPayrollController extends Controller
{
    public function recalculate(Request $request, CompensationPayrollService $payroll)
    {
        $user = Auth::user();
        $period = $payroll->periodForMonth(
            (int) $request->get('year', now()->year),
            (int) $request->get('month', now()->month),
        );

        if ($user->hasRole(['super_admin', 'admin'])) {
            foreach (CompEmployeeProfile::where('is_active', true)->pluck('user_id') as $userId) {
                $payroll->calculateRun(\App\Models\User::find($userId), $period);
            }

            return back()->with('success', 'تم إعادة حساب الرواتب للفترة');
        }

        $ids = $payroll->scopedUserIds($user);
        foreach ($ids as $id) {
            $payroll->calculateRun(\App\Models\User::find($id), $period);
        }

        return back()->with('success', 'تم تحديث الحسابات');
    }

    public function approve(CompPayrollRun $run, CompensationPayrollService $payroll)
    {
        if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }

        $payroll->approveRun($run, Auth::user());

        return back()->with('success', 'تم اعتماد كشف الراتب');
    }

    public function show(CompPayrollRun $run)
    {
        $user = Auth::user();
        if (!$user->hasRole(['super_admin', 'admin']) && (int) $run->user_id !== (int) $user->id) {
            $scope = \App\Services\CrmScopeService::for($user);
            if (!$scope->isManagerScope() || !in_array($run->user_id, $scope->managedTeamMemberUserIds(), true)) {
                abort(403);
            }
        }

        $run->load(['user', 'period', 'lineItems']);

        return view('crm.compensation.payroll.show', compact('run'));
    }
}

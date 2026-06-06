<?php

namespace App\Http\Controllers\Crm\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\Compensation\CompPayrollRun;
use App\Services\Compensation\CompensationPayrollService;
use App\Services\CrmRoleResolver;
use Illuminate\Support\Facades\Auth;

class CompensationDashboardController extends Controller
{
    public function index(CompensationPayrollService $payroll)
    {
        $user = Auth::user();
        $role = CrmRoleResolver::for($user);
        $period = $payroll->currentPeriod();

        return match ($role->workspace()) {
            CrmRoleResolver::WORKSPACE_ADMIN => $this->adminDashboard($payroll, $period),
            CrmRoleResolver::WORKSPACE_MANAGER => $this->managerDashboard($payroll, $user, $period),
            default => $this->repDashboard($payroll, $user, $period),
        };
    }

    protected function adminDashboard(CompensationPayrollService $payroll, $period)
    {
        foreach (CompEmployeeProfile::where('is_active', true)->with('user')->get() as $profile) {
            if ($profile->user) {
                $payroll->calculateRun($profile->user, $period);
            }
        }

        $runs = CompPayrollRun::with('user')->where('period_id', $period->id)->get();

        return view('crm.compensation.admin.dashboard', [
            'period' => $period,
            'stats' => [
                'total_payroll' => $runs->sum('net_pay'),
                'total_commission' => $runs->sum('commission_total'),
                'total_bonus' => $runs->sum('bonus_total'),
                'total_deduction' => $runs->sum('deduction_total'),
                'employees' => $runs->count(),
                'avg_kpi' => round($runs->avg('kpi_score') ?? 0, 1),
            ],
            'topEarners' => $runs->sortByDesc('net_pay')->take(10)->values(),
            'pendingAdjustments' => CompAdjustment::with(['user', 'requester'])
                ->where('status', 'pending')->latest()->limit(20)->get(),
            'kpiDistribution' => $this->kpiDistribution($runs),
            'runs' => $runs->sortByDesc('net_pay'),
        ]);
    }

    protected function managerDashboard(CompensationPayrollService $payroll, $user, $period)
    {
        $ids = $payroll->scopedUserIds($user);
        $teamRuns = CompPayrollRun::with('user')
            ->where('period_id', $period->id)
            ->whereIn('user_id', $ids)
            ->get();

        $myRun = $payroll->calculateRun($user, $period);

        return view('crm.compensation.manager.dashboard', [
            'period' => $period,
            'myRun' => $myRun,
            'teamRuns' => $teamRuns,
            'pendingAdjustments' => CompAdjustment::with('user')
                ->whereIn('user_id', $ids)
                ->where('status', 'pending')
                ->latest()->get(),
        ]);
    }

    protected function repDashboard(CompensationPayrollService $payroll, $user, $period)
    {
        $run = $payroll->calculateRun($user, $period);
        $history = CompPayrollRun::with('period')
            ->where('user_id', $user->id)
            ->orderByDesc('period_id')
            ->limit(6)
            ->get();

        return view('crm.compensation.rep.dashboard', [
            'period' => $period,
            'run' => $run,
            'profile' => $user->compensationProfile,
            'history' => $history,
        ]);
    }

    protected function kpiDistribution($runs): array
    {
        $levels = config('compensation.performance_levels', []);
        $out = [];
        foreach ($levels as $level) {
            $out[] = [
                'label' => $level['label'],
                'count' => $runs->where('kpi_level', $level['key'])->count(),
            ];
        }

        return $out;
    }
}

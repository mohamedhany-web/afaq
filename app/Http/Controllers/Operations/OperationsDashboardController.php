<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\OperationsPeriodReport;
use App\Models\Project;
use App\Models\RealEstateDeveloper;
use App\Services\Compensation\CompensationKpiScoringService;
use App\Services\Compensation\CompensationPayrollService;
use App\Services\AttendanceAbsenceReviewService;
use App\Services\Operations\OperationsKpiService;
use App\Services\Operations\OperationsLeadDistributionService;
use App\Services\OperationsRoleResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OperationsDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(
        CompensationPayrollService $payroll,
        CompensationKpiScoringService $scoring,
        AttendanceAbsenceReviewService $absenceReviews,
        OperationsKpiService $opsKpis,
        OperationsLeadDistributionService $leads,
    ) {
        $user = Auth::user();
        $resolver = OperationsRoleResolver::for($user);
        $period = $payroll->currentPeriod();
        $kpi = $scoring->evaluateUser($user, $period);
        $kpiGroups = $opsKpis->collect($period->starts_at, $period->ends_at, $user);

        $stats = [
            'active_projects' => Project::whereIn('listing_status', ['active', 'available', 'under_construction'])->count(),
            'developers' => RealEstateDeveloper::where('status', RealEstateDeveloper::STATUS_ACTIVE)->count(),
            'pending_absence_reviews' => $absenceReviews->pendingCount(),
            'unassigned_leads' => $leads->unassignedLeadsQuery()->count(),
            'pending_reports' => OperationsPeriodReport::query()
                ->where('user_id', $user->id)
                ->where('status', OperationsPeriodReport::STATUS_DRAFT)
                ->count(),
            'submitted_reports' => OperationsPeriodReport::query()
                ->where('user_id', $user->id)
                ->where('status', OperationsPeriodReport::STATUS_SUBMITTED)
                ->count(),
        ];

        if ($resolver->isAdmin()) {
            $stats['team_reports_pending'] = OperationsPeriodReport::where('status', OperationsPeriodReport::STATUS_DRAFT)->count();
            $stats['team_reports_submitted'] = OperationsPeriodReport::where('status', OperationsPeriodReport::STATUS_SUBMITTED)->count();
        }

        return view('operations.dashboard', compact('user', 'resolver', 'stats', 'kpi', 'period', 'kpiGroups'));
    }
}

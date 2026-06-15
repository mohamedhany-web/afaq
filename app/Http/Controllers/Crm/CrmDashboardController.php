<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\CrmDashboardAnalyticsService;
use App\Services\Operations\OperationsDashboardMetricsService;
use App\Services\SalesManagerDashboardService;
use App\Services\SalesRepDashboardService;
use Illuminate\Support\Facades\Auth;

class CrmDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $crmPulse = $this->crmPulseFor($user);

        if (($user->isSalesDepartmentManager() || $user->isSalesTeamLeader()) && ! $user->hasRole(['super_admin', 'admin'])) {
            return view('crm.dashboard-manager', array_merge(
                SalesManagerDashboardService::build($user),
                ['crmPulse' => $crmPulse]
            ));
        }

        if ($user->isSalesAgentOnly()) {
            return view('crm.dashboard-rep', SalesRepDashboardService::build($user));
        }

        return view('crm.dashboard', array_merge(
            CrmDashboardAnalyticsService::build($user),
            [
                'crmPulse' => $crmPulse,
                'portalPulse' => $user->hasRole(['super_admin', 'admin'])
                    ? app(\App\Services\ClientPortalHubService::class)->adminPulse()
                    : null,
            ]
        ));
    }

    private function crmPulseFor($user): ?array
    {
        if (! $user->canAccessOperations() && ! $user->hasRole(['super_admin', 'admin'])) {
            return null;
        }

        return app(OperationsDashboardMetricsService::class)->snapshot();
    }
}

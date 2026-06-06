<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\CrmDashboardAnalyticsService;
use App\Services\SalesManagerDashboardService;
use App\Services\SalesRepDashboardService;
use Illuminate\Support\Facades\Auth;

class CrmDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSalesManager() && ! $user->hasRole(['super_admin', 'admin'])) {
            return view('crm.dashboard-manager', SalesManagerDashboardService::build($user));
        }

        if ($user->isSalesAgentOnly()) {
            return view('crm.dashboard-rep', SalesRepDashboardService::build($user));
        }

        return view('crm.dashboard', CrmDashboardAnalyticsService::build($user));
    }
}

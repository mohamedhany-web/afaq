<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\MarketingDashboardService;
use Illuminate\Support\Facades\Auth;

class MarketingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessMarketing()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $data = MarketingDashboardService::build(Auth::user());

        if ($data['isRep'] && !$data['isManager']) {
            return view('marketing.dashboard-rep', $data);
        }

        return view('marketing.dashboard', $data);
    }
}

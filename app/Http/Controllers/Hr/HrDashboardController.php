<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Services\Hr\HrDashboardMetricsService;
use Illuminate\Support\Facades\Auth;

class HrDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessHr()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(HrDashboardMetricsService $metrics)
    {
        return view('hr.dashboard', [
            'stats' => $metrics->snapshot(),
        ]);
    }
}

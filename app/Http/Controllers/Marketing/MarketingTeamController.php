<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\MarketingEmployeeService;
use App\Services\MarketingScopeService;
use Illuminate\Support\Facades\Auth;

class MarketingTeamController extends Controller
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
        $scope = MarketingScopeService::for(Auth::user());
        $department = MarketingEmployeeService::marketingDepartment();

        $employees = $scope->employeesQuery()
            ->with(['user.roles', 'department'])
            ->orderByDesc('created_at')
            ->paginate(12);

        $stats = [
            'total' => (clone $scope->employeesQuery())->count(),
            'managers' => (clone $scope->employeesQuery())
                ->whereHas('user.roles', fn ($r) => $r->whereIn('name', MarketingEmployeeService::LEGACY_MANAGER_ROLES))
                ->count(),
            'reps' => (clone $scope->employeesQuery())
                ->whereHas('user.roles', fn ($r) => $r->whereIn('name', MarketingEmployeeService::LEGACY_REP_ROLES))
                ->count(),
        ];

        $canManage = Auth::user()->can('create-employees');

        return view('marketing.team.index', compact('employees', 'stats', 'department', 'canManage'));
    }
}

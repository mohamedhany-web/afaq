<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\CrmScopeService;
use App\Services\EmployeeComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeComplianceController extends Controller
{
    public function __construct(protected EmployeeComplianceService $compliance) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $start = $request->date('from') ?? now()->startOfMonth();
        $end = $request->date('to') ?? now()->endOfDay();

        if ($user->hasRole(['super_admin', 'admin']) || $user->isSalesManager()) {
            $team = $this->teamUsers($user);
            $overview = $this->compliance->teamOverview($team, $start, $end)->values();

            $stats = [
                'team_size' => $overview->count(),
                'excellent' => $overview->filter(fn ($r) => ($r['status']['key'] ?? '') === 'excellent')->count(),
                'critical' => $overview->filter(fn ($r) => in_array($r['status']['key'] ?? '', ['critical', 'warning'], true))->count(),
                'penalties_month' => $overview->sum('penalties_total'),
            ];

            return view('crm.employee-compliance.index', [
                'mode' => 'manager',
                'overview' => $overview,
                'stats' => $stats,
                'start' => $start,
                'end' => $end,
                'self' => null,
            ]);
        }

        $self = $this->compliance->evaluate($user, $start, $end);
        $leaves = $this->compliance->upcomingLeaves($user);

        return view('crm.employee-compliance.index', [
            'mode' => 'self',
            'overview' => collect(),
            'stats' => [],
            'self' => $self,
            'leaves' => $leaves,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function show(User $user, Request $request)
    {
        abort_unless(
            Auth::id() === $user->id
            || Auth::user()->hasRole(['super_admin', 'admin', 'sales_manager']),
            403,
        );

        $start = $request->date('from') ?? now()->startOfMonth();
        $end = $request->date('to') ?? now()->endOfDay();

        return view('crm.employee-compliance.show', [
            'employee' => $user,
            'evaluation' => $this->compliance->evaluate($user, $start, $end),
            'leaves' => $this->compliance->upcomingLeaves($user),
            'start' => $start,
            'end' => $end,
        ]);
    }

    protected function teamUsers(User $manager): \Illuminate\Support\Collection
    {
        if ($manager->hasRole(['super_admin', 'admin'])) {
            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                ->with('employee')
                ->orderBy('name')
                ->get();
        }

        $ids = CrmScopeService::for($manager)->managedTeamMemberUserIds();

        return User::query()->whereIn('id', $ids)->with('employee')->orderBy('name')->get();
    }
}

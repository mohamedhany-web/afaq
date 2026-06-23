<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\Sale;
use App\Services\Operations\OperationsKpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsCrmController extends Controller
{
    public function __construct(protected OperationsKpiService $kpis)
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $salesRepUserId = $request->filled('sales_rep') ? (int) $request->sales_rep : null;
        $employeeId = null;
        if ($salesRepUserId) {
            $employeeId = \App\Models\User::query()->find($salesRepUserId)?->employee?->id;
        }

        $clientQuery = Client::query();
        $saleQuery = Sale::query();

        if ($salesRepUserId) {
            if ($employeeId) {
                $clientQuery->where('assigned_to', $employeeId);
            } else {
                $clientQuery->whereRaw('1 = 0');
            }
            $saleQuery->where('assigned_to', $salesRepUserId);
        }

        $kpiData = $this->kpis->collect();

        $pipeline = (clone $saleQuery)
            ->selectRaw('stage, COUNT(*) as cnt, COALESCE(SUM(estimated_value), 0) as val')
            ->whereNotIn('stage', ['closed_lost'])
            ->groupBy('stage')
            ->get()
            ->keyBy('stage');

        $staleClients = (clone $clientQuery)
            ->whereIn('lead_stage', ['lead', 'prospect', 'proposal'])
            ->where('updated_at', '<', now()->subDays(5))
            ->with('assignedEmployee:id,first_name,last_name')
            ->orderBy('updated_at')
            ->limit(15)
            ->get();

        $overdueFollowUpsQuery = CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->where('scheduled_at', '<', now());

        if ($salesRepUserId) {
            $overdueFollowUpsQuery->where('user_id', $salesRepUserId);
        }

        $overdueFollowUps = $overdueFollowUpsQuery
            ->with(['client:id,name,phone', 'user:id,name'])
            ->orderBy('scheduled_at')
            ->limit(15)
            ->get();

        return view('operations.crm.index', [
            'crmKpis' => $kpiData['groups']['crm_management'] ?? null,
            'salesKpis' => $kpiData['groups']['sales_operations'] ?? null,
            'pipeline' => $pipeline,
            'staleClients' => $staleClients,
            'overdueFollowUps' => $overdueFollowUps,
            'selectedSalesRep' => $salesRepUserId ? \App\Models\User::find($salesRepUserId) : null,
            'stats' => [
                'total_clients' => (clone $clientQuery)->count(),
                'new_clients' => (clone $clientQuery)->where('lead_stage', \App\Services\CrmScopeService::LEAD_STAGE_NEW)->count(),
                'active_deals' => (clone $saleQuery)->whereNotIn('stage', ['closed_won', 'closed_lost'])->count(),
                'won_month' => (clone $saleQuery)->where('stage', 'closed_won')
                    ->whereMonth('actual_close_date', now()->month)
                    ->count(),
            ],
        ]);
    }
}

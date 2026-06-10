<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\Sale;
use App\Services\Operations\OperationsKpiService;
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

    public function index()
    {
        $kpiData = $this->kpis->collect();

        $pipeline = Sale::query()
            ->selectRaw('stage, COUNT(*) as cnt, COALESCE(SUM(estimated_value), 0) as val')
            ->whereNotIn('stage', ['closed_lost'])
            ->groupBy('stage')
            ->get()
            ->keyBy('stage');

        $staleClients = Client::query()
            ->whereIn('lead_stage', ['lead', 'prospect', 'proposal'])
            ->where('updated_at', '<', now()->subDays(5))
            ->with('assignedEmployee:id,first_name,last_name')
            ->orderBy('updated_at')
            ->limit(15)
            ->get();

        $overdueFollowUps = CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->where('scheduled_at', '<', now())
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
            'stats' => [
                'total_clients' => Client::count(),
                'active_deals' => Sale::whereNotIn('stage', ['closed_won', 'closed_lost'])->count(),
                'won_month' => Sale::where('stage', 'closed_won')
                    ->whereMonth('actual_close_date', now()->month)
                    ->count(),
            ],
        ]);
    }
}

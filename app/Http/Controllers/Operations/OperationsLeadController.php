<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Operations\OperationsKpiService;
use App\Services\Operations\OperationsLeadDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsLeadController extends Controller
{
    public function __construct(
        protected OperationsLeadDistributionService $distribution,
        protected OperationsKpiService $kpis,
    ) {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'unassigned');

        $baseQuery = match ($filter) {
            'stale' => Client::query()
                ->whereNull('assigned_to')
                ->where('updated_at', '<', now()->subDays(3))
                ->orderByDesc('updated_at'),
            default => $this->distribution->unassignedLeadsQuery(),
        };

        $leads = (clone $baseQuery)
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where('name', 'like', $s)->orWhere('phone', 'like', $s);
            }))
            ->paginate(20)
            ->withQueryString();

        $kpiData = $this->kpis->collect();
        $leadGroup = $kpiData['groups']['lead_management'] ?? null;

        return view('operations.leads.index', [
            'leads' => $leads,
            'filter' => $filter,
            'reps' => $this->distribution->assignableReps(),
            'repLoads' => $this->distribution->repLoads(),
            'leadKpis' => $leadGroup,
            'stats' => [
                'unassigned' => $this->distribution->unassignedLeadsQuery()->count(),
                'stale' => Client::query()
                    ->whereNull('assigned_to')
                    ->where('updated_at', '<', now()->subDays(3))
                    ->count(),
            ],
        ]);
    }

    public function assign(Request $request, Client $client)
    {
        $request->validate(['employee_id' => 'required|exists:employees,id']);

        if ($client->assigned_to) {
            return back()->with('error', 'العميل مُعيَّن مسبقاً.');
        }

        $this->distribution->assignTo($client, (int) $request->employee_id, Auth::user());

        return back()->with('success', 'تم ترحيل العميل إلى المندوب.');
    }

    public function distributeBatch(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'integer|exists:clients,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $result = $this->distribution->distributeBatch(
            $request->client_ids,
            Auth::user(),
            $request->employee_id ? (int) $request->employee_id : null,
        );

        return back()->with('success', "تم توزيع {$result['assigned']} عميل — متخطى: {$result['skipped']}.");
    }

    public function autoDistribute(Request $request)
    {
        $ids = $this->distribution->unassignedLeadsQuery()
            ->limit((int) $request->input('limit', 50))
            ->pluck('id')
            ->all();

        $result = $this->distribution->distributeBatch($ids, Auth::user());

        return back()->with('success', "توزيع تلقائي: {$result['assigned']} عميل — متخطى: {$result['skipped']}.");
    }
}

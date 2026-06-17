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
        return redirect()->route('operations.clients.index', array_merge(
            ['view' => 'distribution', 'filter' => $request->get('filter', 'unassigned')],
            $request->only('search'),
        ));
    }

    public function assign(Request $request, Client $client)
    {
        $request->validate(['employee_id' => 'required|exists:employees,id']);

        if ($client->assigned_to) {
            return back()->with('error', 'العميل مُعيَّن مسبقاً.');
        }

        $this->distribution->assignTo($client, (int) $request->employee_id, Auth::user());

        return redirect()->route('operations.clients.index', ['view' => 'distribution'])->with('success', 'تم ترحيل العميل إلى المندوب.');
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

        return redirect()->route('operations.clients.index', ['view' => 'distribution'])->with('success', "تم توزيع {$result['assigned']} عميل — متخطى: {$result['skipped']}.");
    }

    public function autoDistribute(Request $request)
    {
        $ids = $this->distribution->unassignedLeadsQuery()
            ->limit((int) $request->input('limit', 50))
            ->pluck('id')
            ->all();

        $result = $this->distribution->distributeBatch($ids, Auth::user());

        return redirect()->route('operations.clients.index', ['view' => 'distribution'])->with('success', "توزيع تلقائي: {$result['assigned']} عميل — متخطى: {$result['skipped']}.");
    }
}

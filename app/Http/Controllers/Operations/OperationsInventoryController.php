<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Services\Operations\OperationsKpiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperationsInventoryController extends Controller
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

        $byStatus = ProjectUnit::query()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $byProject = Project::query()
            ->withCount([
                'units as available_count' => fn ($q) => $q->where('status', ProjectUnit::STATUS_AVAILABLE),
                'units as reserved_count' => fn ($q) => $q->where('status', ProjectUnit::STATUS_RESERVED),
                'units as sold_count' => fn ($q) => $q->where('status', ProjectUnit::STATUS_SOLD),
                'units as units_total',
            ])
            ->whereHas('units')
            ->orderByDesc('available_count')
            ->limit(20)
            ->get();

        $missingPrice = ProjectUnit::query()
            ->where('status', ProjectUnit::STATUS_AVAILABLE)
            ->where(fn ($q) => $q->whereNull('price_cash')->orWhere('price_cash', '<=', 0))
            ->where(fn ($q) => $q->whereNull('price_installment')->orWhere('price_installment', '<=', 0))
            ->with('project:id,name')
            ->limit(10)
            ->get();

        return view('operations.inventory.index', [
            'inventoryKpis' => $kpiData['groups']['inventory_operations'] ?? null,
            'byStatus' => $byStatus,
            'byProject' => $byProject,
            'missingPrice' => $missingPrice,
            'stats' => [
                'total' => ProjectUnit::count(),
                'available' => (int) ($byStatus[ProjectUnit::STATUS_AVAILABLE] ?? 0),
                'reserved' => (int) ($byStatus[ProjectUnit::STATUS_RESERVED] ?? 0),
                'sold' => (int) ($byStatus[ProjectUnit::STATUS_SOLD] ?? 0),
            ],
        ]);
    }
}

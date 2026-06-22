<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Services\Operations\OperationsKpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function index(Request $request)
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

        $statusFilter = $request->get('status');
        if ($statusFilter && !in_array($statusFilter, [
            ProjectUnit::STATUS_AVAILABLE,
            ProjectUnit::STATUS_RESERVED,
            ProjectUnit::STATUS_SOLD,
        ], true)) {
            $statusFilter = null;
        }

        $useTypeFilter = $request->get('use_type');
        $allowedUseTypes = array_keys(config('project_units.use_types', []));
        if ($useTypeFilter && ! in_array($useTypeFilter, $allowedUseTypes, true)) {
            $useTypeFilter = null;
        }

        $selectedProject = $request->filled('project_id')
            ? Project::with('units')->find((int) $request->project_id)
            : null;

        $units = ProjectUnit::query()
            ->with(['project:id,name', 'floor:id,label,level'])
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->when($useTypeFilter, fn ($q) => $q->where('use_type', $useTypeFilter))
            ->when($request->filled('project_id'), fn ($q) => $q->where('project_id', (int) $request->project_id))
            ->when($request->search, function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($q) use ($s) {
                    $q->where('code', 'like', $s)
                        ->orWhereHas('project', fn ($p) => $p->where('name', 'like', $s));
                });
            })
            ->orderBy('project_id')
            ->orderBy('code')
            ->paginate(48)
            ->withQueryString();

        $projects = Project::query()
            ->whereHas('units')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('operations.inventory.index', [
            'inventoryKpis' => $kpiData['groups']['inventory_operations'] ?? null,
            'byStatus' => $byStatus,
            'byProject' => $byProject,
            'missingPrice' => $missingPrice,
            'units' => $units,
            'projects' => $projects,
            'statusFilter' => $statusFilter,
            'useTypeFilter' => $useTypeFilter,
            'selectedProject' => $selectedProject,
            'useTypeLabels' => config('project_units.use_types', []),
            'stats' => [
                'total' => ProjectUnit::count(),
                'available' => (int) ($byStatus[ProjectUnit::STATUS_AVAILABLE] ?? 0),
                'reserved' => (int) ($byStatus[ProjectUnit::STATUS_RESERVED] ?? 0),
                'sold' => (int) ($byStatus[ProjectUnit::STATUS_SOLD] ?? 0),
            ],
        ]);
    }
}

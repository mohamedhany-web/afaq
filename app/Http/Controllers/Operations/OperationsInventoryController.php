<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Crm\Concerns\UsesCrmFilters;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Services\Operations\OperationsKpiService;
use App\Services\ProjectManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OperationsInventoryController extends Controller
{
    use UsesCrmFilters;

    public function __construct(
        protected OperationsKpiService $kpis,
        protected ProjectManagementService $projects,
    ) {
        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $kpiData = $this->kpis->collect();
        $filters = $this->crmFilters($request);

        $byStatus = ProjectUnit::query()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $projectBase = $filters->applyProjectFilters(
            Project::query()->whereHas('units'),
            $request,
        );

        $byProject = (clone $projectBase)
            ->withCount([
                'units as available_count' => fn ($q) => $q->where('status', ProjectUnit::STATUS_AVAILABLE),
                'units as reserved_count' => fn ($q) => $q->where('status', ProjectUnit::STATUS_RESERVED),
                'units as sold_count' => fn ($q) => $q->where('status', ProjectUnit::STATUS_SOLD),
                'units as units_total',
            ])
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
        if ($statusFilter && ! in_array($statusFilter, [
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

        $unitsQuery = ProjectUnit::query()
            ->with(['project:id,name,inventory_source', 'floor:id,label,level', 'paymentPlans'])
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->when($useTypeFilter, fn ($q) => $q->where('use_type', $useTypeFilter))
            ->when($request->filled('project_id'), fn ($q) => $q->where('project_id', (int) $request->project_id))
            ->when($request->filled('inventory_source'), fn ($q) => $q->whereHas(
                'project',
                fn ($p) => $p->where('inventory_source', $request->inventory_source)
            ))
            ->when($request->filled('developer_id'), fn ($q) => $q->whereHas(
                'project',
                fn ($p) => $p->where('real_estate_developer_id', (int) $request->developer_id)
            ))
            ->when($request->filled('direction'), fn ($q) => $q->where('direction', $request->direction))
            ->when($request->filled('floor_number'), fn ($q) => $q->where('floor_number', 'like', '%' . $request->floor_number . '%'))
            ->when($request->filled('area_min'), fn ($q) => $q->where('area_m2', '>=', (float) $request->area_min))
            ->when($request->filled('area_max'), fn ($q) => $q->where('area_m2', '<=', (float) $request->area_max))
            ->when($request->search, function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($q) use ($s) {
                    $q->where('code', 'like', $s)
                        ->orWhere('apartment_number', 'like', $s)
                        ->orWhereHas('project', fn ($p) => $p->where('name', 'like', $s)->orWhere('city', 'like', $s));
                });
            });

        $units = (clone $unitsQuery)
            ->orderBy('project_id')
            ->orderBy('code')
            ->paginate(48)
            ->withQueryString();

        $projects = Project::query()
            ->whereHas('units')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('operations.inventory.index', array_merge(
            $this->projectFilterViewData($filters, $request),
            [
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
                'clearUrl' => route('operations.inventory.index'),
                'projectsRoutePrefix' => 'operations.projects',
                'inventoryExportRoute' => route('operations.inventory.export', request()->query()),
                'stats' => [
                    'total' => ProjectUnit::count(),
                    'available' => (int) ($byStatus[ProjectUnit::STATUS_AVAILABLE] ?? 0),
                    'reserved' => (int) ($byStatus[ProjectUnit::STATUS_RESERVED] ?? 0),
                    'sold' => (int) ($byStatus[ProjectUnit::STATUS_SOLD] ?? 0),
                ],
            ]
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($this->projects->canViewAny($user), 403);

        $filters = $this->crmFilters($request);
        $query = $filters->applyProjectFilters(
            $this->projects->scopedQuery($user)
                ->with(['realEstateDeveloper', 'units.paymentPlans']),
            $request,
        )->orderBy('name');

        $filename = 'inventory-units-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [
                'المشروع', 'نوع المخزون', 'المطور', 'المدينة', 'التصنيف', 'رقم الوحدة', 'الطابق', 'الدور', 'الاتجاه',
                'المساحة', 'سعر الوحدة', 'نسبة البناء', 'نسبة الخصم', 'نسبة التحميل', 'إجمالي العقد', 'وديعة الصيانة',
                'المقدم', 'الباقي', 'أشهر التقسيط', 'حالة الوحدة',
            ]);

            $query->chunkById(50, function ($projects) use ($out) {
                foreach ($projects as $project) {
                    if ($project->units->isEmpty()) {
                        continue;
                    }

                    foreach ($project->units as $unit) {
                        $plan = $unit->paymentPlans->first();
                        fputcsv($out, [
                            $project->name,
                            $project->inventorySourceLabel(),
                            $project->displayDeveloperName(),
                            $project->city,
                            $unit->useTypeLabel(),
                            $unit->displayCode(),
                            $unit->floor_number,
                            $unit->floor_label,
                            $unit->directionLabel(),
                            $unit->area_m2,
                            $unit->unit_price_total ?? $unit->price_cash,
                            $plan?->building_percent,
                            $plan?->discount_percent,
                            $plan?->loading_percent,
                            $plan?->total_contract_amount,
                            $plan?->maintenance_deposit,
                            $plan?->down_payment_amount,
                            $plan?->remaining_balance,
                            $plan?->installment_months,
                            $unit->statusLabel(),
                        ]);
                    }
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

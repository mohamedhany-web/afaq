<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Controllers\Crm\Concerns\UsesCrmFilters;
use App\Support\ProjectUnitNumbering;
use App\Services\CrmScopeService;
use App\Services\ProjectApprovalService;
use App\Services\ProjectManagementService;
use App\Services\ProjectUnitGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmProjectController extends Controller
{
    use UsesCrmFilters;

    protected string $projectsRoutePrefix = 'crm.projects';

    public function __construct(
        protected ProjectManagementService $projects,
        protected ProjectApprovalService $approval,
    ) {}

    protected function projectsRoute(string $action, mixed $parameters = []): string
    {
        return route($this->projectsRoutePrefix . '.' . $action, $parameters);
    }

    /** @return array<string, mixed> */
    protected function projectsViewData(array $data = []): array
    {
        return array_merge(['projectsRoutePrefix' => $this->projectsRoutePrefix], $data);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($this->projects->canViewAny($user), 403, 'لا تملك صلاحية عرض المشاريع.');

        $filters = $this->crmFilters($request);
        $base = $this->projects->scopedQuery($user);

        $projects = $filters->applyProjectFilters(
            (clone $base)->with(['realEstateDeveloper'])->withCount(['mapPins', 'sales']),
            $request,
        )
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('listing_status', 'active')->count(),
            'upcoming' => (clone $base)->where('listing_status', 'upcoming')->count(),
            'available_units' => (clone $base)->sum('available_units'),
            'ownership' => $this->projects->ownershipStats($base),
        ];

        return view('crm.projects.index', $this->projectsViewData([
            'projects' => $projects,
            'stats' => $stats,
            'requiresApproval' => $this->approval->requiresApproval($user),
            'clearUrl' => $this->projectsRoute('index'),
            'projectsExportRoute' => $this->projectsRoute('export', request()->query()),
            ...$this->projectFilterViewData($filters, $request),
            'developers' => $this->projects->contractedDevelopers(),
        ]));
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

        $filename = 'projects-units-' . now()->format('Y-m-d-His') . '.csv';

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
                        fputcsv($out, [
                            $project->name,
                            $project->inventorySourceLabel(),
                            $project->displayDeveloperName(),
                            $project->city,
                            $project->property_type_name,
                            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                        ]);
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

    public function create()
    {
        abort_unless($this->projects->canCreate(Auth::user()), 403);

        return view('crm.projects.create', $this->projectsViewData([
            'project' => new Project(),
            'users' => $this->projects->formUsers(),
            'developers' => $this->projects->contractedDevelopers(),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($this->projects->canCreate($user), 403);

        if ($this->approval->requiresApproval($user)) {
            $this->approval->submitCreate($request, $user);

            return redirect()->route('crm.projects.approvals.index')
                ->with('success', 'تم إرسال طلب إضافة المشروع — بانتظار موافقة الإدارة العليا.');
        }

        $data = $this->projects->validate($request);
        $manualUnits = $request->input('manual_units', []);
        $data = $this->projects->normalize($data, $request, $user);
        $data = $this->projects->resolveDeveloper($data, $user);

        $project = Project::create($data);

        if ($request->filled('team_members')) {
            $project->teamMembers()->attach($request->team_members);
        }

        $this->projects->syncMapPins($project, $request, $user);
        $this->projects->syncManualUnits($project, is_array($manualUnits) ? $manualUnits : []);

        return redirect()->to($this->projectsRoute('show', $project))
            ->with('success', 'تم إضافة المشروع العقاري بنجاح');
    }

    public function show(Project $project)
    {
        abort_unless($this->projects->canView(Auth::user(), $project), 403);

        $project->load([
            'projectManager',
            'department',
            'mapPins',
            'realEstateDeveloper.activeContract',
            'buildingFloors.units.paymentPlans',
        ]);

        $unitGenerator = app(ProjectUnitGeneratorService::class);
        if ($project->buildingFloors->isNotEmpty() && ProjectUnitNumbering::projectNeedsRenumbering($project)) {
            $unitGenerator->applyAfaqNumbering($project);
            $project->load(['buildingFloors.units.paymentPlans']);
        }

        $scopedSales = CrmScopeService::for(Auth::user())->salesQuery()
            ->where('project_id', $project->id);

        $project->setRelation(
            'sales',
            (clone $scopedSales)->with(['client', 'salesRep'])->latest()->limit(5)->get()
        );

        $project->sales_count = (clone $scopedSales)->count();

        $stats = [
            'sales_value' => (float) (clone $scopedSales)->sum('estimated_value'),
            'occupancy' => $project->occupancy_percent,
        ];

        $buildingSummary = app(ProjectUnitGeneratorService::class)->buildingSummary($project);

        return view('crm.projects.show', $this->projectsViewData([
            'project' => $project,
            'stats' => $stats,
            'buildingSummary' => $buildingSummary,
            'pendingChange' => $this->approval->pendingForProject($project),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]));
    }

    public function edit(Project $project)
    {
        abort_unless($this->projects->canUpdate(Auth::user(), $project), 403);

        $project->load(['teamMembers', 'mapPins']);

        return view('crm.projects.edit', $this->projectsViewData([
            'project' => $project,
            'users' => $this->projects->formUsers(),
            'developers' => $this->projects->contractedDevelopers(),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        abort_unless($this->projects->canUpdate($user, $project), 403);

        if ($this->approval->requiresApproval($user)) {
            $this->approval->submitUpdate($request, $project, $user);

            return redirect()->route('crm.projects.approvals.index')
                ->with('success', 'تم إرسال طلب التعديل — بانتظار موافقة الإدارة العليا.');
        }

        $data = $this->projects->validate($request, $project);
        $manualUnits = $request->input('manual_units', []);
        $data = $this->projects->normalize($data, $request, $user, $project);
        $data = $this->projects->resolveDeveloper($data, $user);

        $project->update($data);

        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members ?? []);
        }

        $this->projects->syncMapPins($project, $request, $user);
        $this->projects->syncManualUnits($project, is_array($manualUnits) ? $manualUnits : []);

        return redirect()->to($this->projectsRoute('show', $project))
            ->with('success', 'تم تحديث المشروع العقاري بنجاح');
    }

    public function destroy(Request $request, Project $project)
    {
        $user = Auth::user();
        abort_unless($this->projects->canDelete($user, $project), 403);

        try {
            if ($this->approval->requiresApproval($user)) {
                $request->validate([
                    'delete_reason' => 'required|string|min:10|max:1000',
                ], [
                    'delete_reason.required' => 'يجب كتابة سبب الحذف.',
                    'delete_reason.min' => 'سبب الحذف يجب أن يكون 10 أحرف على الأقل.',
                ]);

                $this->approval->submitDelete($project, $user, $request->input('delete_reason'));

                return redirect()->route('crm.projects.approvals.index')
                    ->with('success', 'تم إرسال طلب الحذف — بانتظار موافقة الإدارة العليا.');
            }

            $this->projects->deleteProject($project, $user);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 422) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            throw $e;
        }

        return redirect()->to($this->projectsRoute('index'))
            ->with('success', 'تم حذف المشروع العقاري بنجاح');
    }
}

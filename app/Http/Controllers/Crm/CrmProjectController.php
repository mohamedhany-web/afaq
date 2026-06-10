<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\CrmScopeService;
use App\Services\ProjectApprovalService;
use App\Services\ProjectManagementService;
use App\Services\ProjectUnitGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmProjectController extends Controller
{
    public function __construct(
        protected ProjectManagementService $projects,
        protected ProjectApprovalService $approval,
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($this->projects->canViewAny($user), 403, 'لا تملك صلاحية عرض المشاريع.');

        $base = $this->projects->scopedQuery($user);

        $projects = (clone $base)
            ->when($request->search, fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('developer_name', 'like', '%' . $request->search . '%');
            }))
            ->when($request->listing_status, fn ($q) => $q->where('listing_status', $request->listing_status))
            ->when($request->property_type, fn ($q) => $q->where('property_type', $request->property_type))
            ->when($request->ownership_type, fn ($q) => $q->where('ownership_type', $request->ownership_type))
            ->with(['realEstateDeveloper'])
            ->withCount(['mapPins', 'sales'])
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

        return view('crm.projects.index', [
            'projects' => $projects,
            'stats' => $stats,
            'requiresApproval' => $this->approval->requiresApproval($user),
        ]);
    }

    public function create()
    {
        abort_unless($this->projects->canCreate(Auth::user()), 403);

        return view('crm.projects.create', [
            'users' => $this->projects->formUsers(),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]);
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
        $data = $this->projects->normalize($data, $request, $user);
        $data = $this->projects->resolveDeveloper($data, $user);

        $project = Project::create($data);

        if ($request->filled('team_members')) {
            $project->teamMembers()->attach($request->team_members);
        }

        $this->projects->syncMapPins($project, $request, $user);

        return redirect()->route('crm.projects.show', $project)
            ->with('success', 'تم إضافة المشروع العقاري بنجاح');
    }

    public function show(Project $project)
    {
        abort_unless($this->projects->canView(Auth::user(), $project), 403);

        $scopedSales = CrmScopeService::for(Auth::user())->salesQuery()
            ->where('project_id', $project->id);

        $project->load([
            'projectManager',
            'department',
            'mapPins',
            'realEstateDeveloper.activeContract',
            'buildingFloors.units.paymentPlans',
        ]);

        $project->setRelation(
            'sales',
            (clone $scopedSales)->with('client')->latest()->limit(5)->get()
        );

        $project->sales_count = (clone $scopedSales)->count();

        $stats = [
            'sales_value' => (float) (clone $scopedSales)->sum('estimated_value'),
            'occupancy' => $project->occupancy_percent,
        ];

        $buildingSummary = app(ProjectUnitGeneratorService::class)->buildingSummary($project);

        return view('crm.projects.show', [
            'project' => $project,
            'stats' => $stats,
            'buildingSummary' => $buildingSummary,
            'pendingChange' => $this->approval->pendingForProject($project),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]);
    }

    public function edit(Project $project)
    {
        abort_unless($this->projects->canUpdate(Auth::user(), $project), 403);

        $project->load(['teamMembers', 'mapPins']);

        return view('crm.projects.edit', [
            'project' => $project,
            'users' => $this->projects->formUsers(),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]);
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
        $data = $this->projects->normalize($data, $request, $user);
        $data = $this->projects->resolveDeveloper($data, $user);

        $project->update($data);

        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members ?? []);
        }

        $this->projects->syncMapPins($project, $request, $user);

        return redirect()->route('crm.projects.show', $project)
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

        return redirect()->route('crm.projects.index')
            ->with('success', 'تم حذف المشروع العقاري بنجاح');
    }
}

<?php

namespace App\Http\Controllers\DeveloperPortal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\DeveloperPortalService;
use App\Services\ProjectUnitGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(
        protected DeveloperPortalService $portal,
        protected ProjectUnitGeneratorService $units,
    ) {}

    public function index(Request $request)
    {
        $account = Auth::guard('developer')->user();
        $projects = $this->portal->projectsQuery($account)
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('location', 'like', '%' . $request->search . '%'))
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('developer-portal.projects.index', compact('projects'));
    }

    public function create()
    {
        abort_unless(Auth::guard('developer')->user()->canManageProjects(), 403);

        return view('developer-portal.projects.create');
    }

    public function store(Request $request)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);

        $developer = $this->portal->developer($account);
        $data = $this->portal->validateProject($request);
        $data = $this->portal->normalizeProject($data);
        $data = $this->portal->attachDeveloperMeta($data, $developer);

        $project = Project::create($data);

        return redirect()->route('developer.projects.show', $project)
            ->with('success', 'تم إضافة المشروع — يظهر الآن لفريق المبيعات');
    }

    public function show(Project $project)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($this->portal->canAccessProject($account, $project), 404);

        $project->load(['buildingFloors.units.paymentPlans', 'mapPins']);
        $buildingSummary = $this->units->buildingSummary($project);
        $themeColor = \App\Helpers\SettingsHelper::getThemeColor();

        return view('developer-portal.projects.show', compact('project', 'buildingSummary', 'themeColor', 'account'));
    }

    public function edit(Project $project)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);
        abort_unless($this->portal->canAccessProject($account, $project), 404);

        return view('developer-portal.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);
        abort_unless($this->portal->canAccessProject($account, $project), 404);

        $data = $this->portal->validateProject($request);
        $data = $this->portal->normalizeProject($data);
        $data = $this->portal->attachDeveloperMeta($data, $this->portal->developer($account));

        $project->update($data);

        return redirect()->route('developer.projects.show', $project)
            ->with('success', 'تم تحديث المشروع');
    }

    public function destroy(Project $project)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);
        abort_unless($this->portal->canAccessProject($account, $project), 404);

        if (!$project->isDeletable()) {
            return back()->with('error', 'لا يمكن حذف مشروع مرتبط بصفقات أو وحدات مباعة.');
        }

        $project->delete();

        return redirect()->route('developer.projects.index')->with('success', 'تم حذف المشروع');
    }
}

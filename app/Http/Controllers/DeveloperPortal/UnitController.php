<?php

namespace App\Http\Controllers\DeveloperPortal;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Services\DeveloperPortalService;
use App\Services\ProjectUnitGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function __construct(
        protected DeveloperPortalService $portal,
        protected ProjectUnitGeneratorService $generator,
    ) {}

    public function generate(Request $request, Project $project)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);
        abort_unless($this->portal->canAccessProject($account, $project), 404);

        $replace = $request->boolean('replace', true);
        if ($replace && $project->units()->where('status', 'sold')->exists()) {
            return back()->with('error', 'لا يمكن إعادة التوليد — يوجد وحدات مباعة.');
        }

        $result = $this->generator->generate($project, $replace);

        return back()->with('success', sprintf('تم توليد %d وحدة على %d طوابق', $result['units'], $result['floors']));
    }

    public function update(Request $request, Project $project, ProjectUnit $unit)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);
        abort_unless($this->portal->canAccessProject($account, $project), 404);
        abort_unless((int) $unit->project_id === (int) $project->id, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('project_units.statuses', [])))],
        ]);

        $unit->update(['status' => $data['status']]);
        $this->generator->syncProjectTotals($project);

        return response()->json(['ok' => true, 'unit' => [
            'id' => $unit->id,
            'status' => $unit->status,
            'status_label' => $unit->statusLabel(),
            'color' => $unit->meshColor(),
        ]]);
    }
}

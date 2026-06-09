<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Services\ProjectManagementService;
use App\Services\ProjectUnitGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CrmProjectUnitController extends Controller
{
    public function __construct(
        protected ProjectManagementService $projects,
        protected ProjectUnitGeneratorService $generator,
    ) {}

    public function generate(Request $request, Project $project)
    {
        abort_unless($this->projects->canUpdate(Auth::user(), $project), 403);

        $request->validate([
            'replace' => 'nullable|boolean',
        ]);

        $replace = $request->boolean('replace', true);

        if ($replace && $project->units()->where('status', 'sold')->exists()) {
            return back()->with('error', 'لا يمكن إعادة التوليد — يوجد وحدات مباعة.');
        }

        $result = $this->generator->generate($project, $replace);

        return back()->with('success', sprintf(
            'تم توليد %d وحدة على %d طوابق',
            $result['units'],
            $result['floors'],
        ));
    }

    public function update(Request $request, Project $project, ProjectUnit $unit)
    {
        abort_unless((int) $unit->project_id === (int) $project->id, 404);
        abort_unless($this->projects->canUpdate(Auth::user(), $project), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('project_units.statuses', [])))],
        ]);

        $unit->update(['status' => $data['status']]);
        $this->generator->syncProjectTotals($project);
        $project->refresh();

        return response()->json([
            'ok' => true,
            'unit' => [
                'id' => $unit->id,
                'code' => $unit->code,
                'status' => $unit->status,
                'status_label' => $unit->statusLabel(),
                'color' => $unit->meshColor(),
                'price_cash' => (float) $unit->price_cash,
                'price_installment' => $unit->price_installment ? (float) $unit->price_installment : null,
            ],
            'project' => [
                'total_units' => $project->total_units,
                'available_units' => $project->available_units,
                'sold_units' => $project->sold_units,
            ],
        ]);
    }
}

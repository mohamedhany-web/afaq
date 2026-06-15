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

    public function renumber(Project $project)
    {
        abort_unless($this->projects->canUpdate(Auth::user(), $project), 403);

        if (!$project->buildingFloors()->exists()) {
            return back()->with('error', 'لا توجد طوابق أو وحدات لإعادة الترقيم.');
        }

        $count = $this->generator->applyAfaqNumbering($project);

        return back()->with('success', $count > 0
            ? "تم تحديث ترقيم {$count} وحدة — B · GF · FF · SF · TF"
            : 'الترقيم مطابق للمعيار بالفعل.');
    }

    public function show(Project $project, ProjectUnit $unit)
    {
        abort_unless((int) $unit->project_id === (int) $project->id, 404);
        abort_unless($this->projects->canView(Auth::user(), $project), 403);

        $unit->load(['floor', 'paymentPlans']);

        return response()->json([
            'unit' => $this->unitPayload($unit),
        ]);
    }

    /** @return array<string, mixed> */
    public static function unitPayload(ProjectUnit $unit): array
    {
        $floor = $unit->floor;
        $area = (float) $unit->area_m2;

        return [
            'id' => $unit->id,
            'code' => $unit->code,
            'floor_id' => $floor?->id,
            'floor_label' => $floor?->label,
            'floor_prefix' => $floor ? \App\Support\ProjectUnitNumbering::floorPrefix((int) $floor->level) : null,
            'floor_level' => $floor?->level,
            'use_type' => $unit->use_type,
            'use_label' => $unit->useTypeLabel(),
            'area_m2' => $area,
            'price_cash' => (float) $unit->price_cash,
            'price_installment' => $unit->price_installment ? (float) $unit->price_installment : null,
            'price_per_m2_cash' => $area > 0 ? round((float) $unit->price_cash / $area, 0) : null,
            'price_per_m2_installment' => $area > 0 && $unit->price_installment
                ? round((float) $unit->price_installment / $area, 0)
                : null,
            'status' => $unit->status,
            'status_label' => $unit->statusLabel(),
            'color' => $unit->meshColor(),
            'payment_plans' => $unit->paymentPlans->map(fn ($plan) => [
                'type' => $plan->plan_type,
                'type_label' => $plan->planTypeLabel(),
                'down_percent' => $plan->down_percent ? (float) $plan->down_percent : null,
                'years' => $plan->years,
                'installment_per_m2' => $plan->installment_per_m2 ? (float) $plan->installment_per_m2 : null,
                'down_payment_amount' => $plan->down_payment_amount ? (float) $plan->down_payment_amount : null,
                'notes' => $plan->notes,
            ])->values()->all(),
            'meta' => $unit->meta ?? [],
            'show_url' => route('crm.projects.units.show', ['project' => $unit->project_id, 'unit' => $unit->id]),
        ];
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

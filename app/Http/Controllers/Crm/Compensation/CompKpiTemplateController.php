<?php

namespace App\Http\Controllers\Crm\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\Compensation\CompKpiItem;
use App\Models\Compensation\CompKpiTemplate;
use App\Models\User;
use App\Services\Compensation\CompensationAuditService;
use App\Services\CrmEmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompKpiTemplateController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $templates = CompKpiTemplate::withCount(['items', 'employeeProfiles'])
            ->with('items')
            ->latest()
            ->get();

        $employees = User::role(array_merge(
            CrmEmployeeService::LEGACY_MANAGER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
        ))->orderBy('name')->get(['id', 'name']);

        return view('crm.compensation.admin.kpi.index', compact('templates', 'employees'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('crm.compensation.admin.kpi.form', $this->formViewData(null, request('role', 'rep')));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $this->validated($request);

        $template = DB::transaction(function () use ($data, $request) {
            $template = CompKpiTemplate::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'target_role' => $data['target_role'],
                'evaluation_period' => $data['evaluation_period'],
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
            ]);
            $this->syncItems($template, $request->input('items', []));

            return $template;
        });

        CompensationAuditService::log('kpi_template.created', CompKpiTemplate::class, $template->id);

        $applied = $this->handleAssignment($request, $template);

        return redirect()->route('crm.compensation.kpi.index')
            ->with('success', 'تم إنشاء قالب KPI' . ($applied ? " — تم ربطه بـ {$applied} موظف" : ''));
    }

    public function edit(CompKpiTemplate $template)
    {
        $this->authorizeAdmin();
        $template->load('items');

        return view('crm.compensation.admin.kpi.form', $this->formViewData($template, $template->target_role));
    }

    public function update(Request $request, CompKpiTemplate $template)
    {
        $this->authorizeAdmin();
        $old = $template->toArray();
        $data = $this->validated($request);

        DB::transaction(function () use ($template, $data, $request) {
            $template->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'target_role' => $data['target_role'],
                'evaluation_period' => $data['evaluation_period'],
                'is_active' => $request->boolean('is_active', true),
            ]);
            $template->items()->delete();
            $this->syncItems($template, $request->input('items', []));
        });

        CompensationAuditService::log('kpi_template.updated', CompKpiTemplate::class, $template->id, $old, $template->fresh()->toArray());

        $applied = $this->handleAssignment($request, $template->fresh());

        return redirect()->route('crm.compensation.kpi.index')
            ->with('success', 'تم تحديث القالب' . ($applied ? " — تم تطبيقه على {$applied} موظف" : ''));
    }

    public function assign(Request $request, CompKpiTemplate $template)
    {
        $this->authorizeAdmin();

        $request->validate([
            'apply_assignment' => 'required|in:all_role,selected',
            'employee_ids' => 'required_if:apply_assignment,selected|array',
            'employee_ids.*' => 'exists:users,id',
        ]);

        $applied = $this->handleAssignment($request, $template);

        return back()->with('success', "تم ربط القالب بـ {$applied} موظف");
    }

    protected function formViewData(?CompKpiTemplate $template, string $role): array
    {
        $repSlugs = config('compensation.rep_kpi_slugs', []);
        $mgrSlugs = config('compensation.manager_kpi_slugs', []);

        $buildCatalog = fn (array $slugs, array $defaults) => collect($defaults)->map(function ($row) use ($slugs) {
            $slug = $row['slug'];

            return [
                'slug' => $slug,
                'name' => $slugs[$slug] ?? $slug,
                'weight' => $row['weight'],
                'target_value' => $row['target_value'],
            ];
        })->values()->all();

        $employees = User::role(array_merge(
            CrmEmployeeService::LEGACY_MANAGER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
        ))->orderBy('name')->get(['id', 'name']);

        $assignedCount = $template
            ? CompEmployeeProfile::where('kpi_template_id', $template->id)->count()
            : 0;

        return [
            'template' => $template,
            'role' => $role,
            'repCatalog' => $buildCatalog($repSlugs, config('compensation.rep_kpi_defaults', [])),
            'managerCatalog' => $buildCatalog($mgrSlugs, config('compensation.manager_kpi_defaults', [])),
            'periodLabels' => config('compensation.evaluation_period_labels', []),
            'roleLabels' => config('compensation.target_role_labels', []),
            'employees' => $employees,
            'assignedCount' => $assignedCount,
        ];
    }

    protected function handleAssignment(Request $request, CompKpiTemplate $template): int
    {
        $mode = $request->input('apply_assignment', 'none');

        if ($mode === 'all_role') {
            return $this->applyTemplateToRole($template);
        }

        if ($mode === 'selected') {
            return $this->applyTemplateToUsers($template, $request->input('employee_ids', []));
        }

        return 0;
    }

    protected function applyTemplateToRole(CompKpiTemplate $template): int
    {
        $roles = $template->target_role === 'manager'
            ? CrmEmployeeService::LEGACY_MANAGER_ROLES
            : CrmEmployeeService::LEGACY_EMPLOYEE_ROLES;

        $userIds = User::role($roles)->pluck('id');

        return $this->applyTemplateToUsers($template, $userIds->all());
    }

    /** @param  array<int|string>  $userIds */
    protected function applyTemplateToUsers(CompKpiTemplate $template, array $userIds): int
    {
        $count = 0;
        foreach (array_unique(array_map('intval', $userIds)) as $userId) {
            if ($userId < 1) {
                continue;
            }
            $user = User::find($userId);
            if (!$user) {
                continue;
            }

            CompEmployeeProfile::updateOrCreate(
                ['user_id' => $userId],
                [
                    'kpi_template_id' => $template->id,
                    'base_salary' => $user->employee?->salary ?? 0,
                    'is_active' => true,
                ],
            );
            $count++;
        }

        if ($count > 0) {
            CompensationAuditService::log('kpi_template.assigned', CompKpiTemplate::class, $template->id, null, [
                'users' => $count,
            ]);
        }

        return $count;
    }

    protected function syncItems(CompKpiTemplate $template, array $items): void
    {
        $order = 0;
        foreach ($items as $row) {
            if (empty($row['slug']) || empty($row['name'])) {
                continue;
            }
            CompKpiItem::create([
                'template_id' => $template->id,
                'slug' => $row['slug'],
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'weight' => (float) ($row['weight'] ?? 0),
                'target_value' => (float) ($row['target_value'] ?? 0),
                'sort_order' => $order++,
            ]);
        }
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_role' => 'required|in:rep,manager',
            'evaluation_period' => 'required|in:daily,weekly,monthly,quarterly',
            'items' => 'required|array|min:1',
            'items.*.slug' => 'required|string|max:64',
            'items.*.name' => 'required|string|max:255',
            'items.*.weight' => 'required|numeric|min:0|max:100',
            'items.*.target_value' => 'required|numeric|min:0',
            'apply_assignment' => 'nullable|in:none,all_role,selected',
            'employee_ids' => 'required_if:apply_assignment,selected|array|min:1',
            'employee_ids.*' => 'exists:users,id',
        ], [
            'employee_ids.required_if' => 'اختر موظفاً واحداً على الأقل عند التطبيق على موظفين محددين',
            'employee_ids.min' => 'اختر موظفاً واحداً على الأقل',
        ]);

        $total = collect($request->input('items', []))->sum(fn ($i) => (float) ($i['weight'] ?? 0));
        if (abs($total - 100) > 0.01) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'items' => 'مجموع الأوزان يجب أن يساوي 100%',
            ]);
        }

        return $data;
    }

    protected function authorizeAdmin(): void
    {
        if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }
    }
}

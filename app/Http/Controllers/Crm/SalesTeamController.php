<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\CrmScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesTeamController extends Controller
{
    public const STAGE_LABELS = [
        'lead' => 'عميل جديد',
        'prospect' => 'تم التواصل',
        'proposal' => 'معاينة',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم البيع',
        'closed_lost' => 'خسارة',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $scope = CrmScopeService::for($user);
        $isScopedManager = $this->isScopedManager($user);

        if ($isScopedManager) {
            $owned = $scope->managedTeamsQuery()->orderByDesc('id')->get();

            if ($owned->count() === 1) {
                return redirect()->route('crm.teams.show', $owned->first());
            }

            if ($owned->isEmpty()) {
                return redirect()->route('crm.teams.create');
            }
        }

        $query = $scope
            ->managedTeamsQuery()
            ->with(['manager', 'members'])
            ->withCount(['members', 'sales']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('manager', fn ($m) => $m->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $teams = $query->latest()->paginate(12)->withQueryString();

        $base = $scope->managedTeamsQuery();
        $stats = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('is_active', true)->count(),
            'members' => (int) DB::table('sales_team_members')
                ->whereIn('sales_team_id', (clone $base)->pluck('id'))
                ->distinct()
                ->count('user_id'),
            'deals' => $scope->salesQuery()
                ->whereIn('sales_team_id', (clone $base)->pluck('id'))
                ->count(),
        ];

        return view('crm.teams.index', [
            'teams' => $teams,
            'stats' => $stats,
            'canCreate' => $this->canCreateTeams($user),
            'canManageAllTeams' => $this->canManageAllTeams($user),
            'isScopedManager' => $isScopedManager,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($this->isScopedManager($user) && $user->managedSalesTeams()->exists()) {
            return redirect()
                ->route('crm.teams.show', $user->managedSalesTeams()->orderByDesc('id')->first())
                ->with('success', 'لديك فريق مبيعات مسجّل بالفعل.');
        }

        $this->authorizeCreate();

        return view('crm.teams.create', $this->formData());
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();

        $user = Auth::user();
        if ($this->isScopedManager($user)) {
            $request->merge(['manager_id' => $user->id]);
        }

        $validator = $this->validator($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $memberIds = $this->sanitizeMemberIds($user, $request->input('member_ids', []));

        $data = $this->teamAttributes($validator->validated(), null, $request);
        $team = SalesTeam::create($data);
        $team->members()->sync($memberIds);

        User::find($data['manager_id'])?->tap(
            fn (User $u) => CrmEmployeeService::assignSalesRole($u, CrmEmployeeService::ROLE_MANAGER)
        );

        return redirect()->route('crm.teams.show', $team)->with('success', 'تم إنشاء فريق المبيعات بنجاح');
    }

    public function show(SalesTeam $team)
    {
        $this->authorizeTeam($team);

        $team->load(['manager.employee', 'members.employee']);
        $team->loadCount(['members', 'sales']);

        $scopedSales = CrmScopeService::for(Auth::user())->salesQuery()
            ->where('sales_team_id', $team->id);

        $salesStats = (clone $scopedSales)
            ->selectRaw("stage, COUNT(*) as cnt, COALESCE(SUM(COALESCE(actual_value, estimated_value)), 0) as val")
            ->groupBy('stage')
            ->get()
            ->keyBy('stage');

        $team->setRelation(
            'sales',
            (clone $scopedSales)
                ->with(['client:id,name', 'salesRep:id,name'])
                ->latest()
                ->limit(15)
                ->get()
        );

        $pipelineValue = (float) (clone $scopedSales)
            ->whereNotIn('stage', ['closed_lost'])
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));

        $user = Auth::user();

        return view('crm.teams.show', [
            'team' => $team,
            'salesStats' => $salesStats,
            'pipelineValue' => $pipelineValue,
            'stageLabels' => self::STAGE_LABELS,
            'canManage' => $this->canManageTeam($user, $team),
            'canDelete' => $this->canDeleteTeam($user, $team),
            'isScopedManager' => $this->isScopedManager($user),
        ]);
    }

    public function edit(SalesTeam $team)
    {
        $this->authorizeTeam($team);

        $user = Auth::user();

        if (!$this->canManageTeam($user, $team)) {
            abort(403, 'لا يمكنك تعديل هذا الفريق.');
        }

        return view('crm.teams.edit', array_merge($this->formData($team), [
            'team' => $team->load('members'),
            'canManage' => true,
            'canDelete' => $this->canDeleteTeam($user, $team),
            'isScopedManager' => $this->isScopedManager($user),
        ]));
    }

    public function update(Request $request, SalesTeam $team)
    {
        $this->authorizeTeam($team);

        if (!$this->canManageTeam(Auth::user(), $team)) {
            abort(403);
        }

        $user = Auth::user();
        if ($this->isScopedManager($user)) {
            $request->merge(['manager_id' => $user->id]);
        }

        $validator = $this->validator($request, $team);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $memberIds = $this->sanitizeMemberIds($user, $request->input('member_ids', []), $team);

        $data = $this->teamAttributes($validator->validated(), $team, $request);
        $team->update($data);
        $team->members()->sync($memberIds);

        if ($team->wasChanged('manager_id')) {
            User::find($team->manager_id)?->tap(
                fn (User $u) => CrmEmployeeService::assignSalesRole($u, CrmEmployeeService::ROLE_MANAGER)
            );
        }

        return redirect()->route('crm.teams.show', $team)->with('success', 'تم تحديث الفريق بنجاح');
    }

    public function destroy(SalesTeam $team)
    {
        $this->authorizeTeam($team);
        $user = Auth::user();

        if (!$this->canDeleteTeam($user, $team)) {
            abort(403);
        }

        Sale::where('sales_team_id', $team->id)->update(['sales_team_id' => null]);
        $team->members()->detach();
        $team->delete();

        return redirect()->route('crm.teams.index')->with('success', 'تم حذف الفريق');
    }

    protected function formData(?SalesTeam $team = null): array
    {
        $user = Auth::user();
        $scope = CrmScopeService::for($user);
        $isScopedManager = $this->isScopedManager($user);

        if ($this->canManageAllTeams($user)) {
            $managers = User::role(array_merge(CrmEmployeeService::LEGACY_MANAGER_ROLES, ['super_admin', 'admin']))
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            $agents = User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        } else {
            $managers = User::whereKey($user->id)->get(['id', 'name', 'email']);

            $allowedIds = collect($scope->assignableTeamMemberUserIds());
            if ($team) {
                $allowedIds = $allowedIds->merge($team->members->pluck('id'));
            }

            $agents = User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                ->whereIn('id', $allowedIds->unique()->filter()->values())
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }

        return [
            'managers' => $managers,
            'agents' => $agents,
            'lockManager' => $isScopedManager,
            'isScopedManager' => $isScopedManager,
        ];
    }

    protected function validator(Request $request, ?SalesTeam $team = null): \Illuminate\Validation\Validator
    {
        $user = Auth::user();
        $rules = [
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ];

        if ($this->isScopedManager($user)) {
            $rules['manager_id'] = 'required|in:' . $user->id;
        }

        return Validator::make($request->all(), $rules, [
            'name.required' => 'اسم الفريق مطلوب',
            'manager_id.required' => 'يجب اختيار مدير المبيعات',
            'manager_id.in' => 'لا يمكنك تعيين فريق لمدير آخر.',
        ]);
    }

    protected function teamAttributes(array $validated, ?SalesTeam $team = null, ?Request $request = null): array
    {
        $user = Auth::user();
        $managerId = $this->isScopedManager($user)
            ? $user->id
            : (int) $validated['manager_id'];

        $attrs = [
            'name' => $validated['name'],
            'manager_id' => $managerId,
            'description' => $validated['description'] ?? null,
            'department_id' => $team?->department_id ?? CrmEmployeeService::salesDepartment()->id,
        ];

        if ($team && $request) {
            $attrs['is_active'] = $request->boolean('is_active');
        } else {
            $attrs['is_active'] = true;
        }

        return $attrs;
    }

    /** @param  array<int|string>  $memberIds */
    protected function sanitizeMemberIds(User $user, array $memberIds, ?SalesTeam $team = null): array
    {
        $memberIds = array_map('intval', $memberIds);

        if ($this->canManageAllTeams($user)) {
            return $memberIds;
        }

        $scope = CrmScopeService::for($user);
        $allowed = collect($scope->assignableTeamMemberUserIds());

        if ($team) {
            $allowed = $allowed->merge($team->members()->pluck('users.id'));
        }

        $allowed = $allowed->unique()->filter()->values()->all();

        return array_values(array_intersect($memberIds, $allowed));
    }

    protected function canManageAllTeams(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin']);
    }

    protected function ownsTeam(User $user, SalesTeam $team): bool
    {
        return (int) $team->manager_id === (int) $user->id;
    }

    protected function isScopedManager(User $user): bool
    {
        if ($this->canManageAllTeams($user)) {
            return false;
        }

        return CrmScopeService::for($user)->isManagerScope();
    }

    protected function canCreateTeams(User $user): bool
    {
        if ($this->canManageAllTeams($user)) {
            return true;
        }

        return $this->isScopedManager($user) && ! $user->managedSalesTeams()->exists();
    }

    protected function canManageTeam(User $user, SalesTeam $team): bool
    {
        return $this->canManageAllTeams($user);
    }

    protected function canDeleteTeam(User $user, SalesTeam $team): bool
    {
        return $this->canManageAllTeams($user);
    }

    protected function authorizeCreate(): void
    {
        if (!$this->canCreateTeams(Auth::user())) {
            abort(403, 'لا يمكنك إنشاء فرق المبيعات.');
        }
    }

    protected function authorizeTeam(SalesTeam $team): void
    {
        $user = Auth::user();

        if ($this->canManageAllTeams($user)) {
            return;
        }

        if ($this->ownsTeam($user, $team)) {
            return;
        }

        abort(403, 'لا يمكنك عرض هذا الفريق.');
    }
}

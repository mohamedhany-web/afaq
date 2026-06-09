<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Services\Crm\ClientTimelineService;
use App\Services\CrmScopeService;
use App\Services\Freelance\FreelanceCommissionSchemeService;
use App\Services\Freelance\SaleCommissionSplitService;
use App\Support\CrmLostReasonRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CrmPipelineController extends Controller
{
    protected array $stages = ['lead', 'prospect', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];

    public function __construct(
        protected SaleCommissionSplitService $commissionSplits,
        protected FreelanceCommissionSchemeService $commissionScheme,
    ) {}

    public const COLUMN_PAGE_SIZE = 15;

    public const CLIENT_COLUMN_PAGE_SIZE = 10;

    public function index(Request $request)
    {
        $scope = CrmScopeService::for(Auth::user());
        $filteredQuery = $this->filteredClientsQuery($request, $scope);
        $scopedSales = $scope->salesQuery();

        $stats = [
            'total' => (clone $filteredQuery)->count(),
            'prospect' => (clone $filteredQuery)->where('status', 'prospect')->count(),
            'active' => (clone $filteredQuery)->where('status', 'active')->count(),
            'with_deals' => (clone $filteredQuery)->whereIn('id', (clone $scopedSales)->distinct()->pluck('client_id'))->count(),
        ];

        $clients = $filteredQuery
            ->with(['createdBy:id,name'])
            ->withCount(['sales as scoped_sales_count' => function ($q) use ($scope) {
                $this->applySaleScopeToRelation($q, $scope);
            }])
            ->orderByDesc('updated_at')
            ->paginate(24)
            ->withQueryString();

        return view('crm.pipeline.index', [
            'clients' => $clients,
            'stats' => $stats,
            'stageLabels' => $this->stageLabels(),
            'statusLabels' => $this->clientStatusLabels(),
        ]);
    }

    public function showClient(Client $client)
    {
        $scope = CrmScopeService::for(Auth::user());
        if (!$scope->clientsQuery()->where('id', $client->id)->exists()) {
            abort(403, 'لا يمكنك الوصول إلى هذا العميل.');
        }

        $client->load([
            'assignedEmployee:id,first_name,last_name',
            'createdBy:id,name',
            'sales' => function ($q) use ($scope) {
                $this->applySaleScopeToRelation($q, $scope);
                $q->with(['project:id,name', 'salesRep:id,name'])
                    ->orderByDesc('updated_at');
            },
        ]);

        $dealsQuery = $scope->salesQuery()->where('client_id', $client->id);
        $stageLabels = $this->stageLabels();
        $stageColors = [
            'lead' => ['bg' => '#6366f1', 'light' => '#eef2ff'],
            'prospect' => ['bg' => '#3b82f6', 'light' => '#eff6ff'],
            'proposal' => ['bg' => '#0ea5e9', 'light' => '#f0f9ff'],
            'negotiation' => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
            'closed_won' => ['bg' => '#16a34a', 'light' => '#f0fdf4'],
            'closed_lost' => ['bg' => '#ef4444', 'light' => '#fef2f2'],
        ];

        $dealColumns = [];
        $dealStageTotals = [];
        foreach ($this->stages as $stage) {
            $items = (clone $dealsQuery)->where('stage', $stage)->get();
            $dealColumns[$stage] = $items;
            $dealStageTotals[$stage] = [
                'count' => $items->count(),
                'value' => (float) $items->sum('estimated_value'),
            ];
        }

        $activeStages = ['lead', 'prospect', 'proposal', 'negotiation'];
        $closedStages = ['closed_won', 'closed_lost'];

        return view('crm.pipeline.client', [
            'client' => $client,
            'stageLabels' => $stageLabels,
            'dealColumns' => $dealColumns,
            'dealStageTotals' => $dealStageTotals,
            'stageColors' => $stageColors,
            'activeStages' => $activeStages,
            'closedStages' => $closedStages,
            'interactionTypes' => $this->interactionTypes(),
            'dealsCount' => $client->sales->count(),
            'dealsValue' => $client->sales->whereNotIn('stage', ['closed_lost'])->sum('estimated_value'),
        ]);
    }

    protected function dealsKanbanView(Request $request)
    {
        $scope = CrmScopeService::for(Auth::user());
        $filteredQuery = $this->filteredSalesQuery($request, $scope);

        $stageLabels = $this->stageLabels();
        $activeStages = ['lead', 'prospect', 'proposal', 'negotiation'];
        $closedStages = ['closed_won', 'closed_lost'];
        $showClosed = $request->boolean('show_closed');

        $stats = [
            'total' => (clone $filteredQuery)->count(),
            'active' => (clone $filteredQuery)->whereIn('stage', $activeStages)->count(),
            'won' => (clone $filteredQuery)->where('stage', 'closed_won')->count(),
            'lost' => (clone $filteredQuery)->where('stage', 'closed_lost')->count(),
            'pipeline_value' => (float) (clone $filteredQuery)->whereNotIn('stage', ['closed_lost'])->sum('estimated_value'),
            'won_value' => (float) (clone $filteredQuery)->where('stage', 'closed_won')
                ->sum(DB::raw('COALESCE(actual_value, estimated_value)')),
        ];

        $aggregates = (clone $filteredQuery)
            ->selectRaw('stage, COUNT(*) as cnt, COALESCE(SUM(estimated_value), 0) as val')
            ->groupBy('stage')
            ->get()
            ->keyBy('stage');

        $stagesForItems = $request->filled('stage')
            ? [$request->stage]
            : array_merge($activeStages, $showClosed ? $closedStages : []);

        $columns = [];
        $stageTotals = [];

        foreach ($this->stages as $stage) {
            $agg = $aggregates->get($stage);
            $total = (int) ($agg->cnt ?? 0);
            $stageTotals[$stage] = [
                'count' => $total,
                'value' => (float) ($agg->val ?? 0),
            ];

            if (!in_array($stage, $stagesForItems, true)) {
                $columns[$stage] = [
                    'items' => collect(),
                    'total' => $total,
                    'has_more' => false,
                    'deferred' => true,
                ];
                continue;
            }

            $stageQuery = (clone $filteredQuery)->where('stage', $stage);
            $items = (clone $stageQuery)
                ->with(['client:id,name', 'project:id,name', 'salesRep:id,name'])
                ->select([
                    'id', 'client_id', 'project_id', 'assigned_to', 'product_service',
                    'estimated_value', 'probability_percentage', 'stage', 'updated_at',
                ])
                ->orderByDesc('updated_at')
                ->limit(self::COLUMN_PAGE_SIZE)
                ->get();

            $columns[$stage] = [
                'items' => $items,
                'total' => $total,
                'has_more' => $total > self::COLUMN_PAGE_SIZE,
                'deferred' => false,
            ];
        }

        return view('crm.pipeline.index-deals', [
            'columns' => $columns,
            'stageLabels' => $stageLabels,
            'activeStages' => $activeStages,
            'closedStages' => $closedStages,
            'stats' => $stats,
            'stageTotals' => $stageTotals,
            'columnPageSize' => self::COLUMN_PAGE_SIZE,
            'showClosed' => $showClosed,
        ]);
    }

    public function columnDeals(Request $request, string $stage)
    {
        if (!in_array($stage, $this->stages, true)) {
            abort(404);
        }

        $scope = CrmScopeService::for(Auth::user());
        $page = max(1, (int) $request->get('page', 1));
        $stageQuery = $this->filteredSalesQuery($request, $scope)->where('stage', $stage);
        $total = (clone $stageQuery)->count();

        $deals = (clone $stageQuery)
            ->with(['client:id,name', 'project:id,name', 'salesRep:id,name'])
            ->select([
                'id', 'client_id', 'project_id', 'assigned_to', 'product_service',
                'estimated_value', 'probability_percentage', 'stage', 'updated_at',
            ])
            ->orderByDesc('updated_at')
            ->forPage($page, self::COLUMN_PAGE_SIZE)
            ->get();

        $stageColors = $this->stageColors();
        $accentColor = $stageColors[$stage] ?? \App\Helpers\SettingsHelper::getThemeColor();

        $html = $deals->map(fn (Sale $deal) => view('crm.pipeline.partials.card', [
            'deal' => $deal,
            'accentColor' => $accentColor,
        ])->render())->implode('');

        $loaded = $page * self::COLUMN_PAGE_SIZE;

        return response()->json([
            'html' => $html,
            'has_more' => $loaded < $total,
            'remaining' => max(0, $total - $loaded),
            'total' => $total,
        ]);
    }

    protected function filteredClientsQuery(Request $request, CrmScopeService $scope): Builder
    {
        $scopedSaleClientIds = fn () => $scope->salesQuery()->distinct()->pluck('client_id');

        return $scope->clientsQuery()
            ->when($request->search, function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('company_name', 'like', $search);
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->lead_stage, fn ($q) => $q->where('lead_stage', $request->lead_stage))
            ->when($request->deal_stage, function ($q) use ($request, $scope) {
                $ids = $scope->salesQuery()->where('stage', $request->deal_stage)->distinct()->pluck('client_id');
                $q->whereIn('id', $ids);
            })
            ->when($request->has_deals === '1', fn ($q) => $q->whereIn('id', $scopedSaleClientIds()))
            ->when($request->has_deals === '0', fn ($q) => $q->whereNotIn('id', $scopedSaleClientIds()));
    }

    protected function filteredSalesQuery(Request $request, CrmScopeService $scope): Builder
    {
        return $scope->salesQuery()
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $search = '%' . $request->search . '%';
                    $query->where('product_service', 'like', $search)
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', $search))
                        ->orWhereHas('project', fn ($p) => $p->where('name', 'like', $search));
                });
            })
            ->when($request->stage, fn ($q) => $q->where('stage', $request->stage));
    }

    protected function clientEagerLoads(CrmScopeService $scope): array
    {
        return [
            'assignedEmployee:id,first_name,last_name',
            'sales' => function ($q) use ($scope) {
                $this->applySaleScopeToRelation($q, $scope);
                $q->with(['project:id,name'])
                    ->select([
                        'id', 'client_id', 'project_id', 'product_service',
                        'estimated_value', 'probability_percentage', 'stage', 'viewing_date', 'updated_at',
                    ])
                    ->orderByDesc('updated_at')
                    ->limit(8);
            },
        ];
    }

    protected function applySaleScopeToRelation($query, CrmScopeService $scope): void
    {
        if ($scope->hasFullAccess()) {
            return;
        }

        if ($scope->isManagerScope()) {
            $query->whereIn('assigned_to', $scope->managedTeamMemberUserIds());

            return;
        }

        $query->where('assigned_to', Auth::id());
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();

        return view('crm.pipeline.create', [
            'projects' => $projects,
            'stages' => $this->stages,
            'stageLabels' => $this->stageLabels(),
            'leadSources' => ['website', 'referral', 'walk_in', 'social_media', 'advertisement', 'call', 'other'],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'product_service' => 'required|string|max:255',
            'estimated_value' => 'required|numeric|min:0',
            'stage' => 'required|in:' . implode(',', $this->stages),
            'probability_percentage' => 'required|numeric|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'lead_source' => 'nullable|string',
            'unit_type' => 'nullable|string|max:100',
            'interest_type' => 'nullable|string|max:100',
            'viewing_date' => 'nullable|date',
            'viewing_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $scope = CrmScopeService::for(Auth::user());
        $scope->assertClientInScope((int) $request->client_id);

        $user = Auth::user();
        $team = SalesTeam::where('manager_id', $user->id)->first()
            ?? $user->salesTeams()->first();

        $sale = Sale::create(array_merge($validator->validated(), [
            'assigned_to' => $user->id,
            'sales_team_id' => $team?->id,
        ]));

        $client = Client::find($request->client_id);
        app(ClientTimelineService::class)->record(
            $client,
            'deal_created',
            'إنشاء صفقة جديدة',
            $sale->product_service,
            $user,
            'sales',
            Sale::class,
            $sale->id,
            ['value' => $sale->estimated_value, 'stage' => $sale->stage],
        );

        return redirect()
            ->route('crm.pipeline.client', $client)
            ->with('success', 'تم إضافة الصفقة بنجاح');
    }

    public function show(Sale $sale)
    {
        $this->authorizeSale($sale);
        $sale->load(['client', 'project', 'salesRep', 'salesTeam', 'listingAgent', 'commissionSplits.user']);
        $commissionPreview = $this->commissionScheme->previewForSale($sale);

        return view('crm.pipeline.show', compact('sale', 'commissionPreview'));
    }

    public function edit(Sale $sale)
    {
        $this->authorizeSale($sale);
        $sale->load('client:id,name,phone,company_name');
        $projects = Project::orderBy('name')->get();

        $agents = \App\Models\User::role(\App\Services\CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('crm.pipeline.edit', [
            'sale' => $sale,
            'projects' => $projects,
            'stages' => $this->stages,
            'agents' => $agents,
            'transactionTypes' => config('freelance_agents.transaction_types'),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        $this->authorizeSale($sale);

        $validator = Validator::make($request->all(), array_merge([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'product_service' => 'required|string|max:255',
            'estimated_value' => 'required|numeric|min:0',
            'actual_value' => 'nullable|numeric|min:0',
            'probability_percentage' => 'required|numeric|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'unit_type' => 'nullable|string|max:100',
            'interest_type' => 'nullable|string|max:100',
            'viewing_date' => 'nullable|date',
            'viewing_notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'actual_close_date' => 'nullable|date',
            'transaction_type' => 'nullable|in:' . implode(',', array_keys(config('freelance_agents.transaction_types', []))),
            'company_commission_amount' => 'nullable|numeric|min:0',
            'listing_agent_id' => 'nullable|exists:users,id',
            'commission_collected' => 'nullable|boolean',
            'commission_notes' => 'nullable|string',
        ], CrmLostReasonRules::stageRules('stage', $this->stages)));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $scope = CrmScopeService::for(Auth::user());
        $scope->assertClientInScope((int) $request->client_id);

        $from = $sale->stage;
        $to = $validator->validated()['stage'];

        $payload = array_merge(
            $validator->validated(),
            CrmLostReasonRules::applyLostFields($request->only(['lost_reason', 'lost_reason_notes']), $to),
            ['commission_collected' => $request->boolean('commission_collected')],
        );

        if ($to === 'closed_won' && empty($payload['actual_close_date'])) {
            $payload['actual_close_date'] = now()->toDateString();
        }

        $sale->update($payload);

        if ($sale->commission_collected && !$sale->commission_collected_at) {
            $this->commissionSplits->markCollected($sale->fresh());
        }

        $this->commissionSplits->syncForSale($sale->fresh());

        if ($from !== $to) {
            $sale->load('client');
            app(ClientTimelineService::class)->recordDealStageChange(
                $sale,
                $from,
                $to,
                Auth::user(),
                $request->lost_reason,
                $request->lost_reason_notes,
            );
        }

        return redirect()->route('crm.pipeline.show', $sale)->with('success', 'تم تحديث الصفقة');
    }

    public function updateStage(Request $request, Sale $sale)
    {
        $this->authorizeSale($sale);

        $request->validate(CrmLostReasonRules::stageRules('stage', $this->stages));

        $from = $sale->stage;
        $to = $request->stage;

        $extra = [];
        if ($to === 'closed_won' && !$sale->actual_close_date) {
            $extra['actual_close_date'] = now()->toDateString();
        }

        $sale->update(array_merge(
            ['stage' => $to],
            $extra,
            CrmLostReasonRules::applyLostFields($request->only(['lost_reason', 'lost_reason_notes']), $to),
        ));

        if ($from !== $to) {
            $sale->load('client');
            app(ClientTimelineService::class)->recordDealStageChange(
                $sale,
                $from,
                $to,
                Auth::user(),
                $request->lost_reason,
                $request->lost_reason_notes,
            );
        }

        if ($to === 'closed_won') {
            $this->commissionSplits->syncForSale($sale->fresh());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'stage' => $sale->stage]);
        }

        return back()->with('success', 'تم تحديث مرحلة الصفقة');
    }

    protected function authorizeSale(Sale $sale): void
    {
        CrmScopeService::for(Auth::user())->assertSaleInScope($sale);
    }

    protected function stageLabels(): array
    {
        return [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];
    }

    protected function clientStatusLabels(): array
    {
        return [
            'prospect' => 'محتمل',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
        ];
    }

    protected function interactionTypes(): array
    {
        return [
            'call' => 'مكالمة',
            'meeting' => 'اجتماع',
            'viewing' => 'معاينة عقار',
            'follow_up' => 'متابعة',
            'note' => 'ملاحظة',
        ];
    }

    protected function stageColors(): array
    {
        return [
            'lead' => '#6366f1',
            'prospect' => '#3b82f6',
            'proposal' => '#0ea5e9',
            'negotiation' => '#f59e0b',
            'closed_won' => '#16a34a',
            'closed_lost' => '#ef4444',
        ];
    }
}

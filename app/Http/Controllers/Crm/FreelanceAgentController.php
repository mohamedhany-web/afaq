<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\FreelanceAgentContract;
use App\Models\Sale;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\Freelance\FreelanceAgentContractService;
use App\Services\Freelance\FreelanceCommissionSchemeService;
use App\Services\Freelance\SaleCommissionSplitService;
use Illuminate\Http\Request;

class FreelanceAgentController extends Controller
{
    public function __construct(
        protected FreelanceAgentContractService $contracts,
        protected FreelanceCommissionSchemeService $scheme,
        protected SaleCommissionSplitService $splits,
    ) {}

    public function index(Request $request)
    {
        $contracts = FreelanceAgentContract::query()->with('user')
            ->when($request->search, fn ($q) => $q->whereHas('user', function ($u) use ($request) {
                $u->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            }))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => FreelanceAgentContract::count(),
            'active' => FreelanceAgentContract::where('status', 'active')->count(),
            'with_target' => FreelanceAgentContract::where('status', 'active')
                ->where(function ($q) {
                    $q->whereNotNull('quarterly_target_amount')->orWhereNotNull('quarterly_target_deals');
                })
                ->count(),
        ];

        return view('crm.freelance-agents.index', compact('contracts', 'stats'));
    }

    public function scheme()
    {
        return view('crm.freelance-agents.scheme', [
            'rows' => config('freelance_agents.scheme_table', []),
        ]);
    }

    public function create()
    {
        return view('crm.freelance-agents.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->contracts->validate($request);
        $contract = $this->contracts->create($data, $request->user());

        return redirect()
            ->route('crm.freelance-agents.show', $contract)
            ->with('success', 'تم تسجيل عقد الوكيل المستقل بنجاح');
    }

    public function show(FreelanceAgentContract $contract)
    {
        $contract->load('user');

        $recentSplits = \App\Models\SaleCommissionSplit::query()
            ->where('user_id', $contract->user_id)
            ->with(['sale.client', 'sale.project'])
            ->latest()
            ->limit(10)
            ->get();

        $metTarget = $this->scheme->agentMetQuarterlyTarget($contract->user_id);

        return view('crm.freelance-agents.show', [
            'contract' => $contract,
            'recentSplits' => $recentSplits,
            'metTarget' => $metTarget,
        ]);
    }

    public function edit(FreelanceAgentContract $contract)
    {
        return view('crm.freelance-agents.edit', array_merge($this->formData(), [
            'contract' => $contract->load('user'),
        ]));
    }

    public function update(Request $request, FreelanceAgentContract $contract)
    {
        $data = $this->contracts->validate($request, $contract);
        $this->contracts->update($contract, $data);

        return redirect()
            ->route('crm.freelance-agents.show', $contract)
            ->with('success', 'تم تحديث عقد الوكيل');
    }

    public function contractPrint(FreelanceAgentContract $contract)
    {
        $contract->load('user');

        return view('crm.freelance-agents.contract-print', [
            'contract' => $contract,
            'companyName' => \App\Helpers\SettingsHelper::getCompanyName(),
        ]);
    }

    public function markCommissionCollected(Sale $sale)
    {
        abort_unless(auth()->user()?->hasRole(['super_admin', 'admin', 'sales_manager']), 403);

        $this->splits->markCollected($sale);
        $this->splits->syncForSale($sale->fresh());

        return back()->with('success', 'تم تسجيل تحصيل العمولة — جاهزة للصرف للوكيل');
    }

    /** @return array<string, mixed> */
    protected function formData(): array
    {
        $agents = User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return [
            'agents' => $agents,
            'statuses' => config('freelance_agents.contract_statuses'),
        ];
    }
}

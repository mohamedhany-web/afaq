<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Crm\Concerns\UsesCrmFilters;
use App\Models\Client;
use App\Models\MarketingCampaign;
use App\Services\ClientApprovalService;
use App\Services\ClientManagementService;
use App\Services\Crm\ClientImportService;
use App\Services\Crm\ClientTimelineService;
use App\Services\Crm\ClientTransferService;
use App\Services\CrmScopeService;
use App\Services\Operations\OperationsClientBucketService;
use App\Services\Operations\OperationsKpiService;
use App\Services\Operations\OperationsLeadDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OperationsClientController extends Controller
{
    use UsesCrmFilters;

    public function __construct(
        protected OperationsClientBucketService $buckets,
        protected OperationsLeadDistributionService $distribution,
        protected OperationsKpiService $kpis,
        protected ClientManagementService $clients,
        protected ClientApprovalService $approval,
    ) {
        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $view = $request->get('view', 'data') === 'distribution' ? 'distribution' : 'data';

        if ($view === 'distribution') {
            return $this->distributionView($request);
        }

        return $this->dataView($request);
    }

    protected function dataView(Request $request)
    {
        $filters = $this->crmFilters($request);
        $bucket = $this->buckets->resolveBucket($request->get('bucket', OperationsClientBucketService::BUCKET_ALL));
        $baseQuery = $this->buckets->applyBucket($filters->scope()->clientsQuery(), $bucket);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'prospect' => (clone $baseQuery)->where('status', 'prospect')->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'with_deals' => (clone $baseQuery)->whereHas('sales')->count(),
        ];

        $clients = $filters->applyClientFilters(
            $baseQuery->with(['assignedEmployee', 'createdBy:id,name', 'sales']),
            $request,
        )
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $statusLabels = [
            'prospect' => 'محتمل',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
        ];

        $stageLabels = CrmScopeService::leadStageLabels();
        $labels = $this->buckets->labels();
        $bucketCounts = collect($labels)->mapWithKeys(
            fn ($label, $key) => [$key => $this->buckets->count($key)]
        );

        $assignableReps = $this->distribution->assignableReps(Auth::user());
        $selectedSalesRep = null;
        if ($request->filled('sales_rep')) {
            $selectedSalesRep = ($filters->salesReps())->firstWhere('id', (int) $request->sales_rep);
        }

        return view('operations.clients.index', [
            'view' => 'data',
            'clients' => $clients,
            'stats' => $stats,
            'bucket' => $bucket,
            'bucketLabels' => $labels,
            'bucketCounts' => $bucketCounts,
            'assignableReps' => $assignableReps,
            'selectedSalesRep' => $selectedSalesRep,
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'clientsRoutePrefix' => 'operations.clients',
            'clearUrl' => route('operations.clients.index', array_filter(['view' => 'data', 'bucket' => $request->get('bucket')])),
            'unassignedCount' => $this->distribution->unassignedLeadsQuery()->count(),
            ...$this->clientFilterViewData($filters, $request, $stageLabels, $statusLabels),
        ]);
    }

    protected function distributionView(Request $request)
    {
        $filter = $request->get('filter', 'unassigned');

        $baseQuery = match ($filter) {
            'stale' => Client::query()
                ->whereNull('assigned_to')
                ->where('updated_at', '<', now()->subDays(3))
                ->orderByDesc('updated_at'),
            default => $this->distribution->unassignedLeadsQuery(),
        };

        $search = trim((string) $request->search);
        $leads = (clone $baseQuery)
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $s = '%' . $search . '%';
                $q->where('name', 'like', $s)->orWhere('phone', 'like', $s);
            }))
            ->paginate(20)
            ->withQueryString();

        $kpiData = $this->kpis->collect();

        return view('operations.clients.index', [
            'view' => 'distribution',
            'leads' => $leads,
            'filter' => $filter,
            'search' => $search,
            'reps' => $this->distribution->assignableReps(Auth::user()),
            'repLoads' => $this->distribution->repLoads(Auth::user()),
            'leadKpis' => $kpiData['groups']['lead_management'] ?? null,
            'stats' => [
                'unassigned' => $this->distribution->unassignedLeadsQuery()->count(),
                'stale' => Client::query()
                    ->whereNull('assigned_to')
                    ->where('updated_at', '<', now()->subDays(3))
                    ->count(),
            ],
            'unassignedCount' => $this->distribution->unassignedLeadsQuery()->count(),
            'clientsRoutePrefix' => 'operations.clients',
        ]);
    }

    public function checkPhone(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $request->validate([
            'phone' => 'required|string|max:50',
            'ignore_id' => 'nullable|integer|exists:clients,id',
        ]);

        $duplicate = Client::findByNormalizedPhone($request->phone, $request->integer('ignore_id') ?: null);
        if (! $duplicate) {
            return response()->json(['duplicate' => false]);
        }

        $duplicate->loadMissing('assignedEmployee');

        return response()->json([
            'duplicate' => true,
            'client' => $duplicate->duplicateSummary(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Client::class);

        $filters = $this->crmFilters($request);
        $query = $filters->applyClientFilters(
            $filters->scope()->clientsQuery()->with(['assignedEmployee']),
            $request,
        )->latest('id');

        $stageLabels = CrmScopeService::leadStageLabels();
        $statusLabels = [
            'prospect' => 'محتمل',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
        ];

        $filename = 'clients-' . now()->format('Y-m-d-His') . '.csv';
        if ($request->filled('sales_rep')) {
            $rep = $filters->salesReps()->firstWhere('id', (int) $request->sales_rep);
            if ($rep) {
                $filename = 'clients-' . Str::slug($rep->name, '-') . '-' . now()->format('Y-m-d') . '.csv';
            }
        }

        return response()->streamDownload(function () use ($query, $stageLabels, $statusLabels) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['الاسم', 'الهاتف', 'البريد', 'الشركة', 'الحالة', 'مرحلة الرحلة', 'المصدر', 'السيلز', 'تاريخ الإضافة']);

            $query->chunkById(200, function ($clients) use ($out, $stageLabels, $statusLabels) {
                foreach ($clients as $client) {
                    fputcsv($out, [
                        $client->name,
                        $client->phone,
                        $client->email,
                        $client->company_name,
                        $statusLabels[$client->status] ?? $client->status,
                        $stageLabels[$client->lead_stage] ?? $client->lead_stage,
                        $client->leadSourceLabel(),
                        $client->assignedSalesRepName(),
                        $client->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function bulkTransfer(Request $request, ClientTransferService $transfers)
    {
        $this->authorize('bulkUpdate', Client::class);

        $validated = $request->validate([
            'client_ids' => 'required|array|min:1|max:200',
            'client_ids.*' => 'integer|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'transfer_tasks' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $scope = CrmScopeService::for($user);
        $clients = $scope->clientsQuery()->whereIn('id', $validated['client_ids'])->get();
        $transferTasks = $request->boolean('transfer_tasks', true);

        $result = $transfers->transferMany(
            $clients,
            (int) $validated['employee_id'],
            $user,
            $request,
            $transferTasks,
        );

        $message = "تم تحويل {$result['transferred']} عميل";
        if ($result['tasks_transferred'] > 0) {
            $message .= " و{$result['tasks_transferred']} مهمة مرتبطة";
        }
        $message .= " — متخطى: {$result['skipped']}.";

        return back()->with('success', $message);
    }

    public function transfer(Request $request, Client $client, ClientTransferService $transfers)
    {
        $this->authorize('view', $client);
        $this->authorize('transfer', $client);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'transfer_tasks' => 'nullable|boolean',
        ]);

        $result = $transfers->transfer(
            $client,
            (int) $validated['employee_id'],
            Auth::user(),
            $request,
            $request->boolean('transfer_tasks', true),
        );

        $message = 'تم تحويل العميل إلى السيلز المحدد.';
        if ($result['tasks_transferred'] > 0) {
            $message .= " تم تحويل {$result['tasks_transferred']} مهمة مرتبطة.";
        }

        return back()->with('success', $message);
    }

    public function create()
    {
        return view('operations.clients.create', [
            'client' => new \App\Models\Client(),
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'marketingCampaigns' => MarketingCampaign::query()->orderBy('name')->get(['id', 'name']),
            'clientsRoutePrefix' => 'operations.clients',
        ]);
    }

    public function importTemplate(ClientImportService $import)
    {
        return $import->downloadTemplate();
    }

    public function import(Request $request, ClientImportService $import)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'duplicate_mode' => 'nullable|in:skip,update',
        ]);

        $result = $import->import(
            $request->file('file'),
            Auth::user(),
            $request->input('duplicate_mode', 'skip')
        );

        $message = sprintf(
            'تم الاستيراد: %d جديد · %d محدّث · %d متخطى · %d فشل',
            $result['imported'],
            $result['updated'],
            $result['skipped'],
            $result['failed']
        );

        return redirect()
            ->route('operations.clients.create', ['tab' => 'import'])
            ->with($result['failed'] > 0 && $result['imported'] === 0 ? 'error' : 'success', $message)
            ->with('import_result', $result);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $this->clients->prepareData($this->clients->validate($request), $user, true);
        $client = Client::create($data);

        app(ClientTimelineService::class)->recordLeadCreated($client, $user);

        return redirect()
            ->route('operations.clients.index', ['view' => 'data'])
            ->with('success', 'تم إضافة العميل بنجاح');
    }
}

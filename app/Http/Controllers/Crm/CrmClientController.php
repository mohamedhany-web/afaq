<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientDeletionBatch;
use App\Models\Employee;
use App\Models\MarketingCampaign;
use App\Services\Crm\ClientActivityService;
use App\Services\Crm\ClientTransferService;
use App\Services\Crm\ClientImportService;
use App\Services\Crm\ClientTimelineService;
use App\Services\ClientApprovalService;
use App\Services\ClientManagementService;
use App\Services\CrmScopeService;
use App\Support\CrmLostReasonRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Http\Controllers\Crm\Concerns\UsesCrmFilters;

class CrmClientController extends Controller
{
    use UsesCrmFilters;
    public function __construct(
        protected ClientManagementService $clients,
        protected ClientApprovalService $approval,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $filters = $this->crmFilters($request);
        $baseQuery = $filters->scope()->clientsQuery();

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
            ->paginate(15)
            ->withQueryString();

        $statusLabels = [
            'prospect' => 'محتمل',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
        ];

        $stageLabels = \App\Services\CrmScopeService::leadStageLabels();
        $assignableReps = (Auth::user()->can('transfer-clients') || Auth::user()->canAccessOperations())
            ? app(\App\Services\Operations\OperationsLeadDistributionService::class)->assignableReps(Auth::user())
            : collect();

        $selectedSalesRep = null;
        if ($request->filled('sales_rep')) {
            $selectedSalesRep = ($filters->salesReps())->firstWhere('id', (int) $request->sales_rep);
        }

        return view('crm.clients.index', [
            'clients' => $clients,
            'stats' => $stats,
            'assignableReps' => $assignableReps,
            'selectedSalesRep' => $selectedSalesRep,
            'requiresApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'clearUrl' => route('crm.clients.index'),
            ...$this->clientFilterViewData($filters, $request, $stageLabels, $statusLabels),
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
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create()
    {
        return view('crm.clients.create', [
            'requiresApproval' => false,
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'marketingCampaigns' => $this->marketingCampaignOptions(),
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
        ], [
            'file.required' => 'يرجى اختيار ملف Excel أو CSV.',
            'file.mimes' => 'الصيغ المدعومة: xlsx, xls, csv',
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
            ->route('crm.clients.create', ['tab' => 'import'])
            ->with($result['failed'] > 0 && $result['imported'] === 0 ? 'error' : 'success', $message)
            ->with('import_result', $result);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $this->clients->prepareData($this->clients->validate($request), $user, true);
        $client = Client::create($data);

        app(ClientTimelineService::class)->recordLeadCreated($client, $user);

        app(ClientActivityService::class)->log(
            $client,
            $user,
            'client_created',
            'إضافة عميل جديد: ' . $client->name,
            null,
            ['name' => $client->name, 'phone' => $client->phone],
            $request,
        );

        return $this->redirectAfterClientMutation($client, 'تم إضافة العميل بنجاح');
    }

    protected function redirectAfterClientMutation(Client $client, string $message)
    {
        $user = Auth::user();

        if ($user->can('viewAny', Client::class)) {
            return redirect()->route('crm.clients.index')->with('success', $message);
        }

        return redirect()->route('crm.pipeline.client', $client)->with('success', $message);
    }

    public function show(Client $client)
    {
        $this->authorizeClient($client);

        if (! Auth::user()->can('viewFullDetails', $client)) {
            return redirect()->route('crm.pipeline.client', $client);
        }

        $client->load(['sales.project', 'sales.salesRep', 'assignedEmployee.user', 'createdBy:id,name', 'staffNotes.user:id,name', 'marketingCampaign']);

        $timeline = app(ClientTimelineService::class)->buildForClient($client);
        $activityLogs = Auth::user()->can('viewActivityLog', $client)
            ? app(ClientActivityService::class)->logsForClient($client)
            : collect();
        $portalHub = app(\App\Services\ClientPortalHubService::class)->summaryForClient($client);
        $lostReasons = config('crm_intelligence.lost_reasons');
        $relatedProjects = $client->sales
            ->pluck('project')
            ->filter()
            ->unique('id')
            ->values();
        $assignableReps = (Auth::user()->can('transfer-clients') || Auth::user()->canAccessOperations())
            ? app(\App\Services\Operations\OperationsLeadDistributionService::class)->assignableReps(Auth::user())
            : collect();

        return view('crm.clients.show', [
            'client' => $client,
            'timeline' => $timeline,
            'activityLogs' => $activityLogs,
            'portalHub' => $portalHub,
            'lostReasons' => $lostReasons,
            'relatedProjects' => $relatedProjects,
            'assignableReps' => $assignableReps,
            'pendingChange' => $this->approval->pendingForClient($client),
            'requiresApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
        ]);
    }

    public function edit(Client $client)
    {
        $this->authorizeClient($client);
        abort_unless($this->clients->canUpdate(Auth::user(), $client), 403);

        return view('crm.clients.edit', [
            'client' => $client,
            'requiresApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'marketingCampaigns' => $this->marketingCampaignOptions(),
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $user = Auth::user();
        $this->authorizeClient($client);
        abort_unless($this->clients->canUpdate($user, $client), 403);

        if ($this->approval->requiresMutationApproval($user)) {
            $this->approval->submitUpdate($request, $client, $user);

            return redirect()->route('crm.clients.approvals.index')
                ->with('success', 'تم إرسال طلب التعديل — بانتظار موافقة العمليات.');
        }

        $tracked = ['name', 'phone', 'email', 'company_name', 'address', 'status', 'lead_stage', 'client_type', 'lead_source', 'lead_source_details', 'marketing_campaign_id', 'assigned_to', 'notes', 'description', 'id_number'];
        $before = $client->only($tracked);
        $client->update($this->clients->prepareData($this->clients->validate($request, $client), $user, false));
        $after = $client->fresh()->only($tracked);

        app(ClientActivityService::class)->logUpdated($client, $user, $before, $after, $request);

        return $this->redirectAfterClientMutation($client, 'تم تحديث بيانات العميل');
    }

    public function updateLeadStage(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $request->validate(CrmLostReasonRules::stageRules('lead_stage', CrmScopeService::LEAD_STAGES));

        $from = $client->lead_stage ?? 'lead';
        $to = $request->lead_stage;

        $client->update(array_merge(
            ['lead_stage' => $to],
            CrmLostReasonRules::applyLostFields($request->only(['lost_reason', 'lost_reason_notes']), $to),
        ));

        if ($from !== $to) {
            app(ClientTimelineService::class)->recordStageChange(
                $client,
                $from,
                $to,
                Auth::user(),
                $request->lost_reason,
                $request->lost_reason_notes,
            );
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'lead_stage' => $client->lead_stage]);
        }

        return back()->with('success', 'تم تحديث مرحلة العميل');
    }

    public function storeStaffNote(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(\App\Models\ClientStaffNote::TYPES)),
            'body' => 'required|string|min:3|max:3000',
        ], [
            'body.required' => 'يرجى كتابة الملاحظة.',
            'body.min' => 'الملاحظة يجب أن تكون 3 أحرف على الأقل.',
        ]);

        $client->staffNotes()->create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'body' => $validated['body'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حفظ الملاحظة']);
        }

        return back()->with('success', 'تم حفظ الملاحظة');
    }

    public function logInteraction(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $validated = $request->validate([
            'interaction_type' => 'required|in:' . implode(',', \App\Models\CrmFollowUp::TYPES),
            'notes' => 'required|string|max:5000',
            'scheduled_at' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'sale_id' => 'nullable|exists:sales,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $validated['client_id'] = $client->id;
        $validated['scheduled_at'] = $validated['scheduled_at'] . ' ' . $validated['scheduled_time'];

        $followUp = \App\Services\CrmFollowUpService::for(Auth::user())->create($validated, Auth::user());

        app(ClientTimelineService::class)->recordInteraction($followUp, Auth::user());

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'crm_interaction',
            'model_type' => Client::class,
            'model_id' => $client->id,
            'description' => "تسجيل {$followUp->typeLabel()} للعميل: {$client->name}",
            'new_values' => [
                'interaction_type' => $validated['interaction_type'],
                'scheduled_at' => $followUp->scheduled_at->toIso8601String(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الموعد في جدول المتابعات',
                'follow_up_id' => $followUp->id,
            ]);
        }

        return redirect()
            ->route('crm.follow-ups.index', [
                'date' => $followUp->scheduled_at->toDateString(),
                'highlight' => $followUp->id,
            ])
            ->with('success', 'تم حفظ الموعد في جدول المتابعات');
    }

    public function destroy(Request $request, Client $client)
    {
        $user = Auth::user();
        $this->authorizeClient($client);
        abort_unless($this->clients->canDelete($user, $client), 403);

        $request->validate([
            'delete_reason' => 'required|string|min:10|max:1000',
        ], [
            'delete_reason.required' => 'يجب كتابة سبب الحذف.',
            'delete_reason.min' => 'سبب الحذف يجب أن يكون 10 أحرف على الأقل.',
        ]);

        if ($this->approval->requiresMutationApproval($user)) {
            $this->approval->submitDelete($client, $user, $request->input('delete_reason'));

            return redirect()->route('crm.clients.approvals.index')
                ->with('success', 'تم إرسال طلب الحذف — بانتظار موافقة العمليات.');
        }

        $reason = $request->input('delete_reason');
        $batch = app(ClientActivityService::class)->recordDeletionBatch(
            $user,
            $reason,
            [app(ClientActivityService::class)->clientSnapshot($client)],
            $request,
        );

        $this->clients->deleteClient($client, $user, $reason, $request, $batch);

        if ($user->can('viewAny', Client::class)) {
            return redirect()->route('crm.clients.index')->with('success', 'تم حذف العميل بنجاح');
        }

        return redirect()->route('crm.pipeline.index')->with('success', 'تم حذف العميل بنجاح');
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('bulkDelete', Client::class);

        $validated = $request->validate([
            'client_ids' => 'required|array|min:1|max:200',
            'client_ids.*' => 'integer|exists:clients,id',
            'delete_reason' => 'required|string|min:10|max:2000',
        ], [
            'delete_reason.required' => 'يجب كتابة سبب الحذف.',
            'delete_reason.min' => 'سبب الحذف يجب أن يكون 10 أحرف على الأقل.',
        ]);

        $user = Auth::user();
        $scope = CrmScopeService::for($user);
        $clients = $scope->clientsQuery()->whereIn('id', $validated['client_ids'])->get();

        if ($clients->isEmpty()) {
            return back()->with('error', 'لا توجد عملاء مطابقون ضمن صلاحياتك.');
        }

        $deleted = 0;
        $skipped = 0;
        $snapshots = [];

        foreach ($clients as $client) {
            if (! $user->can('delete', $client)) {
                $skipped++;
                continue;
            }
            if ($client->sales()->exists() || $client->projects()->exists()) {
                $skipped++;
                continue;
            }
            $snapshots[] = app(ClientActivityService::class)->clientSnapshot($client);
        }

        if ($snapshots === []) {
            return back()->with('error', 'لا يمكن حذف العملاء المحددين (صلاحيات أو ارتباطات).');
        }

        $batch = app(ClientActivityService::class)->recordDeletionBatch(
            $user,
            $validated['delete_reason'],
            $snapshots,
            $request,
        );

        foreach ($clients as $client) {
            if (! in_array($client->id, array_column($snapshots, 'id'), true)) {
                continue;
            }
            try {
                $this->clients->deleteClient($client, $user, $validated['delete_reason'], $request, $batch);
                $deleted++;
            } catch (\Throwable) {
                $skipped++;
            }
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'client_bulk_deleted',
            'model_type' => Client::class,
            'description' => "حذف جماعي: {$deleted} عميل",
            'new_values' => [
                'deletion_batch_id' => $batch->id,
                'clients_count' => $deleted,
                'skipped' => $skipped,
                'delete_reason' => $validated['delete_reason'],
                'client_ids' => array_column($snapshots, 'id'),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return back()->with('success', "تم حذف {$deleted} عميل — متخطى: {$skipped}.");
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
        $this->authorizeClient($client);
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

    public function deletionLogIndex(Request $request)
    {
        $this->authorize('viewDeletionLog', Client::class);

        $batches = ClientDeletionBatch::query()
            ->with('user:id,name')
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('crm.clients.deletions.index', [
            'batches' => $batches,
        ]);
    }

    public function deletionLogShow(ClientDeletionBatch $batch)
    {
        $this->authorize('viewDeletionLog', Client::class);

        $batch->load('user:id,name');

        return view('crm.clients.deletions.show', compact('batch'));
    }

    protected function authorizeClient(Client $client): void
    {
        $this->authorize('view', $client);
    }

    protected function marketingCampaignOptions()
    {
        return MarketingCampaign::query()
            ->orderByDesc('created_at')
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name']);
    }
}

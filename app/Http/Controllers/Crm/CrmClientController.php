<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Services\Crm\ClientImportService;
use App\Services\Crm\ClientTimelineService;
use App\Services\ClientApprovalService;
use App\Services\ClientManagementService;
use App\Services\CrmScopeService;
use App\Support\CrmLostReasonRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $stageLabels = [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];

        return view('crm.clients.index', [
            'clients' => $clients,
            'stats' => $stats,
            'requiresApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
            'clearUrl' => route('crm.clients.index'),
            ...$this->clientFilterViewData($filters, $request, $stageLabels, $statusLabels),
        ]);
    }

    public function create()
    {
        abort_unless($this->clients->canCreate(Auth::user()), 403);

        return view('crm.clients.create', [
            'requiresApproval' => false,
            'requiresMutationApproval' => $this->approval->requiresMutationApproval(Auth::user()),
        ]);
    }

    public function importTemplate(ClientImportService $import)
    {
        return $import->downloadTemplate();
    }

    public function import(Request $request, ClientImportService $import)
    {
        abort_unless($this->clients->canCreate(Auth::user()), 403);

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
            ->route('crm.clients.index')
            ->with($result['failed'] > 0 && $result['imported'] === 0 ? 'error' : 'success', $message)
            ->with('import_result', $result);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($this->clients->canCreate($user), 403);

        $data = $this->clients->prepareData($this->clients->validate($request), $user, true);
        $client = Client::create($data);

        app(ClientTimelineService::class)->recordLeadCreated($client, $user);

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

        $client->load(['sales.project', 'sales.salesRep', 'assignedEmployee.user', 'createdBy:id,name']);

        $timeline = app(ClientTimelineService::class)->buildForClient($client);
        $portalHub = app(\App\Services\ClientPortalHubService::class)->summaryForClient($client);
        $lostReasons = config('crm_intelligence.lost_reasons');
        $relatedProjects = $client->sales
            ->pluck('project')
            ->filter()
            ->unique('id')
            ->values();

        return view('crm.clients.show', [
            'client' => $client,
            'timeline' => $timeline,
            'portalHub' => $portalHub,
            'lostReasons' => $lostReasons,
            'relatedProjects' => $relatedProjects,
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

        $client->update($this->clients->prepareData($this->clients->validate($request), $user, false));

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

        if ($this->approval->requiresMutationApproval($user)) {
            $request->validate([
                'delete_reason' => 'required|string|min:10|max:1000',
            ], [
                'delete_reason.required' => 'يجب كتابة سبب الحذف.',
                'delete_reason.min' => 'سبب الحذف يجب أن يكون 10 أحرف على الأقل.',
            ]);

            $this->approval->submitDelete($client, $user, $request->input('delete_reason'));

            return redirect()->route('crm.clients.approvals.index')
                ->with('success', 'تم إرسال طلب الحذف — بانتظار موافقة العمليات.');
        }

        $this->clients->deleteClient($client);

        if ($user->can('viewAny', Client::class)) {
            return redirect()->route('crm.clients.index')->with('success', 'تم حذف العميل بنجاح');
        }

        return redirect()->route('crm.pipeline.index')->with('success', 'تم حذف العميل بنجاح');
    }

    protected function authorizeClient(Client $client): void
    {
        $this->authorize('view', $client);
    }
}

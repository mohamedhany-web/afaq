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

class CrmClientController extends Controller
{
    public function __construct(
        protected ClientManagementService $clients,
        protected ClientApprovalService $approval,
    ) {}

    public function index(Request $request)
    {
        $baseQuery = CrmScopeService::for(Auth::user())->clientsQuery();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'prospect' => (clone $baseQuery)->where('status', 'prospect')->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'with_deals' => (clone $baseQuery)->whereHas('sales')->count(),
        ];

        $clients = $baseQuery
            ->with(['assignedEmployee', 'createdBy:id,name', 'sales'])
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('company_name', 'like', '%' . $request->search . '%');
            }))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.clients.index', [
            'clients' => $clients,
            'stats' => $stats,
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]);
    }

    public function create()
    {
        abort_unless($this->clients->canCreate(Auth::user()), 403);

        return view('crm.clients.create', [
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
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

        if ($this->approval->requiresApproval(Auth::user())) {
            return redirect()
                ->route('crm.clients.create', ['tab' => 'import'])
                ->with('error', 'استيراد العملاء يتطلب صلاحية الإدارة — استخدم الإدخال اليدوي لإرسال طلبات فردية.');
        }

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

        if ($this->approval->requiresApproval($user)) {
            $this->approval->submitCreate($request, $user);

            return redirect()->route('crm.clients.approvals.index')
                ->with('success', 'تم إرسال طلب إضافة العميل — بانتظار موافقة الإدارة.');
        }

        $data = $this->clients->prepareData($this->clients->validate($request), $user, true);
        $client = Client::create($data);

        app(ClientTimelineService::class)->recordLeadCreated($client, $user);

        return redirect()->route('crm.clients.index')->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Client $client)
    {
        $this->authorizeClient($client);
        $client->load(['sales.project', 'sales.salesRep', 'assignedEmployee', 'createdBy:id,name']);

        $timeline = app(ClientTimelineService::class)->buildForClient($client);
        $lostReasons = config('crm_intelligence.lost_reasons');

        return view('crm.clients.show', [
            'client' => $client,
            'timeline' => $timeline,
            'lostReasons' => $lostReasons,
            'pendingChange' => $this->approval->pendingForClient($client),
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]);
    }

    public function edit(Client $client)
    {
        $this->authorizeClient($client);
        abort_unless($this->clients->canUpdate(Auth::user(), $client), 403);

        return view('crm.clients.edit', [
            'client' => $client,
            'requiresApproval' => $this->approval->requiresApproval(Auth::user()),
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $user = Auth::user();
        $this->authorizeClient($client);
        abort_unless($this->clients->canUpdate($user, $client), 403);

        if ($this->approval->requiresApproval($user)) {
            $this->approval->submitUpdate($request, $client, $user);

            return redirect()->route('crm.clients.approvals.index')
                ->with('success', 'تم إرسال طلب التعديل — بانتظار موافقة الإدارة.');
        }

        $client->update($this->clients->prepareData($this->clients->validate($request), $user, false));

        return redirect()->route('crm.clients.show', $client)->with('success', 'تم تحديث بيانات العميل');
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

        if ($this->approval->requiresApproval($user)) {
            $request->validate([
                'delete_reason' => 'required|string|min:10|max:1000',
            ], [
                'delete_reason.required' => 'يجب كتابة سبب الحذف.',
                'delete_reason.min' => 'سبب الحذف يجب أن يكون 10 أحرف على الأقل.',
            ]);

            $this->approval->submitDelete($client, $user, $request->input('delete_reason'));

            return redirect()->route('crm.clients.approvals.index')
                ->with('success', 'تم إرسال طلب الحذف — بانتظار موافقة الإدارة.');
        }

        $this->clients->deleteClient($client);

        return redirect()->route('crm.clients.index')->with('success', 'تم حذف العميل بنجاح');
    }

    protected function authorizeClient(Client $client): void
    {
        $scope = CrmScopeService::for(Auth::user());
        if (!$scope->clientsQuery()->where('id', $client->id)->exists()) {
            abort(403, 'لا يمكنك الوصول إلى هذا العميل.');
        }
    }
}

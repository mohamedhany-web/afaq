<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Employee;
use App\Services\Crm\ClientImportService;
use App\Services\Crm\ClientTimelineService;
use App\Services\CrmScopeService;
use App\Support\CrmLostReasonRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CrmClientController extends Controller
{
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

        return view('crm.clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('crm.clients.create');
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
            ->route('crm.clients.index')
            ->with($result['failed'] > 0 && $result['imported'] === 0 ? 'error' : 'success', $message)
            ->with('import_result', $result);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'client_type' => 'nullable|in:individual,company',
            'status' => 'required|in:active,inactive,suspended,prospect',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $client = Client::create(array_merge(
            $this->prepareClientData($validator->validated(), true),
            ['lead_stage' => 'lead']
        ));

        app(ClientTimelineService::class)->recordLeadCreated($client, Auth::user());

        return redirect()->route('crm.clients.index')->with('success', 'تم إضافة العميل بنجاح');
    }

    public function show(Client $client)
    {
        $this->authorizeClient($client);
        $client->load(['sales.project', 'sales.salesRep', 'assignedEmployee', 'createdBy:id,name']);

        $timeline = app(ClientTimelineService::class)->buildForClient($client);
        $lostReasons = config('crm_intelligence.lost_reasons');

        return view('crm.clients.show', compact('client', 'timeline', 'lostReasons'));
    }

    public function edit(Client $client)
    {
        $this->authorizeClient($client);
        return view('crm.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'client_type' => 'nullable|in:individual,company',
            'status' => 'required|in:active,inactive,suspended,prospect',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $client->update($this->prepareClientData($validator->validated()));

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

    public function destroy(Client $client)
    {
        $this->authorizeClient($client);

        if ($client->projects()->count() > 0 || $client->sales()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف العميل لأنه مرتبط بصفقات أو مشاريع');
        }

        $client->delete();

        return redirect()->route('crm.clients.index')->with('success', 'تم حذف العميل بنجاح');
    }

    protected function prepareClientData(array $data, bool $isCreate = false): array
    {
        if (isset($data['company'])) {
            $data['company_name'] = $data['company'];
            unset($data['company']);
        }

        $data['client_type'] = match ($data['client_type'] ?? 'individual') {
            'company' => 'small_business',
            default => 'individual',
        };

        if ($isCreate) {
            $scope = CrmScopeService::for(Auth::user());
            $requested = isset($data['assigned_to']) ? (int) $data['assigned_to'] : null;
            $allowed = $scope->assignableEmployeeIds();

            if ($requested && in_array($requested, $allowed, true)) {
                $data['assigned_to'] = $requested;
            } else {
                $data['assigned_to'] = Auth::user()->employee?->id;
            }

            $data['created_by'] = Auth::id();
        }

        return $data;
    }

    protected function authorizeClient(Client $client): void
    {
        $scope = CrmScopeService::for(Auth::user());
        if (!$scope->clientsQuery()->where('id', $client->id)->exists()) {
            abort(403, 'لا يمكنك الوصول إلى هذا العميل.');
        }
    }
}

<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Crm\ClientTimelineService;
use App\Services\MarketingScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->can('create-clients') && !Auth::user()?->can('view-marketing')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $scope = MarketingScopeService::for(Auth::user());

        $leads = $scope->leadsQuery()
            ->with(['marketingCampaign:id,name', 'createdBy:id,name', 'assignedEmployee:id,first_name,last_name'])
            ->when($request->search, function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($sub) use ($s) {
                    $sub->where('name', 'like', $s)
                        ->orWhere('phone', 'like', $s)
                        ->orWhere('email', 'like', $s);
                });
            })
            ->when($request->campaign_id, fn ($q) => $q->where('marketing_campaign_id', $request->campaign_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => (clone $scope->leadsQuery())->count(),
            'today' => (clone $scope->leadsQuery())->whereDate('created_at', today())->count(),
            'month' => (clone $scope->leadsQuery())->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $campaigns = $scope->campaignsQuery()->orderBy('name')->get(['id', 'name']);

        return view('marketing.leads.index', compact('leads', 'stats', 'campaigns'));
    }

    public function create(Request $request)
    {
        $scope = MarketingScopeService::for(Auth::user());

        return view('marketing.leads.create', [
            'campaigns' => $scope->campaignsQuery()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'leadSources' => config('marketing.lead_sources'),
            'prefillCampaign' => $request->get('campaign_id'),
        ]);
    }

    public function store(Request $request, ClientTimelineService $timeline)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'lead_source' => 'nullable|in:' . implode(',', array_keys(config('marketing.lead_sources'))),
            'marketing_campaign_id' => 'nullable|exists:marketing_campaigns,id',
        ]);

        if ($duplicate = Client::findByNormalizedPhone($data['phone'])) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'رقم الهاتف مسجّل مسبقاً للعميل: ' . $duplicate->name]);
        }

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $digits = preg_replace('/\D/', '', $data['phone']);
            $name = 'عميل ' . substr($digits, -4);
        }

        $client = Client::create([
            'name' => $name,
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'company_name' => $data['company'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'prospect',
            'lead_stage' => \App\Services\CrmScopeService::LEAD_STAGE_NEW,
            'lead_source' => $data['lead_source'] ?? 'personal',
            'marketing_campaign_id' => $data['marketing_campaign_id'] ?? null,
            'created_by' => Auth::id(),
            'client_type' => 'individual',
        ]);

        $timeline->recordLeadCreated($client, Auth::user());

        return redirect()->route('marketing.leads.index')->with('success', 'تم إضافة العميل المحتمل بنجاح.');
    }
}

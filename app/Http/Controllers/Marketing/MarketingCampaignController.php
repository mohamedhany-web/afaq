<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use App\Models\Project;
use App\Services\MarketingScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingCampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->can('view-marketing')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $scope = MarketingScopeService::for(Auth::user());
        $query = $scope->campaignsQuery()->with(['manager:id,name', 'project:id,name'])->withCount('leads');

        $campaigns = $query
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->channel, fn ($q) => $q->where('channel', $request->channel))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (clone $scope->campaignsQuery())->count(),
            'active' => (clone $scope->campaignsQuery())->where('status', 'active')->count(),
            'leads' => (clone $scope->leadsQuery())->count(),
            'budget' => (float) (clone $scope->campaignsQuery())->sum('budget'),
        ];

        return view('marketing.campaigns.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        $this->authorizeCreate();

        return view('marketing.campaigns.create', $this->formData());
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        $data = $this->validated($request);
        $data['created_by'] = Auth::id();

        if (empty($data['manager_id']) && Auth::user()->isMarketingManager()) {
            $data['manager_id'] = Auth::id();
        }

        MarketingCampaign::create($data);

        return redirect()->route('marketing.campaigns.index')->with('success', 'تم إنشاء الحملة بنجاح.');
    }

    public function show(MarketingCampaign $campaign)
    {
        $this->authorizeCampaign($campaign);
        $campaign->load(['manager:id,name', 'project:id,name', 'creator:id,name']);
        $campaign->loadCount(['leads', 'activities']);
        $activities = $campaign->activities()->with('assignee:id,name')->latest()->take(10)->get();

        return view('marketing.campaigns.show', compact('campaign', 'activities'));
    }

    public function edit(MarketingCampaign $campaign)
    {
        $this->authorizeCampaign($campaign);
        $this->authorizeEdit();

        return view('marketing.campaigns.edit', array_merge(['campaign' => $campaign], $this->formData()));
    }

    public function update(Request $request, MarketingCampaign $campaign)
    {
        $this->authorizeCampaign($campaign);
        $this->authorizeEdit();
        $campaign->update($this->validated($request));

        return redirect()->route('marketing.campaigns.show', $campaign)->with('success', 'تم تحديث الحملة.');
    }

    public function destroy(MarketingCampaign $campaign)
    {
        $this->authorizeCampaign($campaign);

        if (!Auth::user()->can('delete-marketing')) {
            abort(403);
        }

        if ($campaign->leads()->exists()) {
            return back()->with('error', 'لا يمكن حذف حملة مرتبطة بعملاء.');
        }

        $campaign->delete();

        return redirect()->route('marketing.campaigns.index')->with('success', 'تم حذف الحملة.');
    }

    protected function formData(): array
    {
        $scope = MarketingScopeService::for(Auth::user());

        return [
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'managers' => collect($scope->assignableUsers()),
            'channels' => config('marketing.channels'),
            'statuses' => config('marketing.campaign_statuses'),
        ];
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'channel' => 'required|in:' . implode(',', array_keys(config('marketing.channels'))),
            'status' => 'required|in:' . implode(',', array_keys(config('marketing.campaign_statuses'))),
            'budget' => 'nullable|numeric|min:0',
            'spent_amount' => 'nullable|numeric|min:0',
            'target_leads' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id',
            'manager_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
    }

    protected function authorizeCreate(): void
    {
        if (!Auth::user()->can('create-marketing')) {
            abort(403);
        }
    }

    protected function authorizeEdit(): void
    {
        if (!Auth::user()->can('edit-marketing')) {
            abort(403);
        }
    }

    protected function authorizeCampaign(MarketingCampaign $campaign): void
    {
        $exists = MarketingScopeService::for(Auth::user())
            ->campaignsQuery()
            ->where('id', $campaign->id)
            ->exists();

        if (!$exists) {
            abort(404);
        }
    }
}

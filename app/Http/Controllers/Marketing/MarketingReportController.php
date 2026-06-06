<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\MarketingScopeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketingReportController extends Controller
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

    public function index()
    {
        $scope = MarketingScopeService::for(Auth::user());
        $campaigns = $scope->campaignsQuery();
        $leads = $scope->leadsQuery();

        $byChannel = (clone $campaigns)
            ->select('channel', DB::raw('COUNT(*) as total'), DB::raw('SUM(budget) as budget_sum'))
            ->groupBy('channel')
            ->get()
            ->map(fn ($r) => [
                'channel' => config('marketing.channels.' . $r->channel, $r->channel),
                'campaigns' => $r->total,
                'budget' => (float) $r->budget_sum,
            ]);

        $byStatus = (clone $campaigns)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->mapWithKeys(fn ($count, $status) => [
                config('marketing.campaign_statuses.' . $status, $status) => $count,
            ]);

        $leadsMonthly = (clone $leads)
            ->selectRaw('YEAR(created_at) as y, MONTH(created_at) as m, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('y', 'm')
            ->orderBy('y')
            ->orderBy('m')
            ->get();

        $topCampaigns = (clone $campaigns)
            ->withCount('leads')
            ->orderByDesc('leads_count')
            ->take(8)
            ->get();

        $summary = [
            'campaigns' => (clone $campaigns)->count(),
            'active' => (clone $campaigns)->where('status', 'active')->count(),
            'leads' => (clone $leads)->count(),
            'budget' => (float) (clone $campaigns)->sum('budget'),
            'spent' => (float) (clone $campaigns)->sum('spent_amount'),
            'conversion_hint' => (clone $leads)->where('status', 'active')->count(),
        ];

        return view('marketing.analytics.index', compact('byChannel', 'byStatus', 'leadsMonthly', 'topCampaigns', 'summary'));
    }
}

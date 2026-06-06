<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ClientPostSalesCase;
use App\Services\Crm\LeadFunnelAnalyticsService;
use App\Services\Crm\SalesForecastingService;
use App\Services\Crm\SalesManagementIntelligenceService;
use App\Services\CrmScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmIntelligenceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $scope = CrmScopeService::for($user);

        if (!$scope->hasFullAccess() && !$scope->isManagerScope()) {
            abort(403, 'هذه اللوحة متاحة للإدارة ومديري المبيعات فقط.');
        }

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->subMonths(3)->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $postSalesQuery = ClientPostSalesCase::query()
            ->with(['client:id,name', 'assignee:id,name']);

        if (!$scope->hasFullAccess()) {
            $clientIds = $scope->clientsQuery()->pluck('id');
            $postSalesQuery->whereIn('client_id', $clientIds);
        }

        return view('crm.intelligence.index', [
            'funnel' => LeadFunnelAnalyticsService::build($user, $from, $to),
            'management' => SalesManagementIntelligenceService::build($user),
            'forecast' => SalesForecastingService::build($user),
            'postSales' => [
                'open' => (clone $postSalesQuery)->whereIn('status', ['open', 'in_progress'])->count(),
                'resolved_month' => (clone $postSalesQuery)
                    ->where('status', 'resolved')
                    ->where('resolved_at', '>=', Carbon::now()->startOfMonth())
                    ->count(),
                'recent' => (clone $postSalesQuery)->latest()->limit(8)->get(),
            ],
            'lostReasons' => config('crm_intelligence.lost_reasons'),
            'filters' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
        ]);
    }
}

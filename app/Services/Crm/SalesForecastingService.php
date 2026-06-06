<?php

namespace App\Services\Crm;

use App\Models\Sale;
use App\Services\CrmScopeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesForecastingService
{
    public function __construct(protected CrmScopeService $scope) {}

    public static function build(\App\Models\User $user): array
    {
        return (new self(CrmScopeService::for($user)))->forecast();
    }

    protected function salesQuery()
    {
        return $this->scope->salesQuery();
    }

    protected function forecast(): array
    {
        $sales = $this->salesQuery();
        $trend = [];

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $trend[] = [
                'label' => $start->locale('ar')->translatedFormat('M Y'),
                'revenue' => (float) (clone $sales)->where('stage', 'closed_won')
                    ->whereBetween('actual_close_date', [$start, $end])
                    ->sum(DB::raw('COALESCE(actual_value, estimated_value)')),
                'deals' => (int) (clone $sales)->where('stage', 'closed_won')
                    ->whereBetween('actual_close_date', [$start, $end])
                    ->count(),
            ];
        }

        $recentAvg = collect($trend)->take(-3)->avg('revenue') ?: 0;
        $recentDeals = collect($trend)->take(-3)->avg('deals') ?: 0;

        $forecastMonths = [];
        for ($m = 1; $m <= 3; $m++) {
            $month = Carbon::now()->addMonths($m);
            $factor = 1 + (0.03 * $m);
            $forecastMonths[] = [
                'label' => $month->locale('ar')->translatedFormat('F Y'),
                'revenue_forecast' => round($recentAvg * $factor),
                'deals_forecast' => (int) round($recentDeals * $factor),
            ];
        }

        $pipeline = (float) (clone $sales)
            ->whereNotIn('stage', ['closed_lost', 'closed_won'])
            ->sum('estimated_value');

        $weighted = (float) (clone $sales)
            ->whereNotIn('stage', ['closed_lost', 'closed_won'])
            ->sum(DB::raw('COALESCE(estimated_value, 0) * COALESCE(probability_percentage, 50) / 100'));

        $atRisk = (clone $sales)
            ->with(['client:id,name', 'project:id,name'])
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->where(function ($q) {
                $q->where('updated_at', '<', now()->subDays(14))
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('expected_close_date')
                            ->where('expected_close_date', '<', now());
                    });
            })
            ->orderBy('estimated_value', 'desc')
            ->limit(10)
            ->get()
            ->map(fn (Sale $s) => [
                'id' => $s->id,
                'client' => $s->client?->name,
                'project' => $s->project?->name,
                'stage' => $s->stage,
                'value' => (float) $s->estimated_value,
                'days_stale' => $s->updated_at->diffInDays(now()),
                'reason' => $s->expected_close_date && $s->expected_close_date->lt(now())
                    ? 'تجاوز تاريخ الإغلاق المتوقع'
                    : 'بدون تحديث منذ ' . $s->updated_at->diffInDays(now()) . ' يوم',
            ]);

        $upcomingCollections = (clone $sales)
            ->where('stage', 'closed_won')
            ->whereNotNull('expected_close_date')
            ->whereBetween('expected_close_date', [now(), now()->addDays(30)])
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));

        return [
            'trend' => $trend,
            'forecast' => $forecastMonths,
            'pipeline_value' => $pipeline,
            'weighted_forecast' => $weighted,
            'at_risk_deals' => $atRisk,
            'upcoming_collections' => (float) $upcomingCollections,
        ];
    }
}

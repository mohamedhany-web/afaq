<?php

namespace App\Services\Crm;

use App\Models\Client;
use App\Models\Sale;
use App\Services\CrmScopeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadFunnelAnalyticsService
{
    public function __construct(protected CrmScopeService $scope) {}

    public static function build(\App\Models\User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $scope = CrmScopeService::for($user);
        $service = new self($scope);

        $from = $from ?? Carbon::now()->subMonths(3)->startOfMonth();
        $to = $to ?? Carbon::now()->endOfDay();

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'client_funnel' => $service->clientStageFunnel(),
            'deal_funnel' => $service->dealStageFunnel(),
            'lost_breakdown' => $service->lostReasonBreakdown($from, $to),
            'conversion' => $service->conversionRates($from, $to),
            'stage_velocity' => $service->averageDaysInStage(),
        ];
    }

    protected function clientsQuery()
    {
        return $this->scope->clientsQuery();
    }

    protected function salesQuery()
    {
        return $this->scope->salesQuery();
    }

    protected function clientStageFunnel(): array
    {
        $counts = $this->clientsQuery()
            ->select('lead_stage', DB::raw('count(*) as total'))
            ->groupBy('lead_stage')
            ->pluck('total', 'lead_stage');

        $stages = CrmScopeService::LEAD_STAGES;
        $labels = [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];

        $max = max(1, (int) $counts->max());

        return collect($stages)->map(fn ($s) => [
            'stage' => $s,
            'label' => $labels[$s] ?? $s,
            'count' => (int) ($counts[$s] ?? 0),
            'percent' => round(((int) ($counts[$s] ?? 0) / $max) * 100, 1),
        ])->all();
    }

    protected function dealStageFunnel(): array
    {
        $counts = $this->salesQuery()
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $stages = ['lead', 'prospect', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];
        $labels = [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];

        $max = max(1, (int) $counts->max());

        return collect($stages)->map(fn ($s) => [
            'stage' => $s,
            'label' => $labels[$s] ?? $s,
            'count' => (int) ($counts[$s] ?? 0),
            'percent' => round(((int) ($counts[$s] ?? 0) / $max) * 100, 1),
        ])->all();
    }

    protected function lostReasonBreakdown(Carbon $from, Carbon $to): array
    {
        $reasons = config('crm_intelligence.lost_reasons');

        $clientLost = $this->clientsQuery()
            ->where('lead_stage', 'closed_lost')
            ->whereNotNull('lost_reason')
            ->whereBetween('lost_at', [$from, $to])
            ->select('lost_reason', DB::raw('count(*) as total'))
            ->groupBy('lost_reason')
            ->pluck('total', 'lost_reason');

        $saleLost = $this->salesQuery()
            ->where('stage', 'closed_lost')
            ->whereNotNull('lost_reason')
            ->whereBetween('lost_at', [$from, $to])
            ->select('lost_reason', DB::raw('count(*) as total'))
            ->groupBy('lost_reason')
            ->pluck('total', 'lost_reason');

        $merged = [];
        foreach (array_keys($reasons) as $key) {
            $merged[$key] = (int) ($clientLost[$key] ?? 0) + (int) ($saleLost[$key] ?? 0);
        }

        $unknown = (int) $this->clientsQuery()
            ->where('lead_stage', 'closed_lost')
            ->whereNull('lost_reason')
            ->whereBetween('updated_at', [$from, $to])
            ->count()
            + (int) $this->salesQuery()
                ->where('stage', 'closed_lost')
                ->whereNull('lost_reason')
                ->whereBetween('updated_at', [$from, $to])
                ->count();

        $total = array_sum($merged) + $unknown;
        $max = max(1, max($merged ?: [0]));

        $rows = collect($merged)
            ->filter(fn ($c) => $c > 0)
            ->map(fn ($count, $key) => [
                'key' => $key,
                'label' => $reasons[$key] ?? $key,
                'count' => $count,
                'share' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                'percent' => round(($count / $max) * 100, 1),
            ])
            ->sortByDesc('count')
            ->values()
            ->all();

        if ($unknown > 0) {
            $rows[] = [
                'key' => 'unknown',
                'label' => 'بدون سبب مسجّل',
                'count' => $unknown,
                'share' => $total > 0 ? round(($unknown / $total) * 100, 1) : 0,
                'percent' => round(($unknown / $max) * 100, 1),
            ];
        }

        return [
            'total_lost' => $total,
            'reasons' => $rows,
        ];
    }

    protected function conversionRates(Carbon $from, Carbon $to): array
    {
        $totalLeads = (int) $this->clientsQuery()
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $wonClients = (int) $this->clientsQuery()
            ->where('lead_stage', 'closed_won')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $lostClients = (int) $this->clientsQuery()
            ->where('lead_stage', 'closed_lost')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $totalDeals = (int) $this->salesQuery()
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $wonDeals = (int) $this->salesQuery()
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$from, $to])
            ->count();

        $lostDeals = (int) $this->salesQuery()
            ->where('stage', 'closed_lost')
            ->whereBetween('lost_at', [$from, $to])
            ->count();

        $closed = $wonDeals + $lostDeals;

        return [
            'lead_to_won' => $totalLeads > 0 ? round(($wonClients / $totalLeads) * 100, 1) : 0,
            'deal_close_rate' => $closed > 0 ? round(($wonDeals / $closed) * 100, 1) : 0,
            'total_leads' => $totalLeads,
            'won_clients' => $wonClients,
            'lost_clients' => $lostClients,
            'total_deals' => $totalDeals,
            'won_deals' => $wonDeals,
            'lost_deals' => $lostDeals,
        ];
    }

    protected function averageDaysInStage(): array
    {
        $sales = $this->salesQuery()
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->get(['id', 'stage', 'updated_at', 'created_at']);

        if ($sales->isEmpty()) {
            return ['avg_days_in_pipeline' => 0, 'stale_deals' => 0];
        }

        $days = $sales->map(fn (Sale $s) => $s->created_at->diffInDays(now()));
        $stale = $sales->filter(fn (Sale $s) => $s->updated_at->lt(now()->subDays(7)))->count();

        return [
            'avg_days_in_pipeline' => round($days->avg(), 1),
            'stale_deals' => $stale,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesRepDashboardService
{
    public const PIPELINE_LABELS = [
        'prospect' => 'تم التواصل',
        'qualified' => 'مؤهل',
        'property_visit' => 'معاينة عقار',
        'negotiation' => 'تفاوض',
        'proposal_sent' => 'عرض مُرسَل',
        'closed_won' => 'تم الإغلاق',
        'closed_lost' => 'خسارة',
    ];

    public function __construct(
        protected CrmScopeService $scope,
        protected User $user,
    ) {}

    public static function build(User $user): array
    {
        $scope = CrmScopeService::for($user);
        $service = new self($scope, $user);

        return [
            'user' => $user,
            'role' => 'مندوب مبيعات',
            'kpis' => $service->personalKpis(),
            'tasks' => $service->myTasks(),
            'leads' => $service->myLeads(),
            'pipeline' => $service->salesPipeline(),
            'dailyActivity' => $service->dailyActivity(),
            'properties' => $service->myProperties(),
            'assistant' => $service->smartAssistant(),
            'progress' => $service->performanceProgress(),
            'charts' => $service->chartPayload(),
        ];
    }

    protected function sales(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->scope->salesQuery()->where('assigned_to', $this->user->id);
    }

    protected function clients(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->scope->clientsQuery();
    }

    protected function personalKpis(): array
    {
        $clients = $this->clients();
        $sales = $this->sales();
        $monthStart = Carbon::now()->startOfMonth();
        $today = today();

        $assigned = (clone $clients)->count();
        $newToday = (clone $clients)->whereDate('created_at', $today)->count();
        $followUpsDue = (clone $sales)
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->where(function ($q) use ($today) {
                $q->whereDate('expected_close_date', $today)
                    ->orWhereDate('viewing_date', $today)
                    ->orWhere(function ($sub) {
                        $sub->where('updated_at', '<', now()->startOfDay())
                            ->where('updated_at', '>=', now()->subDays(7));
                    });
            })
            ->count();
        $activeOpps = (clone $sales)->whereIn('stage', ['lead', 'prospect', 'proposal', 'negotiation'])->count();
        $closedMonth = (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)->count();
        $revenue = (float) (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $won = (clone $sales)->where('stage', 'closed_won')->count();
        $conversion = $assigned > 0 ? round(($won / $assigned) * 100, 1) : 0;

        $lastMonth = (float) (clone $sales)->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $target = max($lastMonth * 1.1, 1);

        return [
            'assigned_leads' => $assigned,
            'new_leads_today' => $newToday,
            'follow_ups_due_today' => $followUpsDue,
            'active_opportunities' => $activeOpps,
            'closed_deals_month' => $closedMonth,
            'personal_revenue' => $revenue,
            'conversion_rate' => $conversion,
            'target_achievement' => min(100, round(($revenue / $target) * 100, 1)),
            'monthly_target' => $target,
        ];
    }

    protected function myTasks(): array
    {
        $sales = $this->sales();
        $today = today();

        $todayTasks = (clone $sales)
            ->with('client:id,name,phone')
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->where(function ($q) use ($today) {
                $q->whereDate('viewing_date', $today)
                    ->orWhereDate('expected_close_date', $today)
                    ->orWhereDate('updated_at', $today);
            })
            ->orderBy('viewing_date')
            ->limit(8)
            ->get()
            ->map(fn (Sale $s) => $this->taskRow($s, 'مهمة اليوم'));

        $meetings = (clone $sales)
            ->with('client:id,name')
            ->whereNotNull('viewing_date')
            ->whereDate('viewing_date', '>', $today)
            ->whereDate('viewing_date', '<=', $today->copy()->addDays(14))
            ->orderBy('viewing_date')
            ->limit(6)
            ->get()
            ->map(fn (Sale $s) => $this->taskRow($s, 'اجتماع / معاينة'));

        $visits = (clone $sales)
            ->with('client:id,name,phone')
            ->whereNotNull('viewing_date')
            ->where('stage', 'proposal')
            ->whereDate('viewing_date', '>=', $today)
            ->orderBy('viewing_date')
            ->limit(6)
            ->get()
            ->map(fn (Sale $s) => $this->taskRow($s, 'معاينة عقار'));

        $pendingFollowUps = (clone $sales)
            ->with('client:id,name,phone')
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->where('updated_at', '<', now()->subDays(2))
            ->orderBy('updated_at')
            ->limit(8)
            ->get()
            ->map(fn (Sale $s) => $this->taskRow($s, 'متابعة متأخرة'));

        $clientIds = $this->clients()->pluck('id');
        $contractTasks = Contract::with('client:id,name')
            ->whereIn('client_id', $clientIds)
            ->whereIn('status', ['draft', 'pending', 'active'])
            ->where(function ($q) {
                $q->whereDate('end_date', '<=', now()->addDays(30))
                    ->orWhere('status', 'pending');
            })
            ->orderBy('end_date')
            ->limit(5)
            ->get()
            ->map(fn (Contract $c) => [
                'type' => 'عقد',
                'title' => $c->title ?? $c->contract_number,
                'client' => $c->client?->name,
                'date' => $c->end_date?->format('Y/m/d') ?? '—',
                'url' => route('crm.clients.show', $c->client_id),
            ]);

        return [
            'today' => $todayTasks,
            'meetings' => $meetings,
            'visits' => $visits,
            'follow_ups' => $pendingFollowUps,
            'contracts' => $contractTasks,
        ];
    }

    protected function taskRow(Sale $sale, string $type): array
    {
        return [
            'type' => $type,
            'title' => $sale->client?->name ?? $sale->product_service ?? '—',
            'meta' => self::PIPELINE_LABELS[$sale->stage] ?? $sale->stage,
            'date' => $sale->viewing_date?->format('Y/m/d')
                ?? $sale->expected_close_date?->format('Y/m/d')
                ?? $sale->updated_at->format('Y/m/d'),
            'url' => route('crm.pipeline.show', $sale),
        ];
    }

    protected function myLeads(): array
    {
        $base = $this->clients()->with(['sales' => fn ($q) => $q->where('assigned_to', $this->user->id)]);
        $all = (clone $base)->get();

        $classify = function (Client $c): string {
            $sale = $c->sales->sortByDesc('updated_at')->first();
            if ($c->created_at->gte(now()->subDays(7))) {
                return 'new';
            }
            if ($sale?->stage === 'negotiation' || ($sale?->probability_percentage ?? 0) >= 65) {
                return 'hot';
            }
            if ($c->lead_stage === 'lead' && $c->updated_at->lt(now()->subDays(14))) {
                return 'cold';
            }

            return 'warm';
        };

        $grouped = ['new' => collect(), 'hot' => collect(), 'warm' => collect(), 'cold' => collect(), 'urgent' => collect()];

        foreach ($all as $client) {
            $bucket = $classify($client);
            $row = [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'stage' => $client->lead_stage,
                'url' => route('crm.clients.show', $client),
            ];
            $grouped[$bucket]->push($row);

            $needsAction = $client->updated_at->lt(now()->subDays(2))
                || $client->sales->contains(fn ($s) => $s->stage === 'negotiation' && $s->updated_at->lt(now()->subDays(3)));
            if ($needsAction) {
                $grouped['urgent']->push($row);
            }
        }

        return [
            'new' => $grouped['new']->take(6)->values(),
            'hot' => $grouped['hot']->take(6)->values(),
            'warm' => $grouped['warm']->take(6)->values(),
            'cold' => $grouped['cold']->take(6)->values(),
            'urgent' => $grouped['urgent']->unique('id')->take(8)->values(),
            'counts' => [
                'new' => $grouped['new']->count(),
                'hot' => $grouped['hot']->count(),
                'warm' => $grouped['warm']->count(),
                'cold' => $grouped['cold']->count(),
                'urgent' => $grouped['urgent']->unique('id')->count(),
            ],
        ];
    }

    protected function salesPipeline(): array
    {
        $sales = $this->sales();

        $prospect = (clone $sales)->where('stage', 'prospect')->count();
        $qualified = (clone $this->clients())->whereIn('lead_stage', ['prospect', 'proposal'])->count();
        $visits = (clone $sales)->where('stage', 'proposal')->whereNotNull('viewing_date')->count();
        $proposalSent = (clone $sales)->where('stage', 'proposal')->whereNull('viewing_date')->count();
        $negotiation = (clone $sales)->where('stage', 'negotiation')->count();
        $won = (clone $sales)->where('stage', 'closed_won')->count();
        $lost = (clone $sales)->where('stage', 'closed_lost')->count();

        $steps = [
            ['key' => 'prospect', 'count' => $prospect],
            ['key' => 'qualified', 'count' => $qualified],
            ['key' => 'property_visit', 'count' => $visits],
            ['key' => 'negotiation', 'count' => $negotiation],
            ['key' => 'proposal_sent', 'count' => $proposalSent],
            ['key' => 'closed_won', 'count' => $won],
            ['key' => 'closed_lost', 'count' => $lost],
        ];
        $max = max(1, collect($steps)->max('count'));

        return collect($steps)->map(fn ($s) => [
            'key' => $s['key'],
            'label' => self::PIPELINE_LABELS[$s['key']],
            'count' => $s['count'],
            'percent' => round(($s['count'] / $max) * 100, 1),
            'url' => route('crm.pipeline.index', ['stage' => $this->pipelineFilterStage($s['key'])]),
        ])->all();
    }

    protected function pipelineFilterStage(string $key): ?string
    {
        return match ($key) {
            'prospect', 'qualified' => 'prospect',
            'property_visit', 'proposal_sent' => 'proposal',
            'negotiation' => 'negotiation',
            'closed_won' => 'closed_won',
            'closed_lost' => 'closed_lost',
            default => null,
        };
    }

    protected function dailyActivity(): array
    {
        $sales = $this->sales();
        $today = today();

        $calls = (clone $sales)->where('lead_source', 'call')->whereDate('updated_at', $today)->count();
        $whatsapp = (clone $sales)->where('lead_source', 'whatsapp')->whereDate('updated_at', $today)->count();
        $emails = (clone $sales)->where('lead_source', 'email')->whereDate('updated_at', $today)->count();
        $meetings = (clone $sales)->whereNotNull('viewing_date')->whereDate('viewing_date', $today)->count();
        $tours = (clone $sales)->where('stage', 'proposal')->whereDate('viewing_date', $today)->count();

        $monthCalls = (clone $sales)->where('lead_source', 'call')->where('updated_at', '>=', now()->startOfMonth())->count();

        return [
            'calls' => $calls,
            'whatsapp' => $whatsapp,
            'emails' => $emails,
            'meetings' => $meetings,
            'tours' => $tours,
            'period' => 'today',
            'month_calls' => $monthCalls,
        ];
    }

    protected function myProperties(): array
    {
        $sales = $this->sales();
        $projectIds = (clone $sales)->whereNotNull('project_id')->pluck('project_id');

        $recommended = Project::query()
            ->whereIn('id', $projectIds)
            ->whereIn('listing_status', ['active', 'upcoming'])
            ->orderByDesc('available_units')
            ->limit(4)
            ->get(['id', 'name', 'city', 'location', 'price_from', 'available_units', 'listing_status']);

        $recentIds = (clone $sales)
            ->whereNotNull('viewing_date')
            ->where('viewing_date', '>=', now()->subDays(45))
            ->whereNotNull('project_id')
            ->orderByDesc('viewing_date')
            ->pluck('project_id')
            ->unique()
            ->take(4);

        $recentlyViewed = Project::whereIn('id', $recentIds)->get(['id', 'name', 'city', 'price_from', 'available_units']);

        $frequent = Project::query()
            ->whereIn('id', $projectIds)
            ->withCount(['sales' => fn ($q) => $q->where('assigned_to', $this->user->id)])
            ->orderByDesc('sales_count')
            ->limit(4)
            ->get(['id', 'name', 'city', 'price_from', 'available_units']);

        $available = Project::query()
            ->where('listing_status', 'active')
            ->where('available_units', '>', 0)
            ->orderByDesc('available_units')
            ->limit(6)
            ->get(['id', 'name', 'city', 'location', 'price_from', 'price_to', 'available_units']);

        return [
            'recommended' => $recommended,
            'recently_viewed' => $recentlyViewed,
            'frequent' => $frequent,
            'available' => $available,
        ];
    }

    protected function smartAssistant(): array
    {
        $kpis = $this->personalKpis();
        $leads = $this->myLeads();
        $tasks = $this->myTasks();

        $nextAction = 'راجع مسار المبيعات وحدّث أولوياتك';
        if ($leads['urgent']->isNotEmpty()) {
            $first = $leads['urgent']->first();
            $nextAction = 'اتصل الآن بـ ' . $first['name'] . ' — عميل يحتاج إجراء فوري';
        } elseif ($kpis['follow_ups_due_today'] > 0) {
            $nextAction = 'أكمل ' . $kpis['follow_ups_due_today'] . ' متابعات مستحقة اليوم';
        } elseif ($tasks['visits']->isNotEmpty()) {
            $nextAction = 'استعد لمعاينة: ' . ($tasks['visits']->first()['title'] ?? '');
        }

        $highPriority = $this->sales()
            ->with('client:id,name,phone')
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderByDesc('probability_percentage')
            ->orderByDesc('estimated_value')
            ->limit(5)
            ->get();

        $topDeal = $highPriority->first();
        $closingScore = $topDeal ? (int) ($topDeal->probability_percentage ?? 40) : 0;

        $followUpMessage = $topDeal
            ? 'مرحباً ' . ($topDeal->client?->name ?? '') . '، أود متابعة اهتمامكم بـ ' . ($topDeal->product_service ?? 'العرض العقاري') . '. هل يناسبكم موعد للمعاينة أو لمناقشة التفاصيل؟'
            : 'ابدأ يومك بمراجعة العملاء الجدد وتوزيع وقتك على المتابعات العاجلة.';

        return [
            'next_action' => $nextAction,
            'follow_up_message' => $followUpMessage,
            'high_priority' => $highPriority,
            'closing_score' => $closingScore,
        ];
    }

    protected function performanceProgress(): array
    {
        $kpis = $this->personalKpis();
        $activity = $this->dailyActivity();
        $sales = $this->sales();

        $revenueTrend = [];
        $conversionTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $rev = (float) (clone $sales)->where('stage', 'closed_won')
                ->whereBetween('actual_close_date', [$start, $end])
                ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
            $revenueTrend[] = ['label' => $start->locale('ar')->translatedFormat('M'), 'value' => $rev];

            $leadsInMonth = (clone $this->clients())->whereBetween('created_at', [$start, $end])->count();
            $wonInMonth = (clone $sales)->where('stage', 'closed_won')
                ->whereBetween('actual_close_date', [$start, $end])->count();
            $conversionTrend[] = [
                'label' => $start->locale('ar')->translatedFormat('M'),
                'value' => $leadsInMonth > 0 ? round(($wonInMonth / $leadsInMonth) * 100, 1) : 0,
            ];
        }

        $targets = [
            'calls' => 5,
            'whatsapp' => 3,
            'meetings' => 1,
            'follow_ups' => 3,
        ];
        $followDone = $kpis['follow_ups_due_today'] === 0 ? $targets['follow_ups'] : 0;
        $done = min($activity['calls'], $targets['calls'])
            + min($activity['whatsapp'], $targets['whatsapp'])
            + min($activity['meetings'], $targets['meetings'])
            + $followDone;
        $maxDone = array_sum($targets);
        $productivityScore = $maxDone > 0 ? min(100, round(($done / $maxDone) * 100)) : 0;

        return [
            'target_achievement' => $kpis['target_achievement'],
            'monthly_target' => $kpis['monthly_target'],
            'personal_revenue' => $kpis['personal_revenue'],
            'revenue_trend' => $revenueTrend,
            'conversion_trend' => $conversionTrend,
            'productivity_score' => $productivityScore,
        ];
    }

    protected function chartPayload(): array
    {
        $progress = $this->performanceProgress();

        return [
            'revenue_trend' => $progress['revenue_trend'],
            'conversion_trend' => $progress['conversion_trend'],
            'pipeline' => $this->salesPipeline(),
        ];
    }
}

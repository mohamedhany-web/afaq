<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesManagerDashboardService
{
    public const FUNNEL_LABELS = [
        'lead' => 'عملاء جدد',
        'prospect' => 'تم التواصل',
        'qualified' => 'عملاء مؤهلون',
        'proposal' => 'معاينات عقار',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم الإغلاق',
        'closed_lost' => 'خسارة',
    ];

    public function __construct(
        protected CrmScopeService $scope,
        protected User $user,
        protected Collection $memberIds,
        protected Collection $teams,
    ) {}

    public static function build(User $user): array
    {
        $scope = CrmScopeService::for($user);
        $teams = $scope->managedTeamsQuery()->with(['manager', 'members'])->get();
        $memberIds = collect($scope->managedTeamMemberUserIds())->filter(fn ($id) => $id !== $user->id);

        $service = new self($scope, $user, $memberIds, $teams);

        return [
            'user' => $user,
            'role' => self::roleLabel($user),
            'teams' => $teams,
            'kpis' => $service->executiveKpis(),
            'teamPerformance' => $service->teamPerformance(),
            'individualMetrics' => $service->individualMetrics(),
            'funnel' => $service->funnelAnalysis(),
            'leadDistribution' => $service->leadDistribution(),
            'forecasting' => $service->revenueForecasting(),
            'activityFeed' => $service->activityFeed(),
            'alerts' => $service->performanceAlerts(),
            'aiInsights' => $service->aiInsights(),
            'charts' => $service->chartPayload(),
        ];
    }

    protected function salesBase()
    {
        return $this->scope->salesQuery();
    }

    protected function clientsBase()
    {
        return $this->scope->clientsQuery();
    }

    protected function executiveKpis(): array
    {
        $sales = $this->salesBase();
        $clients = $this->clientsBase();
        $monthStart = Carbon::now()->startOfMonth();
        $activeStages = ['lead', 'prospect', 'proposal', 'negotiation'];

        $totalLeads = (clone $clients)->count();
        $qualified = (clone $clients)->whereIn('lead_stage', ['prospect', 'proposal'])->count();
        $activeOpps = (clone $sales)->whereIn('stage', $activeStages)->count();
        $closedMonth = (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)->count();
        $monthRevenue = (float) (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $teamRevenue = (float) (clone $sales)->where('stage', 'closed_won')
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $wonCount = (clone $sales)->where('stage', 'closed_won')->count();
        $conversion = $totalLeads > 0 ? round(($wonCount / $totalLeads) * 100, 1) : 0;
        $avgDeal = $closedMonth > 0 ? round($monthRevenue / $closedMonth, 0) : 0;

        $lastMonthRevenue = (float) (clone $sales)->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $target = max($lastMonthRevenue * 1.12, 1);

        return [
            'team_revenue' => $teamRevenue,
            'monthly_revenue' => $monthRevenue,
            'total_leads' => $totalLeads,
            'qualified_leads' => $qualified,
            'active_opportunities' => $activeOpps,
            'conversion_rate' => $conversion,
            'closed_deals_month' => $closedMonth,
            'avg_deal_value' => $avgDeal,
            'target_achievement' => min(100, round(($monthRevenue / $target) * 100, 1)),
            'team_target' => $target,
        ];
    }

    protected function teamPerformance(): array
    {
        $monthStart = Carbon::now()->startOfMonth();
        $teamsData = $this->teams->map(function (SalesTeam $team) use ($monthStart) {
            $teamSales = $this->salesBase()->where('sales_team_id', $team->id);
            $revenue = (float) (clone $teamSales)->where('stage', 'closed_won')
                ->where('actual_close_date', '>=', $monthStart)
                ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
            $closed = (clone $teamSales)->where('stage', 'closed_won')
                ->where('actual_close_date', '>=', $monthStart)->count();
            $leads = (clone $teamSales)->whereIn('stage', ['lead', 'prospect', 'proposal', 'negotiation'])->count();
            $won = (clone $teamSales)->where('stage', 'closed_won')->count();
            $conversion = $leads + $won > 0 ? round(($won / ($leads + $won)) * 100, 1) : 0;

            return [
                'id' => $team->id,
                'name' => $team->name,
                'manager' => $team->manager?->name,
                'revenue' => $revenue,
                'closed' => $closed,
                'conversion' => $conversion,
                'members' => $team->members->count(),
            ];
        })->sortByDesc('revenue')->values();

        $leaderboard = $this->repMetrics($monthStart)
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        return [
            'teams' => $teamsData,
            'leaderboard' => $leaderboard,
            'top_team' => $teamsData->first(),
        ];
    }

    protected function repMetrics(?Carbon $since = null): Collection
    {
        $since = $since ?? Carbon::now()->startOfMonth();
        $ids = $this->memberIds->isNotEmpty() ? $this->memberIds : collect([$this->user->id]);

        return User::whereIn('id', $ids)
            ->with('employee')
            ->get()
            ->map(function (User $rep) use ($since) {
                $scoped = $this->salesBase()->where('assigned_to', $rep->id);

                $revenue = (float) (clone $scoped)->where('stage', 'closed_won')
                    ->where('actual_close_date', '>=', $since)
                    ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
                $closed = (clone $scoped)->where('stage', 'closed_won')
                    ->where('actual_close_date', '>=', $since)->count();
                $created = (clone $scoped)->where('created_at', '>=', $since)->count();
                $calls = (clone $scoped)->where('lead_source', 'call')
                    ->where('updated_at', '>=', $since)->count();
                $meetings = (clone $scoped)->whereNotNull('viewing_date')
                    ->where('viewing_date', '>=', $since->toDateString())->count();
                $tours = (clone $scoped)->whereNotNull('viewing_date')
                    ->whereDate('viewing_date', '>=', $since)->count();
                $followUps = (clone $scoped)->where('updated_at', '>=', $since)
                    ->whereNotIn('stage', ['closed_won', 'closed_lost'])->count();

                return [
                    'id' => $rep->id,
                    'name' => $rep->name,
                    'title' => $rep->employee?->job_title ?? 'مندوب مبيعات',
                    'revenue' => $revenue,
                    'deals_closed' => $closed,
                    'opportunities_created' => $created,
                    'calls' => $calls,
                    'meetings' => $meetings,
                    'property_visits' => $tours,
                    'follow_ups' => $followUps,
                ];
            });
    }

    protected function individualMetrics(): array
    {
        $reps = $this->repMetrics(Carbon::now()->startOfMonth());
        $totals = [
            'calls' => $reps->sum('calls'),
            'meetings' => $reps->sum('meetings'),
            'property_visits' => $reps->sum('property_visits'),
            'follow_ups' => $reps->sum('follow_ups'),
            'opportunities_created' => $reps->sum('opportunities_created'),
            'deals_closed' => $reps->sum('deals_closed'),
            'revenue' => $reps->sum('revenue'),
        ];

        return [
            'reps' => $reps->sortByDesc('revenue')->values(),
            'totals' => $totals,
        ];
    }

    protected function funnelAnalysis(): array
    {
        $clientCounts = $this->clientsBase()
            ->select('lead_stage', DB::raw('count(*) as total'))
            ->groupBy('lead_stage')
            ->pluck('total', 'lead_stage');

        $saleCounts = $this->salesBase()
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $qualified = (clone $this->clientsBase())
            ->where(function ($q) {
                $q->whereIn('lead_stage', ['prospect', 'proposal'])
                    ->orWhere('status', 'prospect');
            })
            ->count();

        $steps = [
            ['key' => 'lead', 'count' => (int) ($clientCounts['lead'] ?? 0)],
            ['key' => 'prospect', 'count' => (int) ($clientCounts['prospect'] ?? 0)],
            ['key' => 'qualified', 'count' => $qualified],
            ['key' => 'proposal', 'count' => (int) (($clientCounts['proposal'] ?? 0) + ($saleCounts['proposal'] ?? 0))],
            ['key' => 'negotiation', 'count' => (int) ($saleCounts['negotiation'] ?? 0)],
            ['key' => 'closed_won', 'count' => (int) ($saleCounts['closed_won'] ?? 0)],
            ['key' => 'closed_lost', 'count' => (int) ($saleCounts['closed_lost'] ?? 0)],
        ];

        $max = max(1, collect($steps)->max('count'));

        return collect($steps)->map(fn ($s) => [
            'key' => $s['key'],
            'label' => self::FUNNEL_LABELS[$s['key']] ?? $s['key'],
            'count' => $s['count'],
            'percent' => round(($s['count'] / $max) * 100, 1),
        ])->all();
    }

    protected function leadDistribution(): array
    {
        $memberEmployeeIds = Employee::whereIn('user_id', $this->memberIds->push($this->user->id))->pluck('id');

        $unassigned = $this->clientsBase()
            ->whereNull('assigned_to')
            ->latest()
            ->limit(8)
            ->get(['id', 'name', 'phone', 'created_at']);

        $perRep = $this->clientsBase()
            ->whereIn('assigned_to', $memberEmployeeIds)
            ->select('assigned_to', DB::raw('count(*) as total'))
            ->groupBy('assigned_to')
            ->get()
            ->map(function ($row) {
                $emp = Employee::find($row->assigned_to);
                return [
                    'name' => $emp?->user?->name ?? 'غير معروف',
                    'count' => (int) $row->total,
                ];
            })
            ->sortByDesc('count')
            ->values();

        $assignedClients = (clone $this->clientsBase())->whereNotNull('assigned_to')->count();
        $totalClients = (clone $this->clientsBase())->count();

        $overdue = $this->salesBase()
            ->with(['client:id,name', 'salesRep:id,name'])
            ->where('updated_at', '<', now()->subDays(3))
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderBy('updated_at')
            ->limit(8)
            ->get();

        $responseHours = $this->averageLeadResponseHours();

        return [
            'unassigned' => $unassigned,
            'unassigned_count' => (clone $this->clientsBase())->whereNull('assigned_to')->count(),
            'per_rep' => $perRep,
            'assigned_pct' => $totalClients > 0 ? round(($assignedClients / $totalClients) * 100, 1) : 0,
            'response_hours' => $responseHours,
            'overdue_follow_ups' => $overdue,
            'overdue_count' => (clone $this->salesBase())
                ->where('updated_at', '<', now()->subDays(3))
                ->whereNotIn('stage', ['closed_won', 'closed_lost'])
                ->count(),
        ];
    }

    protected function averageLeadResponseHours(): float
    {
        $pairs = $this->salesBase()
            ->select('client_id', DB::raw('MIN(created_at) as first_sale'))
            ->groupBy('client_id')
            ->get();

        if ($pairs->isEmpty()) {
            return 0;
        }

        $clients = Client::whereIn('id', $pairs->pluck('client_id'))->get()->keyBy('id');
        $hours = 0;
        $n = 0;

        foreach ($pairs as $row) {
            $client = $clients->get($row->client_id);
            if (!$client || !$row->first_sale) {
                continue;
            }
            $hours += $client->created_at->diffInMinutes(Carbon::parse($row->first_sale)) / 60;
            $n++;
        }

        return $n > 0 ? round($hours / $n, 1) : 0;
    }

    protected function revenueForecasting(): array
    {
        $sales = $this->salesBase();
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $trend[] = [
                'label' => $start->locale('ar')->translatedFormat('M Y'),
                'value' => (float) (clone $sales)->where('stage', 'closed_won')
                    ->whereBetween('actual_close_date', [$start, $end])
                    ->sum(DB::raw('COALESCE(actual_value, estimated_value)')),
            ];
        }

        $recentAvg = collect($trend)->take(-3)->avg('value') ?: 0;
        $forecast = [
            ['label' => Carbon::now()->addMonth()->locale('ar')->translatedFormat('M'), 'value' => round($recentAvg * 1.05)],
            ['label' => Carbon::now()->addMonths(2)->locale('ar')->translatedFormat('M'), 'value' => round($recentAvg * 1.1)],
            ['label' => Carbon::now()->addMonths(3)->locale('ar')->translatedFormat('M'), 'value' => round($recentAvg * 1.08)],
        ];

        $pipeline = (float) (clone $sales)->whereNotIn('stage', ['closed_lost', 'closed_won'])->sum('estimated_value');
        $weighted = (float) (clone $sales)->whereNotIn('stage', ['closed_lost', 'closed_won'])
            ->sum(DB::raw('COALESCE(estimated_value, 0) * COALESCE(probability_percentage, 0) / 100'));

        return [
            'trend' => $trend,
            'forecast' => $forecast,
            'pipeline_value' => $pipeline,
            'weighted_forecast' => $weighted,
        ];
    }

    protected function activityFeed(): Collection
    {
        $memberIds = $this->memberIds->push($this->user->id)->unique();

        $logs = ActivityLog::with('user')
            ->whereIn('user_id', $memberIds)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($log) => [
                'type' => 'activity',
                'icon' => 'log',
                'title' => $log->description ?? $log->action,
                'meta' => $log->user?->name ?? '—',
                'time' => $log->created_at,
            ]);

        $sales = $this->salesBase()
            ->with(['client:id,name', 'salesRep:id,name'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(function (Sale $s) {
                $type = match ($s->stage) {
                    'closed_won' => 'won',
                    'closed_lost' => 'lost',
                    default => str_contains((string) $s->viewing_date, now()->toDateString()) ? 'tour' : 'deal',
                };
                return [
                    'type' => $type,
                    'icon' => $type,
                    'title' => match ($type) {
                        'won' => 'صفقة مغلقة: ' . ($s->client?->name ?? '—'),
                        'lost' => 'فرصة خاسرة: ' . ($s->client?->name ?? '—'),
                        'tour' => 'معاينة عقار: ' . ($s->client?->name ?? '—'),
                        default => 'تحديث صفقة: ' . ($s->client?->name ?? '—'),
                    },
                    'meta' => $s->salesRep?->name ?? '—',
                    'time' => $s->updated_at,
                ];
            });

        $newClients = $this->clientsBase()
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (Client $c) => [
                'type' => 'lead',
                'icon' => 'lead',
                'title' => 'عميل جديد: ' . $c->name,
                'meta' => 'تعيين',
                'time' => $c->created_at,
            ]);

        return collect($logs)->merge($sales)->merge($newClients)
            ->sortByDesc('time')
            ->take(12)
            ->values();
    }

    protected function performanceAlerts(): array
    {
        $reps = $this->repMetrics(Carbon::now()->startOfMonth());
        $avgRevenue = $reps->avg('revenue') ?: 0;

        $underperforming = $reps->filter(fn ($r) => $avgRevenue > 0 && $r['revenue'] < ($avgRevenue * 0.5))
            ->values();

        $stagnant = $this->salesBase()
            ->with(['client:id,name', 'salesRep:id,name'])
            ->where('updated_at', '<', now()->subDays(7))
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderBy('estimated_value', 'desc')
            ->limit(6)
            ->get();

        $atRisk = $this->salesBase()
            ->with(['client:id,name'])
            ->where('stage', 'negotiation')
            ->where(function ($q) {
                $q->where('probability_percentage', '<', 40)
                    ->orWhere('updated_at', '<', now()->subDays(5));
            })
            ->orderByDesc('estimated_value')
            ->limit(6)
            ->get();

        $missedCount = (clone $this->salesBase())
            ->where('updated_at', '<', now()->subDays(3))
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->count();

        return [
            'underperforming' => $underperforming,
            'stagnant' => $stagnant,
            'at_risk' => $atRisk,
            'missed_follow_ups' => $missedCount,
        ];
    }

    protected function aiInsights(): array
    {
        $sources = $this->salesBase()
            ->select('lead_source', DB::raw('count(*) as cnt'))
            ->whereNotNull('lead_source')
            ->groupBy('lead_source')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'source' => CrmDashboardAnalyticsService::LEAD_SOURCE_LABELS[$r->lead_source] ?? $r->lead_source,
                'count' => (int) $r->cnt,
            ]);

        $highProb = $this->salesBase()
            ->with(['client:id,name', 'salesRep:id,name'])
            ->where('probability_percentage', '>=', 70)
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderByDesc('probability_percentage')
            ->limit(5)
            ->get();

        $unassignedCount = (clone $this->clientsBase())->whereNull('assigned_to')->count();
        $missedFollowUps = (clone $this->salesBase())
            ->where('updated_at', '<', now()->subDays(3))
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->count();

        $actions = [];
        if ($unassignedCount > 0) {
            $actions[] = 'توزيع ' . $unassignedCount . ' عميل غير مُعيَّن على الفريق اليوم';
        }
        if ($missedFollowUps > 0) {
            $actions[] = 'متابعة ' . $missedFollowUps . ' صفقة متأخرة عن آخر نشاط';
        }
        if ($actions === []) {
            $actions[] = 'الفريق على المسار — ركّز على رفع معدل التحويل في مرحلة التفاوض';
        }

        $reps = $this->repMetrics(Carbon::now()->startOfMonth());
        $productive = $reps->where('follow_ups', '>', 0)->count();
        $productivityPct = $reps->count() > 0 ? round(($productive / $reps->count()) * 100) : 0;

        return [
            'best_sources' => $sources,
            'high_probability' => $highProb,
            'actions' => $actions,
            'productivity_pct' => $productivityPct,
            'productive_reps' => $productive,
            'total_reps' => $reps->count(),
        ];
    }

    protected function chartPayload(): array
    {
        $perf = $this->teamPerformance();
        $forecast = $this->revenueForecasting();

        return [
            'revenue_trend' => $forecast['trend'],
            'forecast' => $forecast['forecast'],
            'team_revenue' => $perf['teams']->map(fn ($t) => [
                'label' => $t['name'],
                'value' => $t['revenue'],
            ])->values()->all(),
            'team_closed' => $perf['teams']->map(fn ($t) => [
                'label' => $t['name'],
                'value' => $t['closed'],
            ])->values()->all(),
            'leads_per_rep' => $this->leadDistribution()['per_rep']->take(8)->values()->all(),
        ];
    }

    protected static function roleLabel(User $user): string
    {
        if ($user->hasRole('sales_manager')) {
            return 'مدير مبيعات';
        }
        if ($user->hasRole('manager')) {
            return 'مدير فريق';
        }

        return 'مدير المبيعات';
    }
}

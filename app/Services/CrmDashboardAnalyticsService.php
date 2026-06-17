<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Project;
use App\Models\RealEstateDeveloper;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrmDashboardAnalyticsService
{
    public const FUNNEL_STAGES = [
        'lead' => 'عميل جديد',
        'prospect' => 'تم التواصل',
        'proposal' => 'اجتماع',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم الإغلاق',
        'closed_lost' => 'خسارة',
    ];

    public const LEAD_SOURCE_LABELS = [
        'personal' => 'شخصي',
        'referral' => 'ترشيح',
        'event' => 'إيفينت',
        'marketing' => 'ماركتينج',
        'paid_ad' => 'إعلان ممول',
    ];

    public function __construct(
        protected CrmScopeService $scope,
        protected User $user,
        protected bool $isSuperAdmin,
        protected bool $isManager,
        protected bool $isRepOnly,
    ) {}

    public static function build(User $user): array
    {
        $scope = CrmScopeService::for($user);
        $isSuperAdmin = $user->hasRole(['super_admin', 'admin']);
        $isManager = $isSuperAdmin || $user->isSalesManager();
        $isRepOnly = $user->isSalesAgentOnly() && !$isSuperAdmin;

        $service = new self($scope, $user, $isSuperAdmin, $isManager, $isRepOnly);

        return [
            'user' => $user,
            'role' => $service->resolveRoleLabel(),
            'isSuperAdmin' => $isSuperAdmin,
            'isManager' => $isManager,
            'isRepOnly' => $isRepOnly,
            'kpis' => $service->kpis(),
            'revenueTrend' => $service->revenueTrend(),
            'leadsVsClosed' => $service->leadsVsClosedTrend(),
            'funnel' => $service->conversionFunnel(),
            'teamRanking' => $isManager ? $service->teamRanking() : collect(),
            'topReps' => $isManager ? $service->topReps(5) : collect(),
            'topTeams' => $isSuperAdmin ? $service->topTeams(5) : collect(),
            'leadLists' => $service->leadLists(),
            'leadSources' => $service->leadSourceBreakdown(),
            'properties' => $service->propertyInsights(),
            'portfolio' => $service->portfolioInsights(),
            'managerCenter' => $isManager ? $service->managerControlCenter() : null,
            'aiInsights' => $service->aiInsights(),
            'activities' => $service->activityFeed(),
            'geo' => $service->geographicalAnalytics(),
            'financial' => $service->financialDashboard(),
            'calendar' => $service->calendarItems(),
            'chartPayload' => $service->chartPayload(),
            'recentSales' => $scope->salesQuery()
                ->with(['client', 'project', 'salesRep'])
                ->latest()
                ->limit(8)
                ->get(),
            'projects' => Project::query()
                ->where(function ($q) {
                    $q->where('listing_status', 'active')
                        ->orWhere('status', 'in_progress');
                })
                ->orderBy('name')
                ->limit(6)
                ->get(),
        ];
    }

    protected function resolveRoleLabel(): string
    {
        if ($this->isSuperAdmin) {
            return 'مدير النظام';
        }
        if ($this->isManager) {
            return 'مدير المبيعات';
        }

        return 'مندوب مبيعات';
    }

    protected function kpis(): array
    {
        $clients = $this->scope->clientsQuery();
        $sales = $this->scope->salesQuery();

        $monthStart = Carbon::now()->startOfMonth();
        $totalLeads = (clone $clients)->count();
        $newToday = (clone $clients)->whereDate('created_at', today())->count();
        $qualified = (clone $clients)->whereIn('lead_stage', ['prospect', 'proposal'])->count();
        $activeOpps = (clone $sales)->whereIn('stage', ['lead', 'prospect', 'proposal', 'negotiation'])->count();
        $closedMonth = (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)
            ->count();
        $revenueMonth = (float) (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $wonTotal = (clone $sales)->where('stage', 'closed_won')->count();
        $conversion = $totalLeads > 0 ? round(($wonTotal / $totalLeads) * 100, 1) : 0;
        $avgDeal = $closedMonth > 0 ? round($revenueMonth / $closedMonth, 0) : 0;

        $lastMonthRevenue = (float) (clone $sales)->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $target = max($lastMonthRevenue * 1.1, 1);
        $targetAchievement = min(100, round(($revenueMonth / $target) * 100, 1));

        $responseHours = $this->averageLeadResponseHours();

        return [
            'total_leads' => $totalLeads,
            'new_leads_today' => $newToday,
            'qualified_leads' => $qualified,
            'active_opportunities' => $activeOpps,
            'closed_deals_month' => $closedMonth,
            'total_revenue' => $revenueMonth,
            'conversion_rate' => $conversion,
            'avg_deal_value' => $avgDeal,
            'target_achievement' => $targetAchievement,
            'lead_response_hours' => $responseHours,
            'pipeline_value' => (float) (clone $sales)->whereNotIn('stage', ['closed_lost'])->sum('estimated_value'),
        ];
    }

    protected function averageLeadResponseHours(): float
    {
        $pairs = $this->scope->salesQuery()
            ->select('client_id', DB::raw('MIN(created_at) as first_sale'))
            ->groupBy('client_id')
            ->get();

        if ($pairs->isEmpty()) {
            return 0;
        }

        $hours = 0;
        $count = 0;
        $clients = Client::whereIn('id', $pairs->pluck('client_id'))->get()->keyBy('id');

        foreach ($pairs as $row) {
            $client = $clients->get($row->client_id);
            if (!$client || !$row->first_sale) {
                continue;
            }
            $diff = $client->created_at->diffInMinutes(Carbon::parse($row->first_sale));
            $hours += $diff / 60;
            $count++;
        }

        return $count > 0 ? round($hours / $count, 1) : 0;
    }

    protected function revenueTrend(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $value = (float) $this->scope->salesQuery()
                ->where('stage', 'closed_won')
                ->whereBetween('actual_close_date', [$start, $end])
                ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
            $months->push([
                'label' => $start->locale('ar')->translatedFormat('M Y'),
                'value' => $value,
            ]);
        }

        return $months->all();
    }

    protected function leadsVsClosedTrend(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $data[] = [
                'label' => $start->locale('ar')->translatedFormat('M'),
                'leads' => $this->scope->clientsQuery()->whereBetween('created_at', [$start, $end])->count(),
                'closed' => $this->scope->salesQuery()
                    ->where('stage', 'closed_won')
                    ->whereBetween('actual_close_date', [$start, $end])
                    ->count(),
            ];
        }

        return $data;
    }

    protected function conversionFunnel(): array
    {
        $clientCounts = $this->scope->clientsQuery()
            ->select('lead_stage', DB::raw('count(*) as total'))
            ->groupBy('lead_stage')
            ->pluck('total', 'lead_stage');

        $saleCounts = $this->scope->salesQuery()
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->pluck('total', 'stage');

        $funnel = [];
        foreach (self::FUNNEL_STAGES as $stage => $label) {
            $count = (int) ($clientCounts[$stage] ?? 0);
            if (in_array($stage, ['closed_won', 'closed_lost'], true)) {
                $count += (int) ($saleCounts[$stage] ?? 0);
            } elseif ($count === 0) {
                $count = (int) ($saleCounts[$stage] ?? 0);
            }
            $funnel[] = ['stage' => $stage, 'label' => $label, 'count' => $count];
        }

        $max = max(1, collect($funnel)->max('count'));

        return array_map(fn ($row) => array_merge($row, [
            'percent' => round(($row['count'] / $max) * 100, 1),
        ]), $funnel);
    }

    protected function teamRanking(): Collection
    {
        return $this->rankedReps(10);
    }

    protected function topReps(int $limit): Collection
    {
        return $this->rankedReps($limit);
    }

    protected function rankedReps(int $limit): Collection
    {
        $memberIds = $this->isSuperAdmin
            ? User::whereHas('assignedSales')->pluck('id')
            : $this->scope->managedTeamMemberUserIds();

        return User::whereIn('id', $memberIds)
            ->with('employee')
            ->withCount([
                'assignedSales as won_count' => fn ($q) => $q->where('stage', 'closed_won')
                    ->where('actual_close_date', '>=', Carbon::now()->startOfMonth()),
            ])
            ->get()
            ->map(function (User $u) {
                $revenue = (float) $this->scope->salesQuery()
                    ->where('assigned_to', $u->id)
                    ->where('stage', 'closed_won')
                    ->where('actual_close_date', '>=', Carbon::now()->startOfMonth())
                    ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'title' => $u->employee?->job_title ?? 'مندوب مبيعات',
                    'won_count' => $u->won_count,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take($limit)
            ->values();
    }

    protected function topTeams(int $limit): Collection
    {
        $teamsQuery = $this->isSuperAdmin
            ? SalesTeam::query()
            : $this->scope->managedTeamsQuery();

        return $teamsQuery->with('manager')
            ->withCount([
                'sales as won_count' => fn ($q) => $q->where('stage', 'closed_won')
                    ->where('actual_close_date', '>=', Carbon::now()->startOfMonth()),
            ])
            ->get()
            ->map(function (SalesTeam $team) {
                $revenue = (float) $this->scope->salesQuery()
                    ->where('sales_team_id', $team->id)
                    ->where('stage', 'closed_won')
                    ->where('actual_close_date', '>=', Carbon::now()->startOfMonth())
                    ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));

                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'manager' => $team->manager?->name,
                    'won_count' => $team->won_count,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take($limit)
            ->values();
    }

    protected function leadLists(): array
    {
        $base = $this->scope->clientsQuery()->with(['assignedEmployee', 'sales']);

        return [
            'recent' => (clone $base)->latest()->limit(6)->get(),
            'high_priority' => (clone $base)->whereHas('sales', fn ($q) => $q
                ->where('probability_percentage', '>=', 60)
                ->orWhere('stage', 'negotiation'))
                ->limit(6)->get(),
            'unassigned' => (clone $base)->whereNull('assigned_to')->limit(6)->get(),
            'follow_up' => (clone $base)->whereHas('sales', fn ($q) => $q
                ->where('updated_at', '<', now()->subDays(3))
                ->whereNotIn('stage', ['closed_won', 'closed_lost']))
                ->limit(6)->get(),
        ];
    }

    protected function leadSourceBreakdown(): array
    {
        $raw = $this->scope->clientsQuery()
            ->select('lead_source', DB::raw('count(*) as total'))
            ->whereNotNull('lead_source')
            ->where('lead_source', '!=', '')
            ->groupBy('lead_source')
            ->pluck('total', 'lead_source');

        $normalized = array_fill_keys(Client::leadSourceKeys(), 0);

        foreach ($raw as $source => $count) {
            $key = Client::normalizeLeadSource($source) ?? 'personal';
            $normalized[$key] = ($normalized[$key] ?? 0) + (int) $count;
        }

        return collect($normalized)
            ->map(fn ($count, $key) => [
                'key' => $key,
                'label' => Client::leadSourceLabels()[$key] ?? $key,
                'count' => $count,
            ])
            ->sortByDesc('count')
            ->values()
            ->all();
    }

    protected function propertyInsights(): array
    {
        $projects = Project::query();

        return [
            'available' => (clone $projects)->where('listing_status', 'active')->count(),
            'reserved' => (clone $projects)->where('listing_status', 'upcoming')->count(),
            'sold' => (clone $projects)->whereIn('listing_status', ['sold_out', 'completed'])->count(),
            'top_viewed' => (clone $projects)->orderByDesc('progress_percentage')->limit(5)->get(),
            'top_requested' => $this->scope->salesQuery()
                ->select('project_id', DB::raw('count(*) as requests'))
                ->whereNotNull('project_id')
                ->groupBy('project_id')
                ->orderByDesc('requests')
                ->limit(5)
                ->with('project')
                ->get(),
            'by_type' => (clone $projects)
                ->select('property_type', DB::raw('count(*) as total'))
                ->groupBy('property_type')
                ->pluck('total', 'property_type')
                ->all(),
            'by_ownership' => (clone $projects)
                ->select('ownership_type', DB::raw('count(*) as total'))
                ->groupBy('ownership_type')
                ->pluck('total', 'ownership_type')
                ->all(),
        ];
    }

    protected function portfolioInsights(): array
    {
        $projects = Project::query();
        $byOwnership = (clone $projects)
            ->selectRaw('ownership_type, COUNT(*) as total, COALESCE(SUM(available_units), 0) as units, COALESCE(SUM(total_units), 0) as all_units')
            ->groupBy('ownership_type')
            ->get()
            ->keyBy('ownership_type');

        $ownershipRows = collect(Project::OWNERSHIP_TYPES)->map(function ($label, $key) use ($byOwnership) {
            $row = $byOwnership->get($key);

            return [
                'key' => $key,
                'label' => $label,
                'count' => (int) ($row->total ?? 0),
                'available_units' => (int) ($row->units ?? 0),
                'total_units' => (int) ($row->all_units ?? 0),
            ];
        })->values()->all();

        $topDevelopers = RealEstateDeveloper::query()
            ->withCount('projects')
            ->having('projects_count', '>', 0)
            ->orderByDesc('projects_count')
            ->limit(6)
            ->get()
            ->map(fn (RealEstateDeveloper $d) => [
                'id' => $d->id,
                'name' => $d->name,
                'projects_count' => $d->projects_count,
            ]);

        $recentByOwnership = [];
        foreach (array_keys(Project::OWNERSHIP_TYPES) as $type) {
            $recentByOwnership[$type] = (clone $projects)
                ->where('ownership_type', $type)
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get(['id', 'name', 'city', 'listing_status', 'ownership_type', 'available_units']);
        }

        return [
            'by_ownership' => $ownershipRows,
            'top_developers' => $topDevelopers,
            'recent_by_ownership' => $recentByOwnership,
            'developer_projects' => (clone $projects)->where('ownership_type', 'developer')->count(),
            'afaq_projects' => (clone $projects)->where('ownership_type', 'afaq_private')->count(),
            'owned_projects' => (clone $projects)->where('ownership_type', 'afaq_private')->count(),
            'partnership_projects' => (clone $projects)->where('ownership_type', 'partnership')->count(),
        ];
    }

    protected function managerControlCenter(): array
    {
        $sales = $this->scope->salesQuery();
        $monthRevenue = (float) (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', Carbon::now()->startOfMonth())
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));
        $target = max($monthRevenue * 1.15, 1);

        return [
            'team_target' => $target,
            'team_progress' => $monthRevenue,
            'team_progress_pct' => min(100, round(($monthRevenue / $target) * 100, 1)),
            'rep_performance' => $this->topReps(6),
            'daily_activity' => [
                'calls' => (clone $sales)->where('lead_source', 'call')->whereDate('updated_at', today())->count(),
                'meetings' => (clone $sales)->whereDate('viewing_date', '>=', today())->count(),
                'tours' => (clone $sales)->whereNotNull('viewing_date')->whereDate('viewing_date', today())->count(),
                'follow_ups' => (clone $sales)->whereDate('updated_at', today())->count(),
                'negotiations' => (clone $sales)->where('stage', 'negotiation')->count(),
            ],
        ];
    }

    protected function aiInsights(): array
    {
        $highIntent = $this->scope->salesQuery()
            ->with(['client', 'project'])
            ->where(function ($q) {
                $q->where('probability_percentage', '>=', 65)
                    ->orWhere('stage', 'negotiation');
            })
            ->orderByDesc('probability_percentage')
            ->limit(5)
            ->get();

        $stale = $this->scope->clientsQuery()
            ->whereHas('sales', fn ($q) => $q
                ->where('updated_at', '<', now()->subDays(2))
                ->whereNotIn('stage', ['closed_won', 'closed_lost']))
            ->limit(5)
            ->get();

        $actions = [];
        foreach ($stale->take(3) as $client) {
            $actions[] = "متابعة عاجلة: {$client->name} — بدون نشاط منذ 48+ ساعة";
        }
        if ($actions === []) {
            $actions[] = 'لا توجد متابعات عاجلة — استمر في جذب عملاء جدد';
        }

        $probable = $this->scope->salesQuery()
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderByDesc('probability_percentage')
            ->limit(4)
            ->get()
            ->map(fn (Sale $s) => [
                'label' => $s->client?->name ?? $s->product_service,
                'score' => (int) ($s->probability_percentage ?? 50),
            ]);

        return [
            'high_intent' => $highIntent,
            'actions' => $actions,
            'closing_scores' => $probable,
            'reminders' => [
                ['text' => 'مراجعة صفقات التفاوض قبل نهاية الأسبوع', 'time' => 'اليوم 17:00'],
                ['text' => 'تأكيد مواعيد المعاينة غداً', 'time' => 'غداً 09:00'],
            ],
        ];
    }

    protected function activityFeed(): Collection
    {
        $logsQuery = ActivityLog::with('user')->latest();
        if ($this->isRepOnly) {
            $logsQuery->where('user_id', $this->user->id);
        } elseif ($this->isManager && !$this->isSuperAdmin) {
            $logsQuery->whereIn('user_id', $this->scope->managedTeamMemberUserIds());
        }

        $logs = collect($logsQuery->limit(8)->get())
            ->map(fn ($log) => [
                'type' => 'log',
                'title' => $log->description ?? $log->action,
                'meta' => $log->user?->name ?? 'النظام',
                'time' => $log->created_at,
            ]);

        $sales = collect($this->scope->salesQuery()
            ->with(['client', 'salesRep'])
            ->latest()
            ->limit(5)
            ->get())
            ->map(fn (Sale $s) => [
                'type' => 'sale',
                'title' => 'تحديث صفقة: ' . ($s->client?->name ?? '—'),
                'meta' => $s->salesRep?->name ?? '—',
                'time' => $s->updated_at,
            ]);

        return $logs->merge($sales)->sortByDesc(fn (array $item) => $item['time'])->take(10)->values();
    }

    protected function geographicalAnalytics(): array
    {
        $byCity = Project::query()
            ->select('city', DB::raw('count(*) as properties'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('properties')
            ->limit(8)
            ->get();

        $salesByCity = $this->scope->salesQuery()
            ->join('projects', 'sales.project_id', '=', 'projects.id')
            ->whereNotNull('sales.project_id')
            ->select('projects.city', DB::raw('count(*) as deals'), DB::raw('SUM(COALESCE(sales.actual_value, sales.estimated_value)) as revenue'))
            ->whereNotNull('projects.city')
            ->groupBy('projects.city')
            ->orderByDesc('deals')
            ->limit(8)
            ->get();

        return [
            'leads_by_city' => $byCity,
            'sales_by_area' => $salesByCity,
            'hotspots' => $salesByCity->take(5)->map(fn ($r) => [
                'city' => $r->city,
                'score' => (int) $r->deals,
            ]),
        ];
    }

    protected function financialDashboard(): array
    {
        $sales = $this->scope->salesQuery();
        $month = (float) (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', Carbon::now()->startOfMonth())
            ->sum(DB::raw('COALESCE(actual_value, estimated_value)'));

        $byTeam = $this->scope->salesQuery()
            ->join('sales_teams', 'sales.sales_team_id', '=', 'sales_teams.id')
            ->select('sales_teams.name', DB::raw('SUM(COALESCE(sales.actual_value, sales.estimated_value)) as revenue'))
            ->where('sales.stage', 'closed_won')
            ->where('sales.actual_close_date', '>=', Carbon::now()->startOfMonth())
            ->groupBy('sales_teams.id', 'sales_teams.name')
            ->orderByDesc('revenue')
            ->limit(6)
            ->get();

        $byType = Project::query()
            ->select('property_type', DB::raw('SUM(COALESCE(price_from, 0)) as value'))
            ->groupBy('property_type')
            ->get();

        $pipeline = (float) (clone $sales)->whereNotIn('stage', ['closed_lost', 'closed_won'])->sum('estimated_value');

        return [
            'monthly_revenue' => $month,
            'by_team' => $byTeam,
            'by_property_type' => $byType,
            'commission_estimate' => round($month * 0.025, 0),
            'outstanding' => round($pipeline * 0.15, 0),
            'installment_collected_pct' => min(100, round(($month / max($pipeline, 1)) * 100, 1)),
        ];
    }

    protected function calendarItems(): array
    {
        $sales = $this->scope->salesQuery();

        return [
            'meetings' => (clone $sales)->whereDate('viewing_date', '>=', today())
                ->whereDate('viewing_date', '<=', today()->addDays(14))
                ->with(['client', 'project'])
                ->orderBy('viewing_date')
                ->limit(6)
                ->get(),
            'follow_ups' => (clone $sales)->whereBetween('expected_close_date', [today(), today()->addDays(14)])
                ->with('client')
                ->limit(5)
                ->get(),
            'deadlines' => (clone $sales)->where('stage', 'negotiation')
                ->whereNotNull('expected_close_date')
                ->orderBy('expected_close_date')
                ->limit(5)
                ->get(),
        ];
    }

    protected function chartPayload(): array
    {
        return [
            'revenue' => $this->revenueTrend(),
            'leadsClosed' => $this->leadsVsClosedTrend(),
            'sources' => $this->leadSourceBreakdown(),
            'propertyTypes' => collect($this->propertyInsights()['by_type'] ?? [])->map(fn ($c, $t) => [
                'label' => Project::PROPERTY_TYPES[$t] ?? $t,
                'count' => $c,
            ])->values()->all(),
            'ownershipTypes' => collect($this->portfolioInsights()['by_ownership'] ?? [])->map(fn ($row) => [
                'label' => $row['label'],
                'count' => $row['count'],
            ])->values()->all(),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\MarketingActivity;
use App\Models\MarketingCampaign;
use App\Models\User;
use Carbon\Carbon;

class MarketingDashboardService
{
    public static function build(User $user): array
    {
        $scope = MarketingScopeService::for($user);
        $resolver = MarketingRoleResolver::for($user);
        $isManager = $resolver->isManager() || $resolver->isAdmin();
        $isRep = $resolver->isRep();

        $campaigns = $scope->campaignsQuery();
        $activities = $scope->activitiesQuery();
        $leads = $scope->leadsQuery();

        $thisMonth = Carbon::now()->startOfMonth();

        $kpis = [
            'active_campaigns' => (clone $campaigns)->where('status', 'active')->count(),
            'total_campaigns' => (clone $campaigns)->count(),
            'leads_month' => (clone $leads)->where('created_at', '>=', $thisMonth)->count(),
            'leads_today' => (clone $leads)->whereDate('created_at', today())->count(),
            'activities_today' => (clone $activities)->dueToday()->count(),
            'activities_overdue' => (clone $activities)->overdue()->count(),
            'budget_total' => (float) (clone $campaigns)->sum('budget'),
            'spent_total' => (float) (clone $campaigns)->sum('spent_amount'),
            'recurring_active' => (clone $activities)->pending()
                ->where('recurrence', '!=', 'none')
                ->count(),
        ];

        $recentCampaigns = (clone $campaigns)
            ->with(['manager:id,name', 'project:id,name'])
            ->withCount('leads')
            ->latest()
            ->take(5)
            ->get();

        $upcomingActivities = (clone $activities)
            ->pending()
            ->with(['assignee:id,name', 'campaign:id,name'])
            ->where('due_at', '>=', now())
            ->orderBy('due_at')
            ->take(8)
            ->get();

        $overdueActivities = (clone $activities)
            ->overdue()
            ->with(['assignee:id,name', 'campaign:id,name'])
            ->orderBy('due_at')
            ->take(5)
            ->get();

        $leadsByChannel = (clone $campaigns)
            ->selectRaw('channel, COUNT(*) as campaigns_count')
            ->groupBy('channel')
            ->get()
            ->map(fn ($row) => [
                'channel' => config('marketing.channels.' . $row->channel, $row->channel),
                'count' => $row->campaigns_count,
            ]);

        $leadsTrend = (clone $leads)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $role = $resolver->isAdmin()
            ? 'إدارة'
            : ($isManager ? 'مدير تسويق' : 'موظف تسويق');

        $compliance = app(MarketingReportComplianceService::class);
        $reportPending = $compliance->pendingFor($user);
        $teamDailyStatus = $isManager ? $compliance->teamDailyStatus($user) : [];

        return compact(
            'user', 'kpis', 'recentCampaigns', 'upcomingActivities',
            'overdueActivities', 'leadsByChannel', 'leadsTrend',
            'isManager', 'isRep', 'role', 'resolver',
            'reportPending', 'teamDailyStatus'
        );
    }
}

<?php

namespace App\Services;

use App\Models\Client;
use App\Models\MarketingActivity;
use App\Models\MarketingCampaign;
use App\Models\MarketingPeriodReport;
use App\Models\User;
use Carbon\Carbon;

class MarketingReportMetricsService
{
    public function build(User $user, string $periodType, Carbon $start, Carbon $end): array
    {
        $rangeStart = $start->copy()->startOfDay();
        $rangeEnd = $end->copy()->endOfDay();

        $activities = MarketingActivity::query()->where('assigned_to', $user->id);
        $leads = Client::query()->where('created_by', $user->id);

        $metrics = [
            'generated_at' => now()->toIso8601String(),
            'period' => [
                'type' => $periodType,
                'start' => $rangeStart->toDateString(),
                'end' => $rangeEnd->toDateString(),
            ],
            'activities' => [
                'assigned' => (clone $activities)->whereBetween('due_at', [$rangeStart, $rangeEnd])->count(),
                'completed' => (clone $activities)->where('status', MarketingActivity::STATUS_COMPLETED)
                    ->whereBetween('completed_at', [$rangeStart, $rangeEnd])->count(),
                'overdue' => (clone $activities)->overdue()->count(),
            ],
            'leads' => [
                'created' => (clone $leads)->whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
                'total_owned' => (clone $leads)->count(),
            ],
            'campaigns' => [
                'active_involved' => MarketingCampaign::query()
                    ->where(function ($q) use ($user) {
                        $q->where('manager_id', $user->id)->orWhere('created_by', $user->id);
                    })
                    ->where('status', 'active')
                    ->count(),
            ],
        ];

        if ($user->isMarketingManager()) {
            $metrics['team'] = $this->teamRollup($user, $periodType, $rangeStart, $rangeEnd);
        }

        return $metrics;
    }

    protected function teamRollup(User $manager, string $periodType, Carbon $start, Carbon $end): array
    {
        $scope = MarketingScopeService::for($manager);
        $teamIds = collect($scope->teamUserIds())->filter(fn ($id) => (int) $id !== (int) $manager->id)->values();

        $submittedDaily = MarketingPeriodReport::query()
            ->where('period_type', MarketingPeriodReport::PERIOD_DAILY)
            ->where('status', MarketingPeriodReport::STATUS_SUBMITTED)
            ->whereDate('period_start', today())
            ->whereIn('user_id', $teamIds)
            ->count();

        $teamLeads = Client::query()
            ->whereIn('created_by', $teamIds)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $teamActivities = MarketingActivity::query()
            ->whereIn('assigned_to', $teamIds)
            ->where('status', MarketingActivity::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$start, $end])
            ->count();

        return [
            'members_count' => $teamIds->count(),
            'leads_created' => $teamLeads,
            'activities_completed' => $teamActivities,
            'daily_reports_submitted_today' => $submittedDaily,
            'daily_reports_missing_today' => max(0, $teamIds->count() - $submittedDaily),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\MarketingPeriodReport;
use App\Models\User;
use Carbon\Carbon;

class MarketingReportComplianceService
{
    public function mandatoryTypesFor(User $user): array
    {
        if ($user->isMarketingManager()) {
            return config('marketing_reports.mandatory_by_role.marketing_manager', ['daily']);
        }

        if ($user->usesMarketingWorkspace()) {
            return config('marketing_reports.mandatory_by_role.marketing_rep', ['daily']);
        }

        return [];
    }

    /** @return array<int, array{type: string, label: string, status: string, report: ?MarketingPeriodReport}> */
    public function pendingFor(User $user): array
    {
        $pending = [];

        foreach ($this->mandatoryTypesFor($user) as $type) {
            $report = $this->currentReport($user, $type);

            if (!$report || $report->isDraft()) {
                $pending[] = [
                    'type' => $type,
                    'label' => config('marketing_reports.period_types.' . $type, $type),
                    'status' => $report ? 'draft' : 'missing',
                    'report' => $report,
                ];
            }
        }

        return $pending;
    }

    public function currentReport(User $user, string $type, ?Carbon $anchor = null): ?MarketingPeriodReport
    {
        $anchor = $anchor ?? now();
        $period = MarketingPeriodReport::resolvePeriod($type, $anchor);

        return MarketingPeriodReport::query()
            ->where('user_id', $user->id)
            ->where('period_type', $type)
            ->whereDate('period_start', $period['start']->toDateString())
            ->first();
    }

    public function isCompliant(User $user): bool
    {
        return $this->pendingFor($user) === [];
    }

    /** @return array<int, array{user: User, submitted: bool, report: ?MarketingPeriodReport}> */
    public function teamDailyStatus(User $manager): array
    {
        if (!$manager->isMarketingManager()) {
            return [];
        }

        $scope = MarketingScopeService::for($manager);
        $teamIds = collect($scope->teamUserIds())
            ->filter(fn ($id) => (int) $id !== (int) $manager->id);

        $users = User::whereIn('id', $teamIds)->orderBy('name')->get(['id', 'name']);
        $result = [];

        foreach ($users as $member) {
            $report = $this->currentReport($member, MarketingPeriodReport::PERIOD_DAILY);
            $result[] = [
                'user' => $member,
                'submitted' => $report?->isSubmitted() ?? false,
                'report' => $report,
            ];
        }

        return $result;
    }
}

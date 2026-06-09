<?php

namespace App\Services;

use App\Models\MarketingActivity;
use App\Models\MarketingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MarketingPlanService
{
    public function __construct(protected MarketingScopeService $scope) {}

    public static function for(User $user): self
    {
        return new self(MarketingScopeService::for($user));
    }

    /** @return array<int, Collection<int, MarketingActivity>> */
    public function calendarByDay(MarketingPlan $plan): array
    {
        $start = Carbon::create($plan->year, $plan->month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        $activities = $plan->activities()
            ->with(['assignee:id,name'])
            ->whereBetween('due_at', [$start, $end])
            ->orderBy('due_at')
            ->get()
            ->groupBy(fn ($a) => $a->due_at?->format('Y-m-d') ?? 'none');

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $days[(int) $d->format('j')] = $activities->get($key, collect());
        }

        return $days;
    }

    public function createTasksFromRows(MarketingPlan $plan, array $rows, int $assignedBy): int
    {
        $count = 0;
        foreach ($rows as $row) {
            if (empty($row['title'])) {
                continue;
            }

            $day = max(1, min(31, (int) ($row['due_day'] ?? 1)));
            $due = Carbon::create($plan->year, $plan->month, 1)
                ->day(min($day, Carbon::create($plan->year, $plan->month, 1)->daysInMonth))
                ->setTime(9, 0);

            MarketingActivity::create([
                'title' => $row['title'],
                'description' => $row['description'] ?? null,
                'type' => $row['type'] ?? 'content',
                'status' => MarketingActivity::STATUS_PENDING,
                'priority' => $row['priority'] ?? 'medium',
                'campaign_id' => $plan->campaign_id,
                'marketing_plan_id' => $plan->id,
                'assigned_to' => $row['assigned_to'] ?? $assignedBy,
                'assigned_by' => $assignedBy,
                'due_at' => $due,
                'recurrence' => 'none',
            ]);
            $count++;
        }

        return $count;
    }

    public function distributeEvenly(MarketingPlan $plan, array $taskTitles, array $userIds, int $assignedBy): int
    {
        if (empty($taskTitles) || empty($userIds)) {
            return 0;
        }

        $start = Carbon::create($plan->year, $plan->month, 1);
        $daysInMonth = $start->daysInMonth;
        $rows = [];
        $userIndex = 0;
        $dayStep = max(1, (int) floor($daysInMonth / count($taskTitles)));

        foreach ($taskTitles as $i => $title) {
            $title = trim($title);
            if ($title === '') {
                continue;
            }
            $day = min($daysInMonth, 1 + ($i * $dayStep));
            $rows[] = [
                'title' => $title,
                'due_day' => $day,
                'assigned_to' => $userIds[$userIndex % count($userIds)],
                'type' => 'content',
                'priority' => 'medium',
            ];
            $userIndex++;
        }

        return $this->createTasksFromRows($plan, $rows, $assignedBy);
    }
}

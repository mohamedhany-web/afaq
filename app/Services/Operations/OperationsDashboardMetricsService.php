<?php

namespace App\Services\Operations;

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OperationsDashboardMetricsService
{
    public function snapshot(?Carbon $reference = null): array
    {
        $today = ($reference ?? now())->copy()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $monthStart = $today->copy()->startOfMonth();

        return [
            'today_comments' => $this->commentsCount($today),
            'yesterday_comments' => $this->commentsCount($yesterday),
            'missed_reminders_yesterday' => $this->missedRemindersCount($yesterday),
            'today_reminders' => $this->todayRemindersCount($today),
            'done_deals' => $this->doneDealsCount($monthStart, $today),
            'potential_clients' => $this->potentialClientsCount(),
            'cancelled_deals' => $this->cancelledDealsCount($monthStart, $today),
            'recent' => [
                'today_comments' => $this->recentComments($today, 5),
                'yesterday_comments' => $this->recentComments($yesterday, 5),
                'missed_reminders_yesterday' => $this->recentMissedReminders($yesterday, 5),
                'today_reminders' => $this->recentTodayReminders($today, 5),
                'done_deals' => $this->recentDoneDeals($monthStart, 5),
                'potential_clients' => $this->recentPotentialClients(5),
                'cancelled_deals' => $this->recentCancelledDeals($monthStart, 5),
            ],
        ];
    }

    public function commentsCount(Carbon $day, ?int $salesRepUserId = null): int
    {
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        return CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_COMPLETED)
            ->when($salesRepUserId, fn ($q) => $q->where('user_id', $salesRepUserId))
            ->whereBetween('completed_at', [$start, $end])
            ->count();
    }

    public function missedRemindersCount(Carbon $day): int
    {
        return $this->missedFollowUpsQuery($day)->count()
            + $this->missedTasksQuery($day)->count();
    }

    public function todayRemindersCount(Carbon $day): int
    {
        return $this->scheduledFollowUpsQuery($day)->count()
            + $this->dueTasksQuery($day)->count();
    }

    public function doneDealsCount(Carbon $from, Carbon $to): int
    {
        return Sale::query()
            ->where('stage', 'closed_won')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('actual_close_date', [$from->toDateString(), $to->toDateString()])
                    ->orWhere(function ($sub) use ($from, $to) {
                        $sub->whereNull('actual_close_date')
                            ->whereBetween('updated_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
                    });
            })
            ->count();
    }

    public function potentialClientsCount(): int
    {
        return Client::query()
            ->where(function ($q) {
                $q->where('status', 'prospect')
                    ->orWhereIn('lead_stage', ['lead', 'prospect', 'proposal']);
            })
            ->count();
    }

    public function cancelledDealsCount(Carbon $from, Carbon $to): int
    {
        return Sale::query()
            ->where('stage', 'closed_lost')
            ->whereBetween('updated_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();
    }

    /** @return Collection<int, CrmFollowUp> */
    public function recentComments(Carbon $day, int $limit = 5): Collection
    {
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        return CrmFollowUp::query()
            ->with(['client:id,name', 'user:id,name'])
            ->where('status', CrmFollowUp::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$start, $end])
            ->orderByDesc('completed_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, mixed> */
    public function recentMissedReminders(Carbon $day, int $limit = 5): Collection
    {
        $followUps = $this->missedFollowUpsQuery($day)
            ->with(['client:id,name', 'user:id,name'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => ['type' => 'follow_up', 'item' => $item]);

        $remaining = max(0, $limit - $followUps->count());
        $tasks = $remaining > 0
            ? $this->missedTasksQuery($day)
                ->with(['assignee:id,name', 'client:id,name'])
                ->orderBy('due_at')
                ->limit($remaining)
                ->get()
                ->map(fn ($item) => ['type' => 'task', 'item' => $item])
            : collect();

        return $followUps->concat($tasks)->take($limit);
    }

    /** @return Collection<int, mixed> */
    public function recentTodayReminders(Carbon $day, int $limit = 5): Collection
    {
        $followUps = $this->scheduledFollowUpsQuery($day)
            ->with(['client:id,name', 'user:id,name'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => ['type' => 'follow_up', 'item' => $item]);

        $remaining = max(0, $limit - $followUps->count());
        $tasks = $remaining > 0
            ? $this->dueTasksQuery($day)
                ->with(['assignee:id,name', 'client:id,name'])
                ->orderBy('due_at')
                ->limit($remaining)
                ->get()
                ->map(fn ($item) => ['type' => 'task', 'item' => $item])
            : collect();

        return $followUps->concat($tasks)->take($limit);
    }

    /** @return Collection<int, Sale> */
    public function recentDoneDeals(Carbon $from, int $limit = 5): Collection
    {
        return Sale::query()
            ->with(['client:id,name', 'salesRep:id,name', 'project:id,name'])
            ->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $from->toDateString())
            ->orderByDesc('actual_close_date')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, Client> */
    public function recentPotentialClients(int $limit = 5): Collection
    {
        return Client::query()
            ->with('assignedEmployee:id,first_name,last_name')
            ->where(function ($q) {
                $q->where('status', 'prospect')
                    ->orWhereIn('lead_stage', ['lead', 'prospect', 'proposal']);
            })
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, Sale> */
    public function recentCancelledDeals(Carbon $from, int $limit = 5): Collection
    {
        return Sale::query()
            ->with(['client:id,name', 'salesRep:id,name'])
            ->where('stage', 'closed_lost')
            ->where('updated_at', '>=', $from->copy()->startOfDay())
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    protected function missedFollowUpsQuery(Carbon $day)
    {
        return CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', $day->toDateString());
    }

    protected function missedTasksQuery(Carbon $day)
    {
        return CrmTask::query()
            ->whereDate('due_at', $day->toDateString())
            ->whereNotIn('status', [
                CrmTask::STATUS_COMPLETED,
                CrmTask::STATUS_VERIFIED,
                CrmTask::STATUS_CANCELLED,
                CrmTask::STATUS_ARCHIVED,
            ]);
    }

    protected function scheduledFollowUpsQuery(Carbon $day)
    {
        return CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', $day->toDateString());
    }

    protected function dueTasksQuery(Carbon $day)
    {
        return CrmTask::query()
            ->whereDate('due_at', $day->toDateString())
            ->whereNotIn('status', [
                CrmTask::STATUS_COMPLETED,
                CrmTask::STATUS_VERIFIED,
                CrmTask::STATUS_CANCELLED,
                CrmTask::STATUS_ARCHIVED,
            ]);
    }
}

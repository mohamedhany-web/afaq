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
    public function snapshot(?Carbon $reference = null, ?int $salesRepUserId = null): array
    {
        $today = ($reference ?? now())->copy()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $monthStart = $today->copy()->startOfMonth();

        return [
            'today_comments' => $this->commentsCount($today, $salesRepUserId),
            'yesterday_comments' => $this->commentsCount($yesterday, $salesRepUserId),
            'missed_reminders_yesterday' => $this->missedRemindersCount($yesterday, $salesRepUserId),
            'today_reminders' => $this->todayRemindersCount($today, $salesRepUserId),
            'done_deals' => $this->doneDealsCount($monthStart, $today, $salesRepUserId),
            'new_clients' => $this->newClientsCount($salesRepUserId),
            'potential_clients' => $this->potentialClientsCount($salesRepUserId),
            'cancelled_deals' => $this->cancelledDealsCount($monthStart, $today, $salesRepUserId),
            'recent' => [
                'today_comments' => $this->recentComments($today, 5, $salesRepUserId),
                'yesterday_comments' => $this->recentComments($yesterday, 5, $salesRepUserId),
                'missed_reminders_yesterday' => $this->recentMissedReminders($yesterday, 5, $salesRepUserId),
                'today_reminders' => $this->recentTodayReminders($today, 5, $salesRepUserId),
                'done_deals' => $this->recentDoneDeals($monthStart, 5, $salesRepUserId),
                'new_clients' => $this->recentNewClients(5, $salesRepUserId),
                'potential_clients' => $this->recentPotentialClients(5, $salesRepUserId),
                'cancelled_deals' => $this->recentCancelledDeals($monthStart, 5, $salesRepUserId),
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

    public function missedRemindersCount(Carbon $day, ?int $salesRepUserId = null): int
    {
        return $this->missedFollowUpsQuery($day, $salesRepUserId)->count()
            + $this->missedTasksQuery($day, $salesRepUserId)->count();
    }

    public function todayRemindersCount(Carbon $day, ?int $salesRepUserId = null): int
    {
        return $this->scheduledFollowUpsQuery($day, $salesRepUserId)->count()
            + $this->dueTasksQuery($day, $salesRepUserId)->count();
    }

    public function doneDealsCount(Carbon $from, Carbon $to, ?int $salesRepUserId = null): int
    {
        return Sale::query()
            ->when($salesRepUserId, fn ($q) => $q->where('assigned_to', $salesRepUserId))
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

    public function potentialClientsCount(?int $salesRepUserId = null): int
    {
        return $this->filterClientsByRep(Client::query(), $salesRepUserId)
            ->where(function ($q) {
                $q->where('status', 'prospect')
                    ->orWhereIn('lead_stage', ['lead', 'prospect', 'proposal']);
            })
            ->count();
    }

    public function newClientsCount(?int $salesRepUserId = null): int
    {
        return $this->filterClientsByRep(Client::query(), $salesRepUserId)
            ->where('lead_stage', \App\Services\CrmScopeService::LEAD_STAGE_NEW)
            ->count();
    }

    public function cancelledDealsCount(Carbon $from, Carbon $to, ?int $salesRepUserId = null): int
    {
        return Sale::query()
            ->when($salesRepUserId, fn ($q) => $q->where('assigned_to', $salesRepUserId))
            ->where('stage', 'closed_lost')
            ->whereBetween('updated_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();
    }

    /** @return Collection<int, CrmFollowUp> */
    public function recentComments(Carbon $day, int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        return CrmFollowUp::query()
            ->with(['client:id,name', 'user:id,name'])
            ->when($salesRepUserId, fn ($q) => $q->where('user_id', $salesRepUserId))
            ->where('status', CrmFollowUp::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$start, $end])
            ->orderByDesc('completed_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, mixed> */
    public function recentMissedReminders(Carbon $day, int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        $followUps = $this->missedFollowUpsQuery($day, $salesRepUserId)
            ->with(['client:id,name', 'user:id,name'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => ['type' => 'follow_up', 'item' => $item]);

        $remaining = max(0, $limit - $followUps->count());
        $tasks = $remaining > 0
            ? $this->missedTasksQuery($day, $salesRepUserId)
                ->with(['assignee:id,name', 'client:id,name'])
                ->orderBy('due_at')
                ->limit($remaining)
                ->get()
                ->map(fn ($item) => ['type' => 'task', 'item' => $item])
            : collect();

        return $followUps->concat($tasks)->take($limit);
    }

    /** @return Collection<int, mixed> */
    public function recentTodayReminders(Carbon $day, int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        $followUps = $this->scheduledFollowUpsQuery($day, $salesRepUserId)
            ->with(['client:id,name', 'user:id,name'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => ['type' => 'follow_up', 'item' => $item]);

        $remaining = max(0, $limit - $followUps->count());
        $tasks = $remaining > 0
            ? $this->dueTasksQuery($day, $salesRepUserId)
                ->with(['assignee:id,name', 'client:id,name'])
                ->orderBy('due_at')
                ->limit($remaining)
                ->get()
                ->map(fn ($item) => ['type' => 'task', 'item' => $item])
            : collect();

        return $followUps->concat($tasks)->take($limit);
    }

    /** @return Collection<int, Sale> */
    public function recentDoneDeals(Carbon $from, int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        return Sale::query()
            ->with(['client:id,name', 'salesRep:id,name', 'project:id,name'])
            ->when($salesRepUserId, fn ($q) => $q->where('assigned_to', $salesRepUserId))
            ->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $from->toDateString())
            ->orderByDesc('actual_close_date')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, Client> */
    public function recentNewClients(int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        return $this->filterClientsByRep(Client::query(), $salesRepUserId)
            ->with('assignedEmployee:id,first_name,last_name')
            ->where('lead_stage', \App\Services\CrmScopeService::LEAD_STAGE_NEW)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, Client> */
    public function recentPotentialClients(int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        return $this->filterClientsByRep(Client::query(), $salesRepUserId)
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
    public function recentCancelledDeals(Carbon $from, int $limit = 5, ?int $salesRepUserId = null): Collection
    {
        return Sale::query()
            ->with(['client:id,name', 'salesRep:id,name'])
            ->when($salesRepUserId, fn ($q) => $q->where('assigned_to', $salesRepUserId))
            ->where('stage', 'closed_lost')
            ->where('updated_at', '>=', $from->copy()->startOfDay())
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    protected function filterClientsByRep($query, ?int $salesRepUserId)
    {
        if (! $salesRepUserId) {
            return $query;
        }

        $employeeId = \App\Models\Employee::query()->where('user_id', $salesRepUserId)->value('id');

        return $query->when($employeeId, fn ($q) => $q->where('assigned_to', $employeeId));
    }

    protected function missedFollowUpsQuery(Carbon $day, ?int $salesRepUserId = null)
    {
        return CrmFollowUp::query()
            ->when($salesRepUserId, fn ($q) => $q->where('user_id', $salesRepUserId))
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', $day->toDateString());
    }

    protected function missedTasksQuery(Carbon $day, ?int $salesRepUserId = null)
    {
        return CrmTask::query()
            ->when($salesRepUserId, fn ($q) => $q->where('assigned_to', $salesRepUserId))
            ->whereDate('due_at', $day->toDateString())
            ->whereNotIn('status', [
                CrmTask::STATUS_COMPLETED,
                CrmTask::STATUS_VERIFIED,
                CrmTask::STATUS_CANCELLED,
                CrmTask::STATUS_ARCHIVED,
            ]);
    }

    protected function scheduledFollowUpsQuery(Carbon $day, ?int $salesRepUserId = null)
    {
        return CrmFollowUp::query()
            ->when($salesRepUserId, fn ($q) => $q->where('user_id', $salesRepUserId))
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', $day->toDateString());
    }

    protected function dueTasksQuery(Carbon $day, ?int $salesRepUserId = null)
    {
        return CrmTask::query()
            ->when($salesRepUserId, fn ($q) => $q->where('assigned_to', $salesRepUserId))
            ->whereDate('due_at', $day->toDateString())
            ->whereNotIn('status', [
                CrmTask::STATUS_COMPLETED,
                CrmTask::STATUS_VERIFIED,
                CrmTask::STATUS_CANCELLED,
                CrmTask::STATUS_ARCHIVED,
            ]);
    }
}

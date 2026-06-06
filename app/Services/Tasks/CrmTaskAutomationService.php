<?php

namespace App\Services\Tasks;

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrmTaskAutomationService
{
    public function __construct(
        protected CrmTaskService $tasks,
    ) {}

    public function run(): array
    {
        return [
            'overdue_followups' => $this->overdueFollowUps(),
            'hot_leads' => $this->hotLeadsNotContacted(),
            'stuck_pipeline' => $this->stuckPipelineDeals(),
        ];
    }

    protected function createSystemTask(
        string $sourceKey,
        int $assignedTo,
        string $title,
        string $description,
        string $priority,
        string $category,
        Carbon $dueAt,
        ?int $clientId = null,
        ?int $saleId = null,
    ): ?CrmTask {
        $existing = CrmTask::query()
            ->where('source_key', $sourceKey)
            ->whereIn('status', config('crm_tasks.active_statuses', []))
            ->first();

        if ($existing) {
            return null;
        }

        try {
            $task = CrmTask::create([
                'title' => $title,
                'description' => $description,
                'assigned_to' => $assignedTo,
                'assigned_by' => null,
                'assigner_type' => 'system',
                'priority' => $priority,
                'status' => CrmTask::STATUS_PENDING,
                'category' => $category,
                'client_id' => $clientId,
                'sale_id' => $saleId,
                'due_at' => $dueAt,
                'requires_acceptance' => false,
                'auto_generated' => true,
                'source_key' => $sourceKey,
                'accepted_at' => now(),
            ]);

            $this->tasks->log($task, null, 'auto_created', null, $task->status, 'مهمة تلقائية من النظام');
            \App\Services\CrmNotificationService::notifyTaskAssigned($task);

            return $task;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function overdueFollowUps(): int
    {
        $count = 0;
        $followUps = CrmFollowUp::query()
            ->where('status', CrmFollowUp::STATUS_SCHEDULED)
            ->where('scheduled_at', '<', now())
            ->with('client:id,name', 'user:id,name')
            ->limit(100)
            ->get();

        foreach ($followUps as $fu) {
            if (!$fu->user_id) {
                continue;
            }
            $key = 'auto:followup:' . $fu->id;
            $task = $this->createSystemTask(
                $key,
                $fu->user_id,
                'متابعة متأخرة: ' . ($fu->client?->name ?? 'عميل'),
                'موعد متابعة فات موعده في ' . $fu->scheduled_at->format('Y-m-d H:i'),
                'critical',
                'follow_ups',
                now()->addHours(2),
                $fu->client_id,
                $fu->sale_id,
            );
            if ($task) {
                $count++;
            }
        }

        return $count;
    }

    protected function hotLeadsNotContacted(): int
    {
        $hours = config('crm_tasks.hot_lead_hours', 2);
        $cutoff = now()->subHours($hours);
        $count = 0;

        $clients = Client::query()
            ->whereIn('lead_stage', ['lead', 'prospect'])
            ->where('created_at', '<=', $cutoff)
            ->where('updated_at', '<=', $cutoff)
            ->whereNotNull('assigned_to')
            ->with('assignedEmployee.user:id')
            ->limit(50)
            ->get();

        foreach ($clients as $client) {
            $userId = $client->assignedEmployee?->user_id;
            if (!$userId) {
                continue;
            }
            $key = 'auto:hot:' . $client->id;
            $task = $this->createSystemTask(
                $key,
                $userId,
                'عميل ساخن بلا تواصل: ' . $client->name,
                "لم يُحدَّث العميل منذ أكثر من {$hours} ساعات.",
                'high',
                'calls',
                now()->addHours(1),
                $client->id,
            );
            if ($task) {
                $count++;
            }
        }

        return $count;
    }

    protected function stuckPipelineDeals(): int
    {
        $days = config('crm_tasks.stuck_pipeline_days', 5);
        $cutoff = now()->subDays($days);
        $count = 0;

        $sales = Sale::query()
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->where('updated_at', '<=', $cutoff)
            ->whereNotNull('assigned_to')
            ->with('client:id,name')
            ->limit(50)
            ->get();

        foreach ($sales as $sale) {
            $key = 'auto:stuck:' . $sale->id;
            $task = $this->createSystemTask(
                $key,
                $sale->assigned_to,
                'صفقة عالقة في المسار: ' . ($sale->client?->name ?? '—'),
                "الصفقة في مرحلة «{$sale->stage}» منذ أكثر من {$days} أيام.",
                'high',
                'closing',
                now()->addDay(),
                $sale->client_id,
                $sale->id,
            );
            if ($task) {
                $count++;
            }
        }

        return $count;
    }

    public function markOverdueTasks(): int
    {
        return CrmTask::query()
            ->whereIn('status', [CrmTask::STATUS_PENDING, CrmTask::STATUS_ACCEPTED, CrmTask::STATUS_IN_PROGRESS])
            ->where('due_at', '<', now())
            ->update(['status' => CrmTask::STATUS_OVERDUE]);
    }

    public function sendDueReminders(): int
    {
        $minutes = config('crm_tasks.reminder_minutes_before', 60);
        $windowStart = now();
        $windowEnd = now()->addMinutes($minutes);
        $count = 0;

        $tasks = CrmTask::query()
            ->active()
            ->whereNull('reminder_sent_at')
            ->whereBetween('due_at', [$windowStart, $windowEnd])
            ->with('assignee')
            ->get();

        foreach ($tasks as $task) {
            \App\Services\CrmNotificationService::notifyTaskReminder($task);
            $task->update(['reminder_sent_at' => now()]);
            $count++;
        }

        return $count;
    }

    public function escalateOverdueToManagers(): int
    {
        $count = 0;
        $tasks = CrmTask::query()
            ->where('status', CrmTask::STATUS_OVERDUE)
            ->whereNull('escalated_at')
            ->with('assignee')
            ->limit(50)
            ->get();

        foreach ($tasks as $task) {
            $member = $task->assignee;
            if (!$member) {
                continue;
            }
            $teamId = DB::table('sales_team_members')->where('user_id', $member->id)->value('sales_team_id');
            $managerId = $teamId ? SalesTeam::whereKey($teamId)->value('manager_id') : null;
            $manager = $managerId ? User::find($managerId) : User::role(\App\Services\CrmEmployeeService::LEGACY_MANAGER_ROLES)->first();

            if ($manager) {
                \App\Services\CrmNotificationService::notifyTaskEscalation($task, $manager);
                $task->update(['escalated_at' => now()]);
                $count++;
            }
        }

        return $count;
    }
}

<?php

namespace App\Services\Crm;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\CrmTask;
use App\Models\User;
use App\Services\CrmScopeService;
use App\Services\Operations\OperationsLeadDistributionService;
use App\Services\Tasks\CrmTaskService;
use Illuminate\Support\Collection;

class ClientTransferBoardService
{
    public function __construct(
        protected OperationsLeadDistributionService $distribution,
        protected CrmTaskService $tasks,
    ) {}

    public function canAccess(User $user): bool
    {
        return $user->can('bulkUpdate', Client::class)
            || $user->can('transfer-clients')
            || $user->canAccessOperations();
    }

    /** @return array{columns: list<array<string, mixed>>, recent_logs: Collection, transfer_tasks_default: bool} */
    public function clientBoard(User $actor, ?string $search = null, int $perColumn = 40): array
    {
        $reps = $this->distribution->assignableReps($actor);
        $scope = CrmScopeService::for($actor);
        $base = $scope->clientsQuery()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            }))
            ->orderByDesc('updated_at');

        $columns = [];

        $unassigned = (clone $base)->whereNull('assigned_to')->limit($perColumn)->get();
        $columns[] = $this->clientColumn(
            'unassigned',
            null,
            null,
            'غير معيّن',
            $unassigned,
            (clone $base)->whereNull('assigned_to')->count(),
        );

        foreach ($reps as $rep) {
            $employeeId = $rep->employee?->id;
            if (! $employeeId) {
                continue;
            }

            $clients = (clone $base)->where('assigned_to', $employeeId)->limit($perColumn)->get();
            $columns[] = $this->clientColumn(
                'emp_' . $employeeId,
                (int) $employeeId,
                (int) $rep->id,
                $rep->name,
                $clients,
                (clone $base)->where('assigned_to', $employeeId)->count(),
            );
        }

        return [
            'columns' => $columns,
            'recent_logs' => $this->recentTransferLogs($actor),
            'transfer_tasks_default' => true,
        ];
    }

    /** @return array{columns: list<array<string, mixed>>} */
    public function taskBoard(User $actor, ?string $search = null, int $perColumn = 30): array
    {
        $reps = $this->distribution->assignableReps($actor);
        $base = $this->tasks->tasksQuery($actor)
            ->active()
            ->with(['client:id,name,phone', 'assignee:id,name'])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhereHas('client', fn ($c) => $c
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%'));
            }))
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->orderBy('due_at');

        $columns = [];

        foreach ($reps as $rep) {
            $tasks = (clone $base)->where('assigned_to', $rep->id)->limit($perColumn)->get();
            $columns[] = $this->taskColumn(
                'user_' . $rep->id,
                (int) $rep->id,
                $rep->name,
                $tasks,
                (clone $base)->where('assigned_to', $rep->id)->count(),
            );
        }

        return ['columns' => $columns];
    }

    /** @param  Collection<int, Client>  $clients */
    protected function clientColumn(
        string $key,
        ?int $employeeId,
        ?int $userId,
        string $name,
        Collection $clients,
        int $total,
    ): array {
        return [
            'key' => $key,
            'employee_id' => $employeeId,
            'user_id' => $userId,
            'name' => $name,
            'total' => $total,
            'shown' => $clients->count(),
            'clients' => $clients->map(fn (Client $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'stage' => $c->lead_stage,
                'stage_label' => CrmScopeService::leadStageLabels()[$c->lead_stage] ?? $c->lead_stage,
                'profile_url' => $c->profileUrl(),
            ])->values()->all(),
        ];
    }

    /** @param  Collection<int, CrmTask>  $tasks */
    protected function taskColumn(string $key, int $userId, string $name, Collection $tasks, int $total): array
    {
        return [
            'key' => $key,
            'user_id' => $userId,
            'name' => $name,
            'total' => $total,
            'shown' => $tasks->count(),
            'tasks' => $tasks->map(fn (CrmTask $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'priority' => $t->priority,
                'priority_label' => $t->priorityLabel(),
                'status_label' => $t->statusLabel(),
                'due_at' => $t->due_at?->format('Y/m/d H:i'),
                'client_name' => $t->client?->name,
                'client_phone' => $t->client?->phone,
                'show_url' => route('crm.tasks.show', $t),
            ])->values()->all(),
        ];
    }

    public function recentTransferLogs(User $actor, int $limit = 20): Collection
    {
        $scope = CrmScopeService::for($actor);
        $clientIds = $scope->clientsQuery()->pluck('id');

        if ($clientIds->isEmpty() && ! $actor->canAccessOperations()) {
            return collect();
        }

        return ActivityLog::query()
            ->with('user:id,name')
            ->whereIn('action', ['client_transferred', 'client_bulk_transferred'])
            ->where('model_type', Client::class)
            ->when(! $actor->canAccessOperations(), function ($q) use ($clientIds) {
                $q->where(function ($q) use ($clientIds) {
                    $q->whereIn('model_id', $clientIds);
                    foreach ($clientIds as $id) {
                        $q->orWhereJsonContains('new_values->client_ids', $id);
                    }
                });
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}

<?php

namespace App\Services\Crm;

use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use App\Services\CrmRoleResolver;
use App\Services\Operations\OperationsLeadDistributionService;
use App\Services\Tasks\CrmTaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ClientTransferService
{
    public function __construct(
        protected OperationsLeadDistributionService $distribution,
        protected CrmTaskService $tasks,
        protected ClientActivityService $activity,
    ) {}

    public function resolveSource(User $actor): string
    {
        if ($actor->canAccessOperations()) {
            return 'operations';
        }

        return CrmRoleResolver::for($actor)->isTeamLeader()
            ? 'crm_team_leader'
            : 'crm_manager';
    }

    /** @return array{client: Client, tasks_transferred: int} */
    public function transfer(
        Client $client,
        int $employeeId,
        User $actor,
        ?Request $request = null,
        bool $transferTasks = true,
        ?string $source = null,
    ): array {
        $source ??= $this->resolveSource($actor);
        $fromEmployee = $client->assignedEmployee;

        $client = $this->distribution->assignTo(
            $client,
            $employeeId,
            $actor,
            $source,
            $request,
            logActivity: false,
        );

        $toEmployee = Employee::findOrFail($employeeId);
        $tasksTransferred = [];

        if ($transferTasks && $toEmployee->user_id) {
            $tasksTransferred = $this->tasks->transferClientTasks(
                $actor,
                $client,
                $fromEmployee?->user_id ? (int) $fromEmployee->user_id : null,
                (int) $toEmployee->user_id,
            );
        }

        $this->activity->logTransfer(
            $client->fresh(['assignedEmployee']),
            $actor,
            $toEmployee,
            $fromEmployee,
            $source,
            $request,
            $tasksTransferred,
        );

        return [
            'client' => $client->fresh(['assignedEmployee']),
            'tasks_transferred' => count($tasksTransferred),
        ];
    }

    /** @return array{transferred: int, skipped: int, tasks_transferred: int} */
    public function transferMany(
        Collection $clients,
        int $employeeId,
        User $actor,
        ?Request $request = null,
        bool $transferTasks = true,
        ?string $source = null,
    ): array {
        $source ??= $this->resolveSource($actor);
        $transferred = 0;
        $skipped = 0;
        $totalTasks = 0;

        foreach ($clients as $client) {
            if (! $actor->can('transfer', $client)) {
                $skipped++;
                continue;
            }

            try {
                $result = $this->transfer($client, $employeeId, $actor, $request, $transferTasks, $source);
                $totalTasks += $result['tasks_transferred'];
                $transferred++;
            } catch (\Throwable) {
                $skipped++;
            }
        }

        return [
            'transferred' => $transferred,
            'skipped' => $skipped,
            'tasks_transferred' => $totalTasks,
        ];
    }
}

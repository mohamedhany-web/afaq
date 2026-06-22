<?php

namespace App\Services\Operations;

use App\Models\Client;
use App\Models\Employee;
use App\Models\User;
use App\Services\Crm\ClientTimelineService;
use App\Services\CrmEmployeeService;
use App\Services\CrmRoleResolver;
use App\Services\CrmScopeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OperationsLeadDistributionService
{
    public function unassignedLeadsQuery()
    {
        return Client::query()
            ->whereNull('assigned_to')
            ->whereIn('lead_stage', ['new', 'lead', 'prospect'])
            ->orderBy('created_at');
    }

    /** @return Collection<int, User> */
    public function assignableReps(?User $actor = null): Collection
    {
        if ($actor && !$actor->canAccessOperations()) {
            $scope = \App\Services\CrmScopeService::for($actor);
            if ($scope->isManagerScope()) {
                $userIds = $scope->managedTeamMemberUserIds();

                return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                    ->whereIn('id', $userIds)
                    ->whereHas('employee', fn ($q) => $q
                        ->where('department_id', CrmEmployeeService::salesDepartment()->id)
                        ->where('status', 'active'))
                    ->with('employee:id,user_id,first_name,last_name')
                    ->orderBy('name')
                    ->get();
            }

            return collect();
        }

        return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            ->whereHas('employee', fn ($q) => $q
                ->where('department_id', CrmEmployeeService::salesDepartment()->id)
                ->where('status', 'active'))
            ->with('employee:id,user_id,first_name,last_name')
            ->orderBy('name')
            ->get();
    }

    /** @return list<array{employee: Employee, load: int}> */
    public function repLoads(?User $actor = null): array
    {
        $repUserIds = $this->assignableReps($actor)->pluck('id');

        return Employee::query()
            ->where('department_id', CrmEmployeeService::salesDepartment()->id)
            ->where('status', 'active')
            ->whereIn('user_id', $repUserIds)
            ->with('user:id,name')
            ->withCount(['clients as active_leads_count' => fn ($q) => $q
                ->whereIn('lead_stage', ['new', 'lead', 'prospect', 'proposal', 'negotiation'])])
            ->orderBy('active_leads_count')
            ->get()
            ->map(fn (Employee $e) => [
                'employee' => $e,
                'load' => (int) $e->active_leads_count,
            ])
            ->all();
    }

    /** توزيع عميل واحد على مندوب */
    public function assignTo(
        Client $client,
        int $employeeId,
        User $actor,
        string $source = 'operations',
        ?\Illuminate\Http\Request $request = null,
        bool $logActivity = true,
    ): Client {
        $allowedIds = $this->assignableReps($actor)->pluck('employee.id')->filter()->map(fn ($id) => (int) $id);

        if ($allowedIds->isNotEmpty() && !$allowedIds->contains($employeeId)) {
            abort(403, 'لا يمكن تعيين العميل لهذا المندوب.');
        }

        $employee = Employee::query()
            ->where('id', $employeeId)
            ->where('department_id', CrmEmployeeService::salesDepartment()->id)
            ->where('status', 'active')
            ->firstOrFail();

        $assignmentLabel = match ($source) {
            'crm_manager' => 'ترحيل من مدير المبيعات',
            'crm_team_leader' => 'ترحيل من قائد الفريق',
            default => 'ترحيل من مدير العمليات',
        };

        return DB::transaction(function () use ($client, $employee, $actor, $source, $assignmentLabel, $request, $logActivity) {
            $fromEmployee = $client->assignedEmployee;
            $updates = ['assigned_to' => $employee->id];

            if ($client->lead_stage === CrmScopeService::LEAD_STAGE_NEW) {
                $updates['lead_stage'] = 'lead';
            }

            $client->update($updates);

            app(ClientTimelineService::class)->record(
                $client,
                'assigned',
                $assignmentLabel,
                'تم تعيين العميل إلى ' . trim($employee->first_name . ' ' . $employee->last_name),
                $actor,
                $source,
                Employee::class,
                $employee->id,
                ['assigned_to' => $employee->id],
            );

            if ($logActivity) {
                app(\App\Services\Crm\ClientActivityService::class)->logTransfer(
                    $client->fresh(['assignedEmployee']),
                    $actor,
                    $employee,
                    $fromEmployee,
                    $source,
                    $request ?? null,
                );
            }

            return $client->fresh(['assignedEmployee']);
        });
    }

    /**
     * توزيع تلقائي — يختار المندوب الأقل حملاً.
     *
     * @return array{assigned: int, skipped: int}
     */
    public function distributeBatch(array $clientIds, User $actor, ?int $preferredEmployeeId = null, ?\Illuminate\Http\Request $request = null): array
    {
        $reps = $this->assignableReps($actor);
        if ($reps->isEmpty()) {
            return ['assigned' => 0, 'skipped' => count($clientIds)];
        }

        $loads = Employee::query()
            ->whereIn('user_id', $reps->pluck('id'))
            ->withCount(['clients as active_leads_count' => fn ($q) => $q
                ->whereIn('lead_stage', ['new', 'lead', 'prospect', 'proposal', 'negotiation'])])
            ->get()
            ->keyBy('id');

        $assigned = 0;
        $skipped = 0;

        foreach ($clientIds as $clientId) {
            $client = $this->unassignedLeadsQuery()->where('id', $clientId)->first();
            if (!$client) {
                $skipped++;
                continue;
            }

            $employeeId = $preferredEmployeeId;
            if (!$employeeId) {
                $employeeId = $loads->sortBy('active_leads_count')->first()?->id;
            }

            if (!$employeeId) {
                $skipped++;
                continue;
            }

            $this->assignTo($client, $employeeId, $actor, $this->assignmentSource($actor), $request);
            if ($loads->has($employeeId)) {
                $loads[$employeeId]->active_leads_count++;
            }
            $assigned++;
        }

        return compact('assigned', 'skipped');
    }

    protected function assignmentSource(User $actor): string
    {
        if ($actor->canAccessOperations()) {
            return 'operations';
        }

        return \App\Services\CrmRoleResolver::for($actor)->isTeamLeader()
            ? 'crm_team_leader'
            : 'crm_manager';
    }
}

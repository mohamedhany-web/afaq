<?php

namespace App\Services\Tasks;

use App\Models\Client;
use App\Models\CrmTask;
use App\Models\CrmTaskLog;
use App\Models\User;
use App\Services\CrmNotificationService;
use App\Services\CrmRoleResolver;
use App\Services\CrmScopeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CrmTaskService
{
    public function __construct(
        protected CrmTaskScoringService $scoring,
    ) {}

    public function tasksQuery(User $actor): Builder
    {
        $scope = CrmScopeService::for($actor);
        $query = CrmTask::query()->with(['assignee:id,name', 'assigner:id,name', 'client:id,name,phone', 'sale:id,client_id', 'project:id,name']);

        if ($scope->hasFullAccess()) {
            return $query;
        }

        if ($scope->isManagerScope()) {
            $ids = $scope->managedTeamMemberUserIds();

            return $query->where(function ($q) use ($ids, $actor) {
                $q->whereIn('assigned_to', $ids)
                    ->orWhere('assigned_by', $actor->id);
            });
        }

        return $query->where('assigned_to', $actor->id);
    }

    public function canView(User $actor, CrmTask $task): bool
    {
        return $this->tasksQuery($actor)->whereKey($task->id)->exists();
    }

    public function canAssignTo(User $actor, int $targetUserId): bool
    {
        $scope = CrmScopeService::for($actor);

        if ($scope->hasFullAccess()) {
            return User::whereKey($targetUserId)->exists();
        }

        if ($scope->isManagerScope()) {
            return in_array($targetUserId, $scope->managedTeamMemberUserIds(), true)
                && $targetUserId !== $actor->id;
        }

        return false;
    }

    public function canTransfer(User $actor, CrmTask $task): bool
    {
        if (! $this->canView($actor, $task)) {
            return false;
        }

        if ($actor->can('transfer-tasks') || $this->assignableUsers($actor)->isNotEmpty()) {
            return true;
        }

        return false;
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    public function assignableUsers(User $actor)
    {
        $scope = CrmScopeService::for($actor);

        if ($scope->hasFullAccess()) {
            return User::role(array_merge(
                \App\Services\CrmEmployeeService::LEGACY_MANAGER_ROLES,
                \App\Services\CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
            ))->orderBy('name')->get(['id', 'name']);
        }

        if ($scope->isManagerScope()) {
            $ids = collect($scope->managedTeamMemberUserIds())
                ->reject(fn ($id) => (int) $id === (int) $actor->id);

            return User::whereIn('id', $ids)->orderBy('name')->get(['id', 'name']);
        }

        return collect();
    }

    public function assignerTypeFor(User $actor): string
    {
        if ($actor->hasRole(['super_admin', 'admin'])) {
            return 'admin';
        }

        return 'manager';
    }

    public function create(User $actor, array $data): CrmTask
    {
        if (!$this->canAssignTo($actor, (int) $data['assigned_to'])) {
            throw ValidationException::withMessages(['assigned_to' => 'لا يمكنك تعيين مهمة لهذا المستخدم.']);
        }

        $this->guardOverload((int) $data['assigned_to']);

        $requiresAcceptance = !empty($data['requires_acceptance']);
        $status = $requiresAcceptance ? CrmTask::STATUS_PENDING : CrmTask::STATUS_ACCEPTED;

        return DB::transaction(function () use ($actor, $data, $requiresAcceptance, $status) {
            $task = CrmTask::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'assigned_to' => $data['assigned_to'],
                'assigned_by' => $actor->id,
                'assigner_type' => $this->assignerTypeFor($actor),
                'priority' => $data['priority'],
                'status' => $status,
                'category' => $data['category'],
                'client_id' => $data['client_id'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'sale_id' => $data['sale_id'] ?? null,
                'due_at' => $data['due_at'],
                'requires_acceptance' => $requiresAcceptance,
                'auto_generated' => false,
                'sales_team_id' => $data['sales_team_id'] ?? null,
                'accepted_at' => $requiresAcceptance ? null : now(),
            ]);

            $this->log($task, $actor, 'created', null, $task->status, 'تم إنشاء المهمة');

            CrmNotificationService::notifyTaskAssigned($task);

            return $task;
        });
    }

    public function update(User $actor, CrmTask $task, array $data): CrmTask
    {
        if (!$this->canView($actor, $task) || !$this->canAssignTo($actor, (int) ($data['assigned_to'] ?? $task->assigned_to))) {
            throw ValidationException::withMessages(['task' => 'غير مصرح بتعديل هذه المهمة.']);
        }

        if (in_array($task->status, [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_VERIFIED, CrmTask::STATUS_ARCHIVED], true)) {
            throw ValidationException::withMessages(['task' => 'لا يمكن تعديل مهمة مكتملة أو مؤرشفة.']);
        }

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? $task->assigned_to,
            'priority' => $data['priority'],
            'category' => $data['category'],
            'client_id' => $data['client_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'sale_id' => $data['sale_id'] ?? null,
            'due_at' => $data['due_at'],
        ]);

        $this->log($task, $actor, 'updated', null, $task->status);

        return $task->fresh();
    }

    public function transfer(User $actor, CrmTask $task, int $targetUserId): CrmTask
    {
        if (! $this->canTransfer($actor, $task)) {
            abort(403, 'غير مصرح بتحويل هذه المهمة.');
        }

        if (! $this->canAssignTo($actor, $targetUserId)) {
            throw ValidationException::withMessages(['assigned_to' => 'لا يمكنك تحويل المهمة لهذا المستخدم.']);
        }

        return $this->performTransfer($actor, $task, $targetUserId, enforceOverloadGuard: true);
    }

    /**
     * تحويل المهام النشطة المرتبطة بعميل عند سحب/تحويل العميل بين السيلز.
     *
     * @return list<array{id: int, title: string}>
     */
    public function transferClientTasks(User $actor, Client $client, ?int $fromUserId, int $toUserId): array
    {
        if ($fromUserId === $toUserId) {
            return [];
        }

        if (! $this->canAssignTo($actor, $toUserId)) {
            return [];
        }

        $query = CrmTask::query()
            ->where('client_id', $client->id)
            ->whereIn('status', config('crm_tasks.active_statuses', []))
            ->where('assigned_to', '!=', $toUserId);

        if ($fromUserId) {
            $query->where('assigned_to', $fromUserId);
        }

        $transferred = [];

        foreach ($query->get() as $task) {
            try {
                $this->performTransfer($actor, $task, $toUserId, enforceOverloadGuard: false);
                $transferred[] = ['id' => $task->id, 'title' => $task->title];
            } catch (\Throwable) {
                continue;
            }
        }

        return $transferred;
    }

    protected function performTransfer(User $actor, CrmTask $task, int $targetUserId, bool $enforceOverloadGuard = true): CrmTask
    {
        if ((int) $task->assigned_to === $targetUserId) {
            throw ValidationException::withMessages(['assigned_to' => 'المهمة مُعيَّنة بالفعل لهذا المستخدم.']);
        }

        if (in_array($task->status, [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_VERIFIED, CrmTask::STATUS_ARCHIVED], true)) {
            throw ValidationException::withMessages(['task' => 'لا يمكن تحويل مهمة مكتملة أو مؤرشفة.']);
        }

        if ($enforceOverloadGuard) {
            $this->guardOverload($targetUserId);
        }

        return DB::transaction(function () use ($actor, $task, $targetUserId) {
            $fromName = $task->assignee?->name ?? '—';
            $toUser = User::findOrFail($targetUserId);

            $task->update([
                'assigned_to' => $targetUserId,
                'status' => $task->requires_acceptance ? CrmTask::STATUS_PENDING : CrmTask::STATUS_ACCEPTED,
                'accepted_at' => $task->requires_acceptance ? null : now(),
            ]);

            $this->log(
                $task,
                $actor,
                'transferred',
                null,
                $task->status,
                'تحويل من ' . $fromName . ' إلى ' . $toUser->name . ' (مع تحويل العميل)',
            );

            CrmNotificationService::notifyTaskAssigned($task->fresh());

            return $task->fresh(['assignee', 'assigner']);
        });
    }

    public function accept(User $actor, CrmTask $task): CrmTask
    {
        $this->assertAssignee($actor, $task);

        if ($task->status !== CrmTask::STATUS_PENDING) {
            throw ValidationException::withMessages(['status' => 'المهمة غير قابلة للقبول.']);
        }

        $task->update(['status' => CrmTask::STATUS_ACCEPTED, 'accepted_at' => now()]);
        $this->log($task, $actor, 'accepted', CrmTask::STATUS_PENDING, CrmTask::STATUS_ACCEPTED);

        return $task;
    }

    public function start(User $actor, CrmTask $task): CrmTask
    {
        $this->assertAssignee($actor, $task);

        if (!in_array($task->status, [CrmTask::STATUS_PENDING, CrmTask::STATUS_ACCEPTED, CrmTask::STATUS_OVERDUE], true)) {
            throw ValidationException::withMessages(['status' => 'لا يمكن بدء هذه المهمة.']);
        }

        $old = $task->status;
        $task->update([
            'status' => CrmTask::STATUS_IN_PROGRESS,
            'started_at' => $task->started_at ?? now(),
            'accepted_at' => $task->accepted_at ?? now(),
        ]);
        $this->log($task, $actor, 'started', $old, CrmTask::STATUS_IN_PROGRESS);

        return $task;
    }

    public function complete(User $actor, CrmTask $task, string $notes): CrmTask
    {
        $this->assertAssignee($actor, $task);

        if (trim($notes) === '') {
            throw ValidationException::withMessages(['completion_notes' => 'ملاحظات الإنجاز إلزامية.']);
        }

        if (!in_array($task->status, [CrmTask::STATUS_IN_PROGRESS, CrmTask::STATUS_ACCEPTED, CrmTask::STATUS_OVERDUE, CrmTask::STATUS_PENDING], true)) {
            throw ValidationException::withMessages(['status' => 'لا يمكن إكمال هذه المهمة.']);
        }

        $old = $task->status;
        $score = $this->scoring->score($task);
        $task->update([
            'status' => CrmTask::STATUS_COMPLETED,
            'completed_at' => now(),
            'completion_notes' => $notes,
            'performance_score' => $score,
        ]);
        $this->log($task, $actor, 'completed', $old, CrmTask::STATUS_COMPLETED, $notes);

        if ($task->assigner) {
            CrmNotificationService::notifyTaskCompleted($task);
        }

        return $task;
    }

    public function verify(User $actor, CrmTask $task, ?string $notes = null): CrmTask
    {
        $scope = CrmScopeService::for($actor);
        if (!$scope->isManagerScope() && !$scope->hasFullAccess()) {
            abort(403);
        }

        if ($task->status !== CrmTask::STATUS_COMPLETED) {
            throw ValidationException::withMessages(['status' => 'يجب إكمال المهمة أولاً.']);
        }

        $task->update([
            'status' => CrmTask::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => $actor->id,
        ]);
        $this->log($task, $actor, 'verified', CrmTask::STATUS_COMPLETED, CrmTask::STATUS_VERIFIED, $notes);

        return $task;
    }

    public function cancel(User $actor, CrmTask $task, ?string $reason = null): CrmTask
    {
        if (!$this->canView($actor, $task)) {
            abort(403);
        }

        $scope = CrmScopeService::for($actor);
        if ((int) $task->assigned_to !== (int) $actor->id && !$scope->isManagerScope() && !$scope->hasFullAccess()) {
            abort(403);
        }

        $old = $task->status;
        $task->update(['status' => CrmTask::STATUS_CANCELLED]);
        $this->log($task, $actor, 'cancelled', $old, CrmTask::STATUS_CANCELLED, $reason);

        return $task;
    }

    protected function assertAssignee(User $actor, CrmTask $task): void
    {
        if ((int) $task->assigned_to !== (int) $actor->id) {
            abort(403, 'هذه المهمة ليست مخصصة لك.');
        }
    }

    protected function guardOverload(int $userId): void
    {
        $max = config('crm_tasks.max_open_tasks_per_user', 25);
        $open = CrmTask::query()->where('assigned_to', $userId)->active()->count();

        if ($open >= $max) {
            throw ValidationException::withMessages([
                'assigned_to' => "الموظف لديه {$open} مهمة نشطة (الحد الأقصى {$max}).",
            ]);
        }
    }

    public function log(CrmTask $task, ?User $user, string $action, ?string $old, ?string $new, ?string $notes = null): void
    {
        CrmTaskLog::create([
            'task_id' => $task->id,
            'user_id' => $user?->id,
            'action' => $action,
            'old_status' => $old,
            'new_status' => $new,
            'notes' => $notes,
        ]);
    }

    public function openCountForUser(int $userId): int
    {
        return CrmTask::query()->where('assigned_to', $userId)->active()->count();
    }

    public function isOverloaded(int $userId): bool
    {
        return $this->openCountForUser($userId) >= config('crm_tasks.overload_threshold', 12);
    }
}

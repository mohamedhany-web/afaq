<?php

namespace App\Services\Tasks;

use App\Models\CrmTask;
use App\Models\User;
use App\Services\CrmRoleResolver;
use App\Services\CrmScopeService;
use Illuminate\Support\Collection;

class CrmTaskDashboardService
{
    public function __construct(
        protected CrmTaskService $taskService,
    ) {}

    public function stats(User $actor): array
    {
        $base = $this->taskService->tasksQuery($actor);
        $active = config('crm_tasks.active_statuses', []);

        return [
            'total_active' => (clone $base)->whereIn('status', $active)->count(),
            'due_today' => (clone $base)->active()->dueToday()->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'completed_week' => (clone $base)->where('status', CrmTask::STATUS_COMPLETED)
                ->where('completed_at', '>=', now()->subDays(7))->count(),
            'critical' => (clone $base)->active()->where('priority', 'critical')->count(),
            'pending_verify' => (clone $base)->where('status', CrmTask::STATUS_COMPLETED)->count(),
        ];
    }

    public function teamProductivity(User $actor): Collection
    {
        $scope = CrmScopeService::for($actor);
        if (!$scope->isManagerScope() && !$scope->hasFullAccess()) {
            return collect();
        }

        $ids = $scope->hasFullAccess()
            ? User::role(\App\Services\CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)->pluck('id')
            : collect($scope->managedTeamMemberUserIds())->reject(fn ($id) => (int) $id === (int) $actor->id);

        return $ids->map(function ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return null;
            }
            $completed = CrmTask::query()
                ->where('assigned_to', $userId)
                ->where('status', CrmTask::STATUS_COMPLETED)
                ->where('completed_at', '>=', now()->subDays(7))
                ->count();
            $overdue = CrmTask::query()->where('assigned_to', $userId)->overdue()->count();
            $open = CrmTask::query()->where('assigned_to', $userId)->active()->count();
            $avgScore = CrmTask::query()
                ->where('assigned_to', $userId)
                ->whereNotNull('performance_score')
                ->where('completed_at', '>=', now()->subDays(30))
                ->avg('performance_score');

            return [
                'user' => $user,
                'completed_week' => $completed,
                'overdue' => $overdue,
                'open' => $open,
                'avg_score' => round($avgScore ?? 0, 1),
                'overloaded' => app(CrmTaskService::class)->isOverloaded($userId),
            ];
        })->filter()->sortByDesc('completed_week')->values();
    }

    public function adminOverview(User $actor): array
    {
        if (!CrmScopeService::for($actor)->hasFullAccess()) {
            return [];
        }

        $byManager = CrmTask::query()
            ->selectRaw('assigned_by, COUNT(*) as cnt')
            ->whereNotNull('assigned_by')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('assigned_by')
            ->pluck('cnt', 'assigned_by');

        return [
            'tasks_by_assigner' => $byManager,
            'company_completion_rate' => $this->completionRate(CrmTask::query()),
        ];
    }

    protected function completionRate($query): float
    {
        $total = (clone $query)->where('created_at', '>=', now()->subDays(30))->count();
        if ($total === 0) {
            return 0;
        }
        $done = (clone $query)->whereIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_VERIFIED])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return round(($done / $total) * 100, 1);
    }

    public function viewMode(User $actor): string
    {
        return CrmRoleResolver::for($actor)->workspace();
    }
}

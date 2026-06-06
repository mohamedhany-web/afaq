<?php

namespace App\Services\Crm;

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use App\Services\CrmScopeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesManagementIntelligenceService
{
    public function __construct(protected CrmScopeService $scope) {}

    public static function build(User $user): array
    {
        $scope = CrmScopeService::for($user);
        $service = new self($scope);

        return [
            'managers' => $service->managerMetrics(),
            'teams' => $service->teamMetrics(),
            'summary' => $service->orgSummary(),
        ];
    }

    protected function managerMetrics(): Collection
    {
        if ($this->scope->hasFullAccess()) {
            $teams = SalesTeam::with(['manager', 'members'])->get();
        } else {
            $teams = $this->scope->managedTeamsQuery()->with(['manager', 'members'])->get();
        }

        return $teams->map(function (SalesTeam $team) {
            $manager = $team->manager;
            $memberUserIds = $team->members->pluck('user_id')->push($manager?->id)->filter()->unique();

            return [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'manager_id' => $manager?->id,
                'manager_name' => $manager?->name ?? '—',
                'metrics' => $this->metricsForUsers($memberUserIds, $team->id),
            ];
        })->values();
    }

    protected function teamMetrics(): Collection
    {
        return $this->managerMetrics()->map(fn ($row) => [
            'team_id' => $row['team_id'],
            'team_name' => $row['team_name'],
            'manager_name' => $row['manager_name'],
            ...$row['metrics'],
        ]);
    }

    protected function orgSummary(): array
    {
        $sales = $this->scope->salesQuery();
        $clients = $this->scope->clientsQuery();
        $monthStart = Carbon::now()->startOfMonth();

        $followUps = CrmFollowUp::query()
            ->whereIn('client_id', (clone $clients)->pluck('id'))
            ->where('scheduled_at', '>=', $monthStart);

        $completed = (clone $followUps)->where('status', 'completed')->count();
        $scheduled = (clone $followUps)->count();

        $viewings = (clone $sales)
            ->whereNotNull('viewing_date')
            ->where('viewing_date', '>=', $monthStart)
            ->count();

        $closed = (clone $sales)->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)
            ->count();

        $lost = (clone $sales)->where('stage', 'closed_lost')
            ->where('lost_at', '>=', $monthStart)
            ->count();

        $closedTotal = $closed + $lost;

        return [
            'follow_up_rate' => $scheduled > 0 ? round(($completed / $scheduled) * 100, 1) : 0,
            'viewings_month' => $viewings,
            'close_rate_month' => $closedTotal > 0 ? round(($closed / $closedTotal) * 100, 1) : 0,
            'avg_response_hours' => $this->averageResponseHours(),
        ];
    }

    protected function metricsForUsers(Collection $userIds, ?int $teamId = null): array
    {
        $employeeIds = Employee::whereIn('user_id', $userIds)->pluck('id');

        $salesQuery = Sale::query();
        if ($teamId) {
            $salesQuery->where('sales_team_id', $teamId);
        } else {
            $salesQuery->whereIn('assigned_to', $userIds);
        }

        $clientsQuery = Client::query()->whereIn('assigned_to', $employeeIds);

        $monthStart = Carbon::now()->startOfMonth();
        $activeStages = ['lead', 'prospect', 'proposal', 'negotiation'];

        $pipeline = (float) (clone $salesQuery)
            ->whereIn('stage', $activeStages)
            ->sum('estimated_value');

        $won = (int) (clone $salesQuery)
            ->where('stage', 'closed_won')
            ->where('actual_close_date', '>=', $monthStart)
            ->count();

        $lost = (int) (clone $salesQuery)
            ->where('stage', 'closed_lost')
            ->where(function ($q) use ($monthStart) {
                $q->where('lost_at', '>=', $monthStart)
                    ->orWhere('updated_at', '>=', $monthStart);
            })
            ->count();

        $closed = $won + $lost;

        $viewings = (int) (clone $salesQuery)
            ->whereNotNull('viewing_date')
            ->where('viewing_date', '>=', $monthStart)
            ->count();

        $clientIds = (clone $clientsQuery)->pluck('id');
        $followUps = CrmFollowUp::whereIn('client_id', $clientIds)
            ->where('scheduled_at', '>=', $monthStart);
        $followUpRate = (clone $followUps)->count() > 0
            ? round(((clone $followUps)->where('status', 'completed')->count() / (clone $followUps)->count()) * 100, 1)
            : 0;

        return [
            'pipeline_value' => $pipeline,
            'won_month' => $won,
            'lost_month' => $lost,
            'close_rate' => $closed > 0 ? round(($won / $closed) * 100, 1) : 0,
            'viewings_month' => $viewings,
            'follow_up_rate' => $followUpRate,
            'active_clients' => (int) (clone $clientsQuery)->whereIn('lead_stage', $activeStages)->count(),
            'avg_response_hours' => $this->averageResponseHoursForUsers($userIds),
        ];
    }

    protected function averageResponseHours(): float
    {
        $clientIds = $this->scope->clientsQuery()->pluck('id');

        return $this->averageResponseHoursForClientIds($clientIds);
    }

    protected function averageResponseHoursForUsers(Collection $userIds): float
    {
        $employeeIds = Employee::whereIn('user_id', $userIds)->pluck('id');
        $clientIds = Client::whereIn('assigned_to', $employeeIds)->pluck('id');

        return $this->averageResponseHoursForClientIds($clientIds);
    }

    protected function averageResponseHoursForClientIds(Collection $clientIds): float
    {
        if ($clientIds->isEmpty()) {
            return 0;
        }

        $pairs = Sale::whereIn('client_id', $clientIds)
            ->select('client_id', DB::raw('MIN(created_at) as first_sale'))
            ->groupBy('client_id')
            ->get();

        $clients = Client::whereIn('id', $pairs->pluck('client_id'))->get()->keyBy('id');
        $hours = 0;
        $n = 0;

        foreach ($pairs as $row) {
            $client = $clients->get($row->client_id);
            if (!$client || !$row->first_sale) {
                continue;
            }
            $hours += $client->created_at->diffInMinutes(Carbon::parse($row->first_sale)) / 60;
            $n++;
        }

        return $n > 0 ? round($hours / $n, 1) : 0;
    }
}

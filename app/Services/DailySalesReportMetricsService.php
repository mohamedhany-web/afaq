<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Contract;
use App\Models\CrmFollowUp;
use App\Models\Sale;
use App\Models\User;
use App\Services\WorkDayService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DailySalesReportMetricsService
{
    public function __construct(
        protected WorkDayService $workDay,
    ) {}
    public function build(User $user, Carbon|string $date): array
    {
        $day = Carbon::parse($date)->startOfDay();
        $start = $day->copy();
        $end = $day->copy()->endOfDay();
        $tomorrowStart = $day->copy()->addDay()->startOfDay();
        $tomorrowEnd = $day->copy()->addDay()->endOfDay();

        $clientScope = $this->clientsForUser($user);
        $followUps = fn () => CrmFollowUp::query()->where('user_id', $user->id);
        $sales = fn () => Sale::query()->where('assigned_to', $user->id);

        return [
            'generated_at' => now()->toIso8601String(),
            'work_day' => $this->workDay->workDayMetricsForReport($user, $day),
            'lead_summary' => [
                'new_leads_received' => (clone $clientScope)
                    ->where('created_by', $user->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'leads_contacted' => $followUps()
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhereBetween('completed_at', [$start, $end]);
                    })
                    ->whereIn('interaction_type', ['call', 'meeting', 'follow_up', 'note'])
                    ->distinct('client_id')
                    ->count('client_id'),
                'qualified_leads' => (clone $clientScope)
                    ->whereIn('lead_stage', ['prospect', 'proposal', 'negotiation'])
                    ->whereBetween('updated_at', [$start, $end])
                    ->count(),
                'unqualified_leads' => (clone $clientScope)
                    ->where('lead_stage', 'closed_lost')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count(),
            ],
            'communication' => [
                'calls_made' => $followUps()
                    ->where('interaction_type', 'call')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhereBetween('completed_at', [$start, $end]);
                    })
                    ->count(),
                'calls_answered' => $followUps()
                    ->where('interaction_type', 'call')
                    ->where('status', CrmFollowUp::STATUS_COMPLETED)
                    ->whereBetween('completed_at', [$start, $end])
                    ->count(),
                'whatsapp_conversations' => $followUps()
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhereBetween('completed_at', [$start, $end]);
                    })
                    ->where(function ($q) {
                        $q->where('notes', 'like', '%واتس%')
                            ->orWhere('notes', 'like', '%whatsapp%')
                            ->orWhere('notes', 'like', '%WhatsApp%');
                    })
                    ->count(),
                'emails_sent' => $followUps()
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhereBetween('completed_at', [$start, $end]);
                    })
                    ->where(function ($q) {
                        $q->where('notes', 'like', '%بريد%')
                            ->orWhere('notes', 'like', '%email%')
                            ->orWhere('notes', 'like', '%إيميل%');
                    })
                    ->count(),
            ],
            'meetings_visits' => [
                'meetings_scheduled' => $followUps()
                    ->where('interaction_type', 'meeting')
                    ->whereBetween('scheduled_at', [$start, $end])
                    ->count(),
                'meetings_completed' => $followUps()
                    ->where('interaction_type', 'meeting')
                    ->where('status', CrmFollowUp::STATUS_COMPLETED)
                    ->whereBetween('completed_at', [$start, $end])
                    ->count(),
                'property_visits_conducted' => $followUps()
                    ->where('interaction_type', 'viewing')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('scheduled_at', [$start, $end])
                            ->orWhereBetween('completed_at', [$start, $end]);
                    })
                    ->count(),
            ],
            'pipeline_progress' => [
                'leads_to_qualified' => (clone $clientScope)
                    ->where('lead_stage', 'prospect')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count(),
                'leads_to_negotiation' => (clone $clientScope)
                    ->where('lead_stage', 'negotiation')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count(),
                'proposals_sent' => (clone $clientScope)
                    ->where('lead_stage', 'proposal')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count()
                    + $sales()
                        ->where('stage', 'proposal')
                        ->whereBetween('updated_at', [$start, $end])
                        ->count(),
                'contracts_sent' => Contract::query()
                    ->where('created_by', $user->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
            ],
            'deals' => [
                'deals_closed_won' => $sales()
                    ->where('stage', 'closed_won')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('actual_close_date', [$start->toDateString(), $end->toDateString()])
                            ->orWhereBetween('updated_at', [$start, $end]);
                    })
                    ->count(),
                'deals_closed_lost' => $sales()
                    ->where('stage', 'closed_lost')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('actual_close_date', [$start->toDateString(), $end->toDateString()])
                            ->orWhereBetween('updated_at', [$start, $end]);
                    })
                    ->count(),
                'expected_revenue_new_opportunities' => (float) $sales()
                    ->whereBetween('created_at', [$start, $end])
                    ->whereNotIn('stage', ['closed_won', 'closed_lost'])
                    ->sum('estimated_value'),
            ],
            'follow_ups' => [
                'follow_ups_completed' => $followUps()
                    ->where('status', CrmFollowUp::STATUS_COMPLETED)
                    ->whereBetween('completed_at', [$start, $end])
                    ->count(),
                'overdue_follow_ups' => $followUps()
                    ->where('status', CrmFollowUp::STATUS_SCHEDULED)
                    ->where('scheduled_at', '<', $end)
                    ->where(function ($q) use ($end) {
                        $q->whereNull('completed_at')
                            ->orWhere('completed_at', '>', $end);
                    })
                    ->count(),
                'follow_ups_scheduled_tomorrow' => $followUps()
                    ->where('status', CrmFollowUp::STATUS_SCHEDULED)
                    ->whereBetween('scheduled_at', [$tomorrowStart, $tomorrowEnd])
                    ->count(),
            ],
        ];
    }

    protected function clientsForUser(User $user): Builder
    {
        $employeeId = $user->employee?->id;

        return Client::query()->where(function ($q) use ($user, $employeeId) {
            $q->where('created_by', $user->id);

            if ($employeeId) {
                $q->orWhere('assigned_to', $employeeId);
            }

            $q->orWhereHas('sales', fn ($s) => $s->where('assigned_to', $user->id));
        });
    }
}

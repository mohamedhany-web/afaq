<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\User;
use App\Services\Crm\CrmFilterService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FollowUpDashboardService
{
    public const BUCKETS = [
        'today' => 'اليوم',
        'overdue' => 'متأخرة',
        'upcoming' => 'قادمة',
        'completed' => 'مكتملة',
        'cancelled' => 'ملغاة',
        'all' => 'الكل',
    ];

    public function __construct(
        protected CrmFollowUpService $followUps,
        protected CrmFilterService $filters,
    ) {}

    public static function for(User $user): self
    {
        return new self(
            CrmFollowUpService::for($user),
            CrmFilterService::for($user),
        );
    }

    public function isEnhancedWorkspace(User $user, string $workspace): bool
    {
        return $workspace === 'operations'
            || ($workspace === 'admin' && $user->hasRole(['super_admin', 'admin']));
    }

    /** @return array<string, mixed> */
    public function buildIndex(Request $request, User $user, string $workspace): array
    {
        $enhanced = $this->isEnhancedWorkspace($user, $workspace);
        $scope = $this->filters->scope();
        $base = $this->followUps->followUpsQuery()
            ->with(['client:id,name,phone,status,lead_stage,assigned_to', 'client.assignedEmployee:id,first_name,last_name', 'user:id,name', 'creator:id,name', 'sale:id,product_service']);

        $date = $request->filled('date') ? Carbon::parse($request->date) : now();
        $view = $request->get('view', $enhanced && $request->filled('date_from') ? 'range' : 'day');
        $bucket = $request->get('bucket', $request->get('filter'));

        $filtered = $this->filters->applyFollowUpFilters(clone $base, $request);
        $listed = $this->applyListingScope(clone $filtered, $request, $date, $view, $bucket, $enhanced);

        $stats = $this->buildStats(clone $base, $request);
        $clientBreakdown = $enhanced ? $this->clientBreakdown(clone $filtered) : collect();

        $assignableUsers = collect($this->followUps->assignableUsers($user));
        $filterKeys = $this->filters->followUpFilterKeys($enhanced);
        $routes = $this->routesFor($workspace);

        return [
            'followUps' => $listed->orderBy('scheduled_at')->paginate($enhanced ? 40 : 30)->withQueryString(),
            'stats' => $stats,
            'date' => $date,
            'view' => $view,
            'bucket' => $bucket,
            'assignableUsers' => $assignableUsers,
            'typeLabels' => CrmFollowUp::TYPE_LABELS,
            'canAssignOthers' => $assignableUsers->count() > 1,
            'isManager' => $scope->isManagerScope() || $scope->hasFullAccess() || $user->canAccessOperations(),
            'highlight' => $request->integer('highlight'),
            'enhanced' => $enhanced,
            'workspace' => $workspace,
            'clientBreakdown' => $clientBreakdown,
            'buckets' => self::BUCKETS,
            'routes' => $routes,
            'clearUrl' => $routes['index'],
            'filterKeys' => $filterKeys,
            'advancedKeys' => $enhanced
                ? ['type', 'client_status', 'client_lead_stage', 'date_from', 'date_to', 'client_unassigned', 'overdue_only']
                : ['type'],
            'hasActive' => $this->filters->hasActiveFilters($request, $filterKeys),
            'salesReps' => $this->filters->salesReps(),
            'showSalesRepFilter' => $this->filters->showSalesRepFilter(),
            'dateValue' => $date->toDateString(),
            'statusOptions' => [
                'scheduled' => 'مجدولة',
                'completed' => 'مكتملة',
                'cancelled' => 'ملغاة',
            ],
            'clientStatusOptions' => [
                'prospect' => 'محتمل',
                'active' => 'نشط',
                'inactive' => 'غير نشط',
                'suspended' => 'موقوف',
            ],
            'stageLabels' => [
                'lead' => 'عميل محتمل',
                'prospect' => 'مهتم',
                'proposal' => 'عرض سعر',
                'negotiation' => 'تفاوض',
                'closed_won' => 'تم البيع',
                'closed_lost' => 'خسارة',
            ],
            'searchPlaceholder' => 'بحث: العميل، الهاتف، الملاحظات...',
        ];
    }

    protected function applyListingScope(
        Builder $query,
        Request $request,
        Carbon $date,
        string $view,
        ?string $bucket,
        bool $enhanced,
    ): Builder {
        if ($bucket && $bucket !== 'all') {
            return $this->applyBucket($query, $bucket);
        }

        if ($enhanced && $view === 'range' && ($request->filled('date_from') || $request->filled('date_to'))) {
            if ($request->filled('date_from')) {
                $query->whereDate('scheduled_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('scheduled_at', '<=', $request->date_to);
            }

            return $query;
        }

        if ($enhanced && $view === 'all') {
            return $query;
        }

        if ($view === 'week') {
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();

            return $query->whereBetween('scheduled_at', [$start, $end]);
        }

        return $query->whereDate('scheduled_at', $date->toDateString());
    }

    protected function applyBucket(Builder $query, string $bucket): Builder
    {
        return match ($bucket) {
            'today' => $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('status', CrmFollowUp::STATUS_SCHEDULED)
                        ->whereDate('scheduled_at', today());
                })->orWhere(function ($sub) {
                    $sub->where('status', CrmFollowUp::STATUS_COMPLETED)
                        ->whereDate('completed_at', today());
                });
            }),
            'overdue' => $query
                ->where('status', CrmFollowUp::STATUS_SCHEDULED)
                ->where('scheduled_at', '<', now()),
            'upcoming' => $query
                ->where('status', CrmFollowUp::STATUS_SCHEDULED)
                ->whereBetween('scheduled_at', [now(), now()->addDays(7)]),
            'completed' => $query->where('status', CrmFollowUp::STATUS_COMPLETED),
            'cancelled' => $query->where('status', CrmFollowUp::STATUS_CANCELLED),
            default => $query,
        };
    }

    /** @return array<string, int> */
    protected function buildStats(Builder $base, Request $request): array
    {
        $scoped = $this->filters->applyFollowUpFilters(clone $base, $request->duplicate());

        return [
            'today' => (clone $scoped)->where(function ($q) {
                $q->where(function ($s) {
                    $s->where('status', CrmFollowUp::STATUS_SCHEDULED)->whereDate('scheduled_at', today());
                })->orWhere(function ($s) {
                    $s->where('status', CrmFollowUp::STATUS_COMPLETED)->whereDate('completed_at', today());
                });
            })->count(),
            'overdue' => (clone $scoped)->where('status', CrmFollowUp::STATUS_SCHEDULED)->where('scheduled_at', '<', now())->count(),
            'upcoming' => (clone $scoped)->where('status', CrmFollowUp::STATUS_SCHEDULED)
                ->whereBetween('scheduled_at', [now(), now()->addDays(7)])->count(),
            'completed' => (clone $scoped)->where('status', CrmFollowUp::STATUS_COMPLETED)->count(),
            'cancelled' => (clone $scoped)->where('status', CrmFollowUp::STATUS_CANCELLED)->count(),
            'clients' => (clone $scoped)->distinct('client_id')->count('client_id'),
        ];
    }

    /** @return Collection<int, object> */
    protected function clientBreakdown(Builder $query): Collection
    {
        $now = now();
        $today = today()->toDateString();

        $rows = (clone $query)
            ->reorder()
            ->select('client_id')
            ->selectRaw("SUM(CASE WHEN status = 'scheduled' AND scheduled_at < ? THEN 1 ELSE 0 END) as overdue_cnt", [$now])
            ->selectRaw("SUM(CASE WHEN status = 'scheduled' AND DATE(scheduled_at) = ? THEN 1 ELSE 0 END) as today_cnt", [$today])
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_cnt")
            ->selectRaw('COUNT(*) as total_cnt')
            ->groupBy('client_id')
            ->orderByDesc('overdue_cnt')
            ->orderByDesc('today_cnt')
            ->limit(12)
            ->get();

        $clients = Client::query()
            ->whereIn('id', $rows->pluck('client_id'))
            ->get(['id', 'name', 'phone', 'status', 'lead_stage'])
            ->keyBy('id');

        return $rows->map(fn ($row) => (object) [
            'client' => $clients->get($row->client_id),
            'overdue_cnt' => (int) $row->overdue_cnt,
            'today_cnt' => (int) $row->today_cnt,
            'completed_cnt' => (int) $row->completed_cnt,
            'total_cnt' => (int) $row->total_cnt,
        ])->filter(fn ($row) => $row->client !== null)->values();
    }

    /** @return array<string, string> */
    protected function routesFor(string $workspace): array
    {
        if ($workspace === 'operations') {
            return [
                'index' => route('operations.follow-ups.index'),
                'store' => route('operations.follow-ups.store'),
                'complete' => 'operations.follow-ups.complete',
                'cancel' => 'operations.follow-ups.cancel',
            ];
        }

        return [
            'index' => route('crm.follow-ups.index'),
            'store' => route('crm.follow-ups.store'),
            'complete' => 'crm.follow-ups.complete',
            'cancel' => 'crm.follow-ups.cancel',
        ];
    }
}

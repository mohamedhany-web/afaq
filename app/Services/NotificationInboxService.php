<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NotificationInboxService
{
    public const CRM_TYPES = ['crm_follow_up', 'crm_reminder', 'crm_daily_report', 'crm_task'];

    public function stats(User $user): array
    {
        $ttl = config('notifications.stats_cache_seconds', 30);

        return Cache::remember("notifications.stats.{$user->id}", $ttl, function () use ($user) {
            $weekStart = now()->subDays(7)->startOfDay()->toDateTimeString();
            $crmIn = "'" . implode("','", self::CRM_TYPES) . "'";

            $row = Notification::query()
                ->where('user_id', $user->id)
                ->selectRaw("
                    COUNT(*) as total_count,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_count,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week_count,
                    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_count,
                    SUM(CASE WHEN type IN ({$crmIn}) THEN 1 ELSE 0 END) as crm_count,
                    SUM(CASE WHEN type = 'project' THEN 1 ELSE 0 END) as project_count,
                    SUM(CASE WHEN type = 'message' THEN 1 ELSE 0 END) as message_count,
                    SUM(CASE WHEN type = 'task' THEN 1 ELSE 0 END) as task_count
                ", [$weekStart])
                ->first();

            return [
                'total' => (int) ($row->total_count ?? 0),
                'unread' => (int) ($row->unread_count ?? 0),
                'today' => (int) ($row->today_count ?? 0),
                'week' => (int) ($row->week_count ?? 0),
                'read' => (int) ($row->read_count ?? 0),
                'crm' => (int) ($row->crm_count ?? 0),
                'project' => (int) ($row->project_count ?? 0),
                'message' => (int) ($row->message_count ?? 0),
                'task' => (int) ($row->task_count ?? 0),
            ];
        });
    }

    public function forgetStats(int $userId): void
    {
        Cache::forget("notifications.stats.{$userId}");
    }

    public function buildQuery(User $user, string $filter, ?string $search, bool $archive = false): Builder
    {
        $query = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        match ($filter) {
            'unread' => $query->where('is_read', false),
            'read' => $query->where('is_read', true),
            'today' => $query->whereDate('created_at', now()),
            'week' => $query->where('created_at', '>=', now()->subDays(7)->startOfDay()),
            'task' => $query->where('type', 'task'),
            'project' => $query->where('type', 'project'),
            'message' => $query->where('type', 'message'),
            'crm' => $query->whereIn('type', self::CRM_TYPES),
            'all' => $this->applyAllWindow($query, $archive),
            default => $query->where('is_read', false),
        };

        return $query;
    }

    protected function applyAllWindow(Builder $query, bool $archive): Builder
    {
        if ($archive) {
            return $query;
        }

        $days = config('notifications.list_max_days', 90);

        return $query->where('created_at', '>=', now()->subDays($days)->startOfDay());
    }

    public function paginate(User $user, string $filter, ?string $search, bool $archive = false): LengthAwarePaginator
    {
        $perPage = config('notifications.per_page', 50);

        return $this->buildQuery($user, $filter, $search, $archive)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function groupByDate(Collection $notifications): Collection
    {
        return $notifications->groupBy(function (Notification $n) {
            return $n->created_at->toDateString();
        })->map(function (Collection $items, string $date) {
            return [
                'label' => $this->dateGroupLabel($date),
                'date' => $date,
                'items' => $items,
            ];
        });
    }

    public function dateGroupLabel(string $date): string
    {
        $d = Carbon::parse($date)->startOfDay();

        if ($d->isToday()) {
            return 'اليوم';
        }
        if ($d->isYesterday()) {
            return 'أمس';
        }
        if ($d->greaterThanOrEqualTo(now()->subDays(7)->startOfDay())) {
            return $d->locale('ar')->translatedFormat('l j F');
        }

        return $d->locale('ar')->translatedFormat('j F Y');
    }

    public function pruneReadForUser(User $user, ?int $days = null): int
    {
        $days = $days ?? config('notifications.prune_read_after_days', 30);
        $cutoff = now()->subDays($days);

        $deleted = $user->notifications()
            ->where('is_read', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->forgetStats($user->id);

        return $deleted;
    }

    public function pruneReadGlobally(?int $days = null): int
    {
        $days = $days ?? config('notifications.prune_read_after_days', 30);
        $cutoff = now()->subDays($days);

        return Notification::query()
            ->where('is_read', true)
            ->where('created_at', '<', $cutoff)
            ->delete();
    }
}

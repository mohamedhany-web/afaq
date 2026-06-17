<?php

namespace App\Services\Operations;

use App\Models\CrmTask;
use Carbon\Carbon;

class OperationsWorkspaceService
{
    public function __construct(
        protected OperationsClientBucketService $buckets,
        protected OperationsDashboardMetricsService $metrics,
    ) {}

    /** @return array<int, array<string, mixed>> */
    public function dashboardSections(?Carbon $reference = null): array
    {
        $today = ($reference ?? now())->copy()->startOfDay();
        $yesterday = $today->copy()->subDay();
        $tomorrow = $today->copy()->addDay();

        return [
            $this->clientSection('all', OperationsClientBucketService::BUCKET_ALL, 'theme', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'),
            $this->clientSection('follow_up', OperationsClientBucketService::BUCKET_FOLLOW_UP, 'blue', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'),
            $this->clientSection('interested', OperationsClientBucketService::BUCKET_INTERESTED, 'purple', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'),
            $this->clientSection('cancelled', OperationsClientBucketService::BUCKET_CANCELLED, 'red', 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'),
            $this->clientSection('not_interested', OperationsClientBucketService::BUCKET_NOT_INTERESTED, 'amber', 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'),
            [
                'key' => 'overdue_tasks',
                'label' => __('operations.sections.overdue_tasks'),
                'count' => CrmTask::query()->overdue()->count(),
                'accent' => 'red',
                'icon' => 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'href' => route('crm.tasks.index', ['filter' => 'overdue']) . '#page-data',
                'linkLabel' => __('operations.actions.view_details'),
            ],
            [
                'key' => 'contracts',
                'label' => __('operations.sections.contracts'),
                'count' => $this->buckets->count(OperationsClientBucketService::BUCKET_CONTRACTS),
                'accent' => 'green',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'href' => route('operations.clients.index', ['bucket' => OperationsClientBucketService::BUCKET_CONTRACTS]) . '#page-data',
                'linkLabel' => __('operations.actions.view_details'),
            ],
            [
                'key' => 'tasks_today',
                'label' => __('operations.sections.tasks_today'),
                'count' => $this->tasksDueOn($today)->count(),
                'accent' => 'theme',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'href' => route('crm.tasks.index', ['filter' => 'today']) . '#page-data',
                'linkLabel' => __('operations.actions.view_details'),
            ],
            [
                'key' => 'tasks_tomorrow',
                'label' => __('operations.sections.tasks_tomorrow'),
                'count' => $this->tasksDueOn($tomorrow)->count(),
                'accent' => 'blue',
                'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'href' => route('crm.tasks.index', ['filter' => 'tomorrow']) . '#page-data',
                'linkLabel' => __('operations.actions.view_details'),
            ],
            [
                'key' => 'comments_today',
                'label' => __('operations.sections.comments_today'),
                'count' => $this->metrics->commentsCount($today),
                'accent' => 'theme',
                'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                'href' => route('operations.follow-ups.index', ['bucket' => 'today', 'date' => $today->toDateString()]) . '#page-data',
                'linkLabel' => __('operations.actions.view_details'),
            ],
            [
                'key' => 'comments_yesterday',
                'label' => __('operations.sections.comments_yesterday'),
                'count' => $this->metrics->commentsCount($yesterday),
                'accent' => 'purple',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'href' => route('operations.follow-ups.index', ['bucket' => 'completed', 'date' => $yesterday->toDateString()]) . '#page-data',
                'linkLabel' => __('operations.actions.view_details'),
            ],
        ];
    }

  /** @return array<string, mixed> */
    protected function clientSection(string $key, string $bucket, string $accent, string $icon): array
    {
        return [
            'key' => $key,
            'label' => __('operations.sections.' . $key),
            'count' => $this->buckets->count($bucket),
            'accent' => $accent,
            'icon' => $icon,
            'href' => route('operations.clients.index', ['bucket' => $bucket]) . '#page-data',
            'linkLabel' => __('operations.actions.view_details'),
        ];
    }

    protected function tasksDueOn(Carbon $day)
    {
        return CrmTask::query()
            ->whereDate('due_at', $day->toDateString())
            ->whereNotIn('status', [
                CrmTask::STATUS_COMPLETED,
                CrmTask::STATUS_VERIFIED,
                CrmTask::STATUS_CANCELLED,
                CrmTask::STATUS_ARCHIVED,
            ]);
    }
}

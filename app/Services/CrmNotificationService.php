<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class CrmNotificationService
{
    public static function notify(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $digestKey = null,
    ): Notification {
        if ($digestKey) {
            $existing = Notification::query()
                ->where('user_id', $userId)
                ->where('digest_key', $digestKey)
                ->where('is_read', false)
                ->first();

            if ($existing) {
                $count = (int) ($existing->data['count'] ?? 1) + 1;
                $existing->update([
                    'title' => $title,
                    'message' => $message,
                    'data' => array_merge($existing->data ?? [], $data, ['count' => $count]),
                ]);

                app(NotificationInboxService::class)->forgetStats($userId);

                return $existing->fresh();
            }
        }

        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'digest_key' => $digestKey,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);

        app(NotificationInboxService::class)->forgetStats($userId);

        return $notification;
    }

    /**
     * تذكير مجمّع: إشعار واحد لكل مستخدم بدل مئات الإشعارات المنفصلة.
     */
    public static function notifyFollowUpReminderBatch(User $user, Collection $followUps, string $kind): void
    {
        if ($followUps->isEmpty()) {
            return;
        }

        $count = $followUps->count();
        $digestKey = "crm_reminder:{$user->id}:{$kind}:" . now()->toDateString();

        $label = $kind === 'overdue' ? 'متأخرة' : 'قريبة';
        $first = $followUps->first();
        $url = route('crm.follow-ups.index', [
            'date' => $first->scheduled_at->toDateString(),
        ]);

        $sample = $followUps->take(3)->map(fn ($f) => $f->client?->name ?? '—')->implode('، ');
        $more = $count > 3 ? ' +' . ($count - 3) : '';

        self::notify(
            $user->id,
            'crm_reminder',
            "{$count} متابعات {$label}",
            "لديك {$count} موعد متابعة {$label}: {$sample}{$more}",
            [
                'url' => $url,
                'count' => $count,
                'kind' => $kind,
            ],
            $digestKey,
        );
    }

    public static function notifyFollowUpScheduled(User $assignee, array $payload): void
    {
        self::notify(
            $assignee->id,
            'crm_follow_up',
            'متابعة مجدولة',
            $payload['message'],
            $payload['data'],
        );
    }

    public static function notifyFollowUpReminder(User $assignee, array $payload): void
    {
        self::notify(
            $assignee->id,
            'crm_reminder',
            'تذكير بموعد متابعة',
            $payload['message'],
            $payload['data'],
        );
    }

    public static function notifyManagerOfTeamActivity(User $manager, array $payload): void
    {
        self::notify(
            $manager->id,
            'crm_follow_up',
            $payload['title'] ?? 'نشاط فريق المبيعات',
            $payload['message'],
            $payload['data'],
        );
    }

    public static function notifyTaskAssigned(\App\Models\CrmTask $task): void
    {
        self::notify(
            $task->assigned_to,
            'crm_task',
            'مهمة جديدة: ' . $task->title,
            $task->description ?? 'تم تعيين مهمة لك — الموعد: ' . $task->due_at->format('Y-m-d H:i'),
            [
                'url' => route('crm.tasks.show', $task),
                'task_id' => $task->id,
                'priority' => $task->priority,
            ],
            'crm_task:assign:' . $task->id,
        );
    }

    public static function notifyTaskReminder(\App\Models\CrmTask $task): void
    {
        self::notify(
            $task->assigned_to,
            'crm_task',
            'تذكير بمهمة: ' . $task->title,
            'الموعد النهائي: ' . $task->due_at->format('Y-m-d H:i'),
            ['url' => route('crm.tasks.show', $task), 'task_id' => $task->id],
            'crm_task:reminder:' . $task->id . ':' . now()->toDateString(),
        );
    }

    public static function notifyTaskCompleted(\App\Models\CrmTask $task): void
    {
        if (!$task->assigned_by) {
            return;
        }
        self::notify(
            $task->assigned_by,
            'crm_task',
            'اكتملت مهمة: ' . $task->title,
            $task->assignee?->name . ' — ' . mb_substr($task->completion_notes ?? '', 0, 120),
            ['url' => route('crm.tasks.show', $task), 'task_id' => $task->id],
        );
    }

    public static function notifyTaskEscalation(\App\Models\CrmTask $task, User $manager): void
    {
        self::notify(
            $manager->id,
            'crm_task',
            'تصعيد: مهمة متأخرة',
            ($task->assignee?->name ?? 'موظف') . ' — ' . $task->title,
            ['url' => route('crm.tasks.show', $task), 'task_id' => $task->id],
            'crm_task:escalate:' . $task->id,
        );
    }
}

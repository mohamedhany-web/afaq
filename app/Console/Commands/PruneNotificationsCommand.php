<?php

namespace App\Console\Commands;

use App\Services\NotificationInboxService;
use Illuminate\Console\Command;

class PruneNotificationsCommand extends Command
{
    protected $signature = 'notifications:prune-read {--days=}';

    protected $description = 'حذف الإشعارات المقروءة القديمة لتخفيف الحجم';

    public function handle(NotificationInboxService $inbox): int
    {
        $days = $this->option('days') ?: config('notifications.prune_read_after_days', 30);
        $deleted = $inbox->pruneReadGlobally((int) $days);
        $this->info("Deleted {$deleted} read notification(s) older than {$days} days.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\WorkDayService;
use Illuminate\Console\Command;

class AttendanceAutoCheckoutCommand extends Command
{
    protected $signature = 'attendance:auto-checkout';

    protected $description = 'إيقاف تلقائي لجلسات العمل عند انتهاء الساعات اليومية المطلوبة';

    public function handle(WorkDayService $workDay): int
    {
        $count = $workDay->processExpiredSessions();

        if ($count > 0) {
            $this->info("تم إيقاف {$count} جلسة عمل تلقائياً.");
        }

        return self::SUCCESS;
    }
}

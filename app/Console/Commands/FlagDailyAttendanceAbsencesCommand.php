<?php

namespace App\Console\Commands;

use App\Services\AttendanceAbsenceReviewService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FlagDailyAttendanceAbsencesCommand extends Command
{
    protected $signature = 'attendance:flag-absences {--date=}';

    protected $description = 'تسجيل غياب محتمل يومياً لمراجعة مدير العمليات';

    public function handle(AttendanceAbsenceReviewService $service): int
    {
        if ($this->option('date')) {
            $date = Carbon::parse($this->option('date'))->startOfDay();
            $flagged = $service->flagAbsencesForDate($date);
            $this->info("تاريخ {$date->toDateString()}: {$flagged} غياب جديد.");

            return self::SUCCESS;
        }

        if (now()->hour < 14) {
            $reviewDate = Carbon::yesterday()->startOfDay();
            $auto = $service->autoConfirmOverduePending($reviewDate);
            $this->info("تأكيد تلقائي لغياب {$reviewDate->toDateString()}: {$auto} سجل.");

            return self::SUCCESS;
        }

        $date = Carbon::today()->startOfDay();
        $flagged = $service->flagAbsencesForDate($date);
        $this->info("تاريخ {$date->toDateString()}: {$flagged} غياب محتمل للمراجعة.");

        return self::SUCCESS;
    }
}

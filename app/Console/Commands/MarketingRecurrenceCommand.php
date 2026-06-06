<?php

namespace App\Console\Commands;

use App\Services\MarketingRecurrenceService;
use Illuminate\Console\Command;

class MarketingRecurrenceCommand extends Command
{
    protected $signature = 'marketing:process-recurrence';

    protected $description = 'إنشاء مهام تسويق دورية مستحقة';

    public function handle(MarketingRecurrenceService $service): int
    {
        $count = $service->processDueRecurrences();
        $this->info("تم إنشاء {$count} مهمة تسويق دورية.");

        return self::SUCCESS;
    }
}

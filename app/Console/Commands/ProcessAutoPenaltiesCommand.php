<?php

namespace App\Console\Commands;

use App\Services\AutoPenaltyService;
use Illuminate\Console\Command;

class ProcessAutoPenaltiesCommand extends Command
{
    protected $signature = 'penalties:apply-overdue';

    protected $description = 'Apply automatic penalties for overdue tasks and missing reports';

    public function handle(AutoPenaltyService $penalties): int
    {
        $stats = $penalties->processAll();

        $this->info("Applied: {$stats['applied']}");
        $this->info("Skipped: {$stats['skipped']}");
        $this->info("Errors: {$stats['errors']}");

        return self::SUCCESS;
    }
}

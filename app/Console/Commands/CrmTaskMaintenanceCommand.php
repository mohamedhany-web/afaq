<?php

namespace App\Console\Commands;

use App\Services\Tasks\CrmTaskAutomationService;
use Illuminate\Console\Command;

class CrmTaskMaintenanceCommand extends Command
{
    protected $signature = 'crm:task-maintenance';

    protected $description = 'CRM tasks: overdue, reminders, escalation, auto-generation';

    public function handle(CrmTaskAutomationService $automation): int
    {
        $overdue = $automation->markOverdueTasks();
        $reminders = $automation->sendDueReminders();
        $escalations = $automation->escalateOverdueToManagers();
        $generated = $automation->run();

        $this->info("Overdue marked: {$overdue}");
        $this->info("Reminders: {$reminders}");
        $this->info("Escalations: {$escalations}");
        $this->info('Auto-generated: ' . json_encode($generated));

        return self::SUCCESS;
    }
}

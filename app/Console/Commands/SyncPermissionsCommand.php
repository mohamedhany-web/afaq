<?php

namespace App\Console\Commands;

use App\Services\PermissionRegistryService;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'مزامنة السجل المركزي للصلاحيات (config/permissions.php) مع قاعدة البيانات';

    public function handle(PermissionRegistryService $registry): int
    {
        $registry->ensureRegisteredInDatabase();
        $report = $registry->syncReport();

        $this->info("تمت المزامنة — {$report['total_db']} صلاحية في قاعدة البيانات.");

        if ($report['in_db_only'] !== []) {
            $this->warn('صلاحيات في قاعدة البيانات غير موجودة في السجل:');
            foreach ($report['in_db_only'] as $key) {
                $this->line("  - {$key}");
            }
            $this->line('أضفها إلى config/permissions.php → registry');
        }

        if ($report['not_in_ui_modules'] !== []) {
            $this->warn('صلاحيات غير معروضة في مصفوفة الوحدات (permission_modules):');
            foreach ($report['not_in_ui_modules'] as $key) {
                $this->line("  - {$key}");
            }
            $this->line('أضفها إلى config/crm_roles.php → permission_modules');
        }

        return self::SUCCESS;
    }
}

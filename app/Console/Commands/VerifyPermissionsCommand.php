<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPermission;
use App\Services\CrmRoleCatalogService;
use App\Services\PermissionRegistryService;
use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

class VerifyPermissionsCommand extends Command
{
    protected $signature = 'permissions:verify {user_id? : اختبار التفعيل/التعطيل على مستخدم}';

    protected $description = 'التحقق من اكتمال السجل والواجهة وسلوك التفعيل/التعطيل';

    public function handle(PermissionRegistryService $registry): int
    {
        $registry->ensureRegisteredInDatabase();
        $report = $registry->syncReport();
        $registryKeys = $registry->registryKeys();
        $moduleKeys = CrmRoleCatalogService::modulePermissionKeys();

        $this->info('السجل: ' . count($registryKeys) . ' | قاعدة البيانات: ' . $report['total_db'] . ' | الوحدات: ' . count($moduleKeys));

        $failed = false;

        if ($report['in_db_only'] !== []) {
            $failed = true;
            $this->error('صلاحيات في DB غير موجودة في السجل:');
            foreach ($report['in_db_only'] as $key) {
                $this->line("  - {$key}");
            }
        }

        if ($report['not_in_ui_modules'] !== []) {
            $failed = true;
            $this->error('صلاحيات غير معروضة في مصفوفة الواجهة:');
            foreach ($report['not_in_ui_modules'] as $key) {
                $this->line("  - {$key}");
            }
        }

        if ($report['in_registry_only'] !== []) {
            $this->warn('صلاحيات في السجل لم تُزامَن بعد: ' . implode(', ', $report['in_registry_only']));
        }

        $userId = $this->argument('user_id');
        if ($userId) {
            $failed = $this->verifyUserToggle((int) $userId) || $failed;
        }

        if ($failed) {
            $this->error('فشل التحقق — راجع الأخطاء أعلاه.');

            return self::FAILURE;
        }

        $this->info('✓ جميع الصلاحيات مسجّلة ومعروضة في الواجهة.');

        return self::SUCCESS;
    }

    protected function verifyUserToggle(int $userId): bool
    {
        $user = User::with('roles.permissions')->find($userId);
        if (! $user) {
            $this->error("المستخدم #{$userId} غير موجود.");

            return true;
        }

        $sample = 'view-clients';
        $hadBefore = $user->can($sample);

        UserPermission::updateOrCreate(
            ['user_id' => $user->id, 'permission_key' => $sample],
            ['is_enabled' => false]
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user->unsetRelation('customPermissions');
        $user->refresh();

        if ($user->can($sample)) {
            $this->error("تعطيل {$sample} لم يُطبَّق — ما زال المستخدم يملك الصلاحية.");

            return true;
        }

        UserPermission::updateOrCreate(
            ['user_id' => $user->id, 'permission_key' => $sample],
            ['is_enabled' => true]
        );
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user->unsetRelation('customPermissions');
        $user->refresh();

        if (! $user->can($sample)) {
            $this->error("تفعيل {$sample} لم يُطبَّق.");

            return true;
        }

        if (! $hadBefore) {
            UserPermission::where('user_id', $user->id)->where('permission_key', $sample)->delete();
        } else {
            UserPermission::where('user_id', $user->id)->where('permission_key', $sample)->delete();
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info("✓ اختبار التفعيل/التعطيل نجح للمستخدم: {$user->name} ({$sample})");

        return false;
    }
}

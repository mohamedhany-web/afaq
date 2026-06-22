<?php if(!empty($permissionSyncReport)): ?>
    <?php
        $total = $permissionSyncReport['total_db'] ?? 0;
        $inModules = $permissionSyncReport['total_ui_modules'] ?? 0;
        $uncategorized = $permissionSyncReport['not_in_ui_modules'] ?? [];
        $dbOnly = $permissionSyncReport['in_db_only'] ?? [];
    ?>
    <div class="mb-6 rounded-xl border px-4 py-3 text-sm font-tajawal <?php echo e(($uncategorized === [] && $dbOnly === []) ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-amber-50 border-amber-200 text-amber-900'); ?>">
        <p class="font-semibold">
            صلاحيات النظام: <?php echo e($total); ?> في قاعدة البيانات — <?php echo e($inModules); ?> معروضة في المصفوفة
        </p>
        <?php if($uncategorized !== []): ?>
            <p class="mt-2 text-xs">
                غير مصنّفة في الواجهة (<?php echo e(count($uncategorized)); ?>):
                <span dir="ltr" class="font-mono"><?php echo e(implode(', ', array_slice($uncategorized, 0, 8))); ?><?php echo e(count($uncategorized) > 8 ? '…' : ''); ?></span>
                — أضفها في <code>config/crm_roles.php</code>
            </p>
        <?php endif; ?>
        <?php if($dbOnly !== []): ?>
            <p class="mt-1 text-xs">
                في قاعدة البيانات فقط وغير موجودة في السجل المركزي — أضفها في <code>config/permissions.php</code>
            </p>
        <?php endif; ?>
        <?php if($uncategorized === [] && $dbOnly === []): ?>
            <p class="mt-1 text-xs text-blue-700">جميع صلاحيات النظام معروضة هنا. لأي ميزة جديدة: أضف المفتاح في <code>config/permissions.php</code> ثم في <code>permission_modules</code>.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\roles\partials\permission-sync-status.blade.php ENDPATH**/ ?>
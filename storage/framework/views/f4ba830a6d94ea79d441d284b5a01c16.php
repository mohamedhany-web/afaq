<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        سجل حركات العميل
        <p class="text-xs font-normal text-gray-500 mt-1">جميع التعديلات والتحويلات والحذف المرتبطة بهذا العميل</p>
    </div>
    <div class="divide-y divide-gray-100 max-h-[28rem] overflow-y-auto">
        <?php $__empty_1 = true; $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <article class="px-5 sm:px-6 py-4 text-sm font-tajawal">
            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-700"><?php echo e($log->action_name); ?></span>
                <time class="text-[11px] text-gray-400" datetime="<?php echo e($log->created_at->toIso8601String()); ?>">
                    <?php echo e($log->created_at->format('Y/m/d')); ?> · <?php echo e($log->created_at->format('H:i')); ?>

                </time>
            </div>
            <p class="text-gray-800"><?php echo e($log->description); ?></p>
            <?php if($log->user): ?>
            <p class="text-xs text-gray-500 mt-1">بواسطة: <strong><?php echo e($log->user->name); ?></strong></p>
            <?php endif; ?>
            <?php $changes = $log->new_values['changes'] ?? $log->old_values['changes'] ?? null; ?>
            <?php if(is_array($changes) && count($changes)): ?>
            <ul class="mt-2 space-y-1 text-xs text-gray-600 bg-gray-50 rounded-lg p-3">
                <?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><strong><?php echo e($change['label'] ?? $change['field']); ?>:</strong> <?php echo e($change['from'] ?? '—'); ?> ← <?php echo e($change['to'] ?? '—'); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php endif; ?>
            <?php if(($log->new_values['assigned_name'] ?? null) && $log->action === 'client_transferred'): ?>
            <p class="text-xs text-blue-700 mt-1">تحويل إلى: <?php echo e($log->new_values['assigned_name']); ?></p>
            <?php endif; ?>
            <?php if($log->old_values['delete_reason'] ?? $log->new_values['delete_reason'] ?? null): ?>
            <p class="text-xs text-red-700 mt-1">سبب الحذف: <?php echo e($log->old_values['delete_reason'] ?? $log->new_values['delete_reason']); ?></p>
            <?php endif; ?>
        </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="p-8 text-center text-gray-400 text-sm font-tajawal">لا توجد حركات مسجّلة بعد.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\activity-log.blade.php ENDPATH**/ ?>
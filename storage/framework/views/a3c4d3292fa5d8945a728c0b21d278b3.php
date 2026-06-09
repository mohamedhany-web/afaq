<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal"><?php echo e($tableTitle ?? 'التقارير'); ?></h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($reports->total()); ?></span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">التاريخ</th>
                    <?php if($showEmployeeColumn ?? false): ?>
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">الموظف</th>
                    <?php endif; ?>
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">الحالة</th>
                    <th class="text-right p-4 text-xs font-bold text-gray-600 font-tajawal">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 text-sm font-tajawal"><?php echo e($report->report_date->format('Y-m-d')); ?></td>
                    <?php if($showEmployeeColumn ?? false): ?>
                    <td class="p-4 text-sm font-tajawal font-medium text-gray-900"><?php echo e($report->author?->name); ?></td>
                    <?php endif; ?>
                    <td class="p-4">
                        <?php if($report->isSubmitted()): ?>
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 font-tajawal">مرفوع</span>
                        <?php else: ?>
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 font-tajawal">مسودة</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4">
                        <a href="<?php echo e(route('crm.daily-reports.show', $report)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold font-tajawal text-white hover:opacity-90"
                           style="background: <?php echo e($themeColor); ?>;">عرض</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e(($showEmployeeColumn ?? false) ? 4 : 3); ?>" class="p-8 text-center text-gray-500 text-sm font-tajawal"><?php echo e($emptyMessage ?? 'لا توجد تقارير.'); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reports->hasPages()): ?>
    <div class="p-4 sm:p-5 border-t border-gray-200"><?php echo e($reports->links()); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/daily-reports/partials/list-table.blade.php ENDPATH**/ ?>
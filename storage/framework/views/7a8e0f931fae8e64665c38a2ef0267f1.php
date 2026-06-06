

<?php $__env->startSection('page-title', $meta['title'] ?? 'تقرير'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $columns = $payload['columns'] ?? [];
    $rows = $payload['rows'] ?? [];
    $exportUrl = route('admin.system-reports.export', $payload['report_key']);
    if ($supportsDateFilter && request('start_date')) {
        $exportUrl .= '?' . http_build_query(request()->only(['start_date', 'end_date']));
    }
?>
<div class="w-full font-tajawal">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <a href="<?php echo e(route('admin.system-reports.index')); ?>" class="text-sm font-medium mb-2 inline-flex items-center gap-1 hover:underline" style="color: <?php echo e($themeColor); ?>">
                ← مركز التقارير
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($meta['title']); ?></h1>
            <p class="text-sm text-gray-600 mt-1"><?php echo e($meta['description']); ?></p>
            <p class="text-xs text-gray-500 mt-1">آخر تحديث: <?php echo e($payload['generated_at']); ?> <?php if(!empty($payload['period_label'])): ?> — <?php echo e($payload['period_label']); ?> <?php endif; ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e($exportUrl); ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md"
               style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                تصدير Excel
            </a>
        </div>
    </div>

    <?php if($supportsDateFilter): ?>
    <form method="GET" class="mb-6 bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">من تاريخ</label>
            <input type="date" name="start_date" value="<?php echo e(request('start_date', $payload['filters']['start_date'] ?? '')); ?>"
                   class="rounded-xl border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">إلى تاريخ</label>
            <input type="date" name="end_date" value="<?php echo e(request('end_date', $payload['filters']['end_date'] ?? '')); ?>"
                   class="rounded-xl border-gray-300 text-sm">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background: <?php echo e($themeColor); ?>">تطبيق</button>
        <a href="<?php echo e(route('admin.system-reports.show', $payload['report_key'])); ?>" class="text-sm text-gray-500 hover:text-gray-700">مسح</a>
    </form>
    <?php endif; ?>

    <?php if(!empty($payload['summary'])): ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        <?php $__currentLoopData = $payload['summary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1"><?php echo e($item['label']); ?></p>
            <p class="text-lg font-bold text-gray-900"><?php echo e($item['value']); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3 border-b border-gray-100 flex justify-between items-center">
            <span class="text-sm font-semibold text-gray-700">البيانات التفصيلية</span>
            <span class="text-xs px-2.5 py-1 rounded-full" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>"><?php echo e(count($rows)); ?> سجل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-right">
                <thead>
                    <tr style="background: <?php echo e($themeColor); ?>; color: #fff;">
                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap"><?php echo e($col['label']); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="<?php echo e($i % 2 ? 'bg-gray-50' : 'bg-white'); ?> border-b border-gray-100">
                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $val = is_array($row) ? ($row[$col['key']] ?? '—') : '—';
                            if (($col['type'] ?? null) === 'money' && is_numeric($val)) {
                                $val = number_format((float) $val, 2);
                            }
                        ?>
                        <td class="px-4 py-2.5 text-gray-800 max-w-xs truncate"><?php echo e($val); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(max(count($columns), 1)); ?>" class="px-4 py-12 text-center text-gray-500">لا توجد بيانات للفترة المحددة</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/admin/system-reports/show.blade.php ENDPATH**/ ?>
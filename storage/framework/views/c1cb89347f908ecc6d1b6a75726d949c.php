
<?php $__env->startSection('page-title', 'تقارير العمليات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $periodLabels = config('operations_reports.period_types');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $resolver->isAdmin() ? 'تقارير مديري العمليات' : 'تقاريري الدورية',
    'subtitle' => 'تقارير تشغيلية يحددها مدير العمليات وتراجعها الإدارة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="mb-4 flex flex-wrap gap-2">
    <?php $__currentLoopData = $periodLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('operations.reports.index', ['period' => $key])); ?>"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 <?php echo e($periodType === $key ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white'); ?>"
       <?php if($periodType === $key): ?> style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);" <?php endif; ?>>
        <?php echo e($label); ?>

    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مرفوعة', 'value' => $stats['submitted'], 'accent' => 'green', 'href' => route('operations.reports.index', ['status' => 'submitted']) . '#page-data', 'linkLabel' => 'عرض المرفوعة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مسودات', 'value' => $stats['draft'], 'accent' => 'amber', 'href' => route('operations.reports.index', ['status' => 'draft']) . '#page-data', 'linkLabel' => 'عرض المسودات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'القائمة', 'value' => $reports->total(), 'accent' => 'theme', 'href' => route('operations.reports.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($resolver->isManager()): ?>
<div class="bg-white rounded-2xl shadow-lg border mb-6 p-5 font-tajawal">
    <p class="font-bold text-gray-900 mb-3">إنشاء تقرير <?php echo e($periodLabels[$periodType] ?? ''); ?></p>
    <form method="POST" action="<?php echo e(route('operations.reports.generate')); ?>" class="flex flex-wrap gap-3 items-end">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="period_type" value="<?php echo e($periodType); ?>">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ الفترة</label>
            <input type="date" name="anchor_date" value="<?php echo e(old('anchor_date', today()->toDateString())); ?>" max="<?php echo e(today()->toDateString()); ?>" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm" required>
        </div>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-bold" style="background: <?php echo e($themeColor); ?>;">إنشاء / فتح التقرير</button>
    </form>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="px-5 py-4 border-b font-bold">قائمة التقارير — <?php echo e($periodLabels[$periodType] ?? ''); ?></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <?php if($resolver->isAdmin()): ?><th class="p-3 text-right">مدير العمليات</th><?php endif; ?>
                <th class="p-3 text-right">الفترة</th>
                <th class="p-3 text-right">النوع</th>
                <th class="p-3 text-right">ملاحظات</th>
                <th class="p-3 text-right">الحالة</th>
                <th class="p-3 text-right">إجراء</th>
            </tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t border-gray-100 hover:bg-gray-50">
                <?php if($resolver->isAdmin()): ?><td class="p-3"><?php echo e($report->author?->name); ?></td><?php endif; ?>
                <td class="p-3"><?php echo e($report->periodRangeLabel()); ?></td>
                <td class="p-3"><?php echo e($report->periodLabel()); ?></td>
                <td class="p-3 text-gray-600 max-w-xs">
                    <?php if($report->notes): ?>
                    <span class="line-clamp-2 text-xs" title="<?php echo e($report->notes); ?>"><?php echo e(Str::limit($report->notes, 80)); ?></span>
                    <?php else: ?>
                    <span class="text-gray-400 text-xs">—</span>
                    <?php endif; ?>
                </td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-xs font-bold <?php echo e($report->isSubmitted() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'); ?>">
                        <?php echo e($report->isSubmitted() ? 'مرفوع' : 'مسودة'); ?>

                    </span>
                </td>
                <td class="p-3"><a href="<?php echo e(route('operations.reports.show', $report)); ?>" class="font-bold" style="color: <?php echo e($themeColor); ?>;">عرض</a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="<?php echo e($resolver->isAdmin() ? 6 : 5); ?>" class="p-6 text-center text-gray-500">لا توجد تقارير بعد.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reports->hasPages()): ?><div class="p-4"><?php echo e($reports->links()); ?></div><?php endif; ?>
</div>

<?php if(($salesRepRows ?? collect())->isNotEmpty()): ?>
<div class="mt-6 bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-2" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">
        <div>
            <p class="font-bold text-gray-900">مندوبو المبيعات</p>
            <p class="text-xs text-gray-500 mt-0.5">حالة تقاريرهم اليومية للفترة المحددة</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="hidden" name="period" value="<?php echo e($periodType); ?>">
            <?php if(request('status')): ?><input type="hidden" name="status" value="<?php echo e(request('status')); ?>"><?php endif; ?>
            <input type="date" name="anchor_date" value="<?php echo e(request('anchor_date', today()->toDateString())); ?>" max="<?php echo e(today()->toDateString()); ?>" class="border rounded-lg px-3 py-1.5 text-xs">
            <button type="submit" class="px-3 py-1.5 rounded-lg text-white text-xs font-bold" style="background:<?php echo e($themeColor); ?>">تحديث</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-right">اسم السيلز</th>
                    <th class="p-3 text-right">حالة التقرير</th>
                    <th class="p-3 text-right">ملاحظات</th>
                    <th class="p-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__currentLoopData = $salesRepRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-semibold text-gray-900"><?php echo e($row['user']->name); ?></td>
                    <td class="p-3">
                        <?php if($row['submitted']): ?>
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800 font-bold">مرفوع</span>
                        <?php if($periodType !== 'daily' && ($row['reports_count'] ?? 0) > 0): ?>
                        <span class="text-[10px] text-gray-500 mr-1">(<?php echo e($row['reports_count']); ?>)</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800 font-bold">لم يُرفع</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3 text-xs text-gray-600 max-w-md">
                        <?php if($row['notes']): ?>
                        <span class="line-clamp-2" title="<?php echo e($row['notes']); ?>"><?php echo e(Str::limit($row['notes'], 100)); ?></span>
                        <?php else: ?>
                        <span class="text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <?php if($row['report_url']): ?>
                        <a href="<?php echo e($row['report_url']); ?>" class="text-xs font-bold" style="color:<?php echo e($themeColor); ?>">عرض التقرير</a>
                        <?php else: ?>
                        <span class="text-xs text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\reports\index.blade.php ENDPATH**/ ?>
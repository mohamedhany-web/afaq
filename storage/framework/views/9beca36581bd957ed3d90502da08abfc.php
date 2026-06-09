
<?php $__env->startSection('page-title', 'التقارير الدورية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $isManager = $resolver->isManager() || $resolver->isAdmin();
    $periodLabels = config('marketing_reports.period_types');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'التقارير الدورية — التسويق',
    'subtitle' => $isManager ? 'تقاريرك الإلزامية ومتابعة فريق التسويق' : 'التقرير اليومي الإلزامي',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'actionUrl' => route('marketing.analytics.index'),
    'actionLabel' => 'تحليلات الأداء',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div><?php endif; ?>

<?php if(count($myPending)): ?>
<div class="mb-6 p-5 rounded-2xl border-2 border-amber-300 bg-amber-50 font-tajawal">
    <p class="font-bold text-amber-900 mb-3">تقارير إلزامية لم تُرفع بعد</p>
    <div class="flex flex-wrap gap-2">
        <?php $__currentLoopData = $myPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($item['report'] ? route('marketing.reports.show', $item['report']) : route('marketing.reports.index', ['period' => $item['type']])); ?>"
           class="px-4 py-2 rounded-xl text-sm font-bold <?php echo e($item['status'] === 'missing' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-900'); ?>">
            تقرير <?php echo e($item['label']); ?> — <?php echo e($item['status'] === 'missing' ? 'لم يُنشأ' : 'مسودة'); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="mb-4 flex flex-wrap gap-2">
    <?php $__currentLoopData = $periodLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('marketing.reports.index', ['period' => $key])); ?>"
       class="px-5 py-2.5 rounded-xl text-sm font-bold font-tajawal border-2 <?php echo e($periodType === $key ? 'text-white border-transparent' : 'border-gray-200 text-gray-600 bg-white'); ?>"
       <?php if($periodType === $key): ?> style="background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%);" <?php endif; ?>>
        <?php echo e($label); ?>

    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مرفوعة', 'value' => $stats['submitted'], 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مسودات', 'value' => $stats['draft'], 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'القائمة', 'value' => $reports->total(), 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('marketing.period-reports.partials.create-form', ['periodType' => $periodType, 'isManager' => $isManager], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($isManager && $periodType === 'daily' && count($teamDailyStatus)): ?>
<div class="bg-white rounded-2xl shadow-lg border mb-6 overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b font-bold" style="background: linear-gradient(135deg, #7c3aed08 0%, transparent 100%);">التزام الفريق — تقرير اليوم</div>
    <div class="divide-y">
        <?php $__currentLoopData = $teamDailyStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="px-5 py-3 flex justify-between items-center gap-3">
            <span class="font-semibold"><?php echo e($row['user']->name); ?></span>
            <?php if($row['submitted']): ?>
            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">مرفوع</span>
            <?php else: ?>
            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">لم يُرفع</span>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b font-bold">قائمة التقارير — <?php echo e($periodLabels[$periodType] ?? ''); ?></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <?php if($isManager): ?><th class="p-3 text-right">الموظف</th><?php endif; ?>
                <th class="p-3 text-right">الفترة</th>
                <th class="p-3 text-right">الحالة</th>
                <th class="p-3 text-right">إجراء</th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <?php if($isManager): ?><td class="p-3"><?php echo e($r->author?->name); ?></td><?php endif; ?>
                    <td class="p-3"><?php echo e($r->periodRangeLabel()); ?></td>
                    <td class="p-3">
                        <span class="text-xs px-2 py-1 rounded-full <?php echo e($r->isSubmitted() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'); ?>">
                            <?php echo e($r->isSubmitted() ? 'مرفوع' : 'مسودة'); ?>

                        </span>
                    </td>
                    <td class="p-3">
                        <a href="<?php echo e(route('marketing.reports.show', $r)); ?>" class="text-xs font-bold" style="color:#7c3aed">عرض</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="<?php echo e($isManager ? 4 : 3); ?>" class="p-8 text-center text-gray-500">لا تقارير في هذه الفترة.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reports->hasPages()): ?><div class="p-4 border-t"><?php echo e($reports->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\period-reports\index.blade.php ENDPATH**/ ?>
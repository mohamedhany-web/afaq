
<?php $__env->startSection('page-title', 'تحليلات التسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', ['title' => 'تحليلات التسويق', 'subtitle' => 'أداء الحملات والقنوات', 'actionUrl' => route('marketing.reports.index'), 'actionLabel' => 'التقارير الدورية'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الحملات', 'value' => $summary['campaigns'], 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'Leads', 'value' => $summary['leads'], 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نشطة', 'value' => $summary['active'], 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الميزانية', 'value' => number_format($summary['budget']), 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المصروف', 'value' => number_format($summary['spent']), 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء نشطون', 'value' => $summary['conversion_hint'], 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <h3 class="font-bold mb-4">حسب القناة</h3>
        <?php $__empty_1 = true; $__currentLoopData = $byChannel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex justify-between py-2 border-b text-sm"><span><?php echo e($row['channel']); ?></span><span><?php echo e($row['campaigns']); ?> حملة · <?php echo e(number_format($row['budget'])); ?> ج.م</span></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-gray-500 text-sm">لا بيانات</p><?php endif; ?>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <h3 class="font-bold mb-4">أفضل الحملات (Leads)</h3>
        <?php $__empty_1 = true; $__currentLoopData = $topCampaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex justify-between py-2 border-b text-sm"><span><?php echo e($c->name); ?></span><span class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($c->leads_count); ?></span></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-gray-500 text-sm">لا بيانات</p><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/analytics/index.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'تقارير التعويضات'); ?>

<?php $__env->startSection('content'); ?>
<?php $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v); $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', ['title' => 'تقارير الرواتب والتعويضات', 'subtitle' => "$month / $year"], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form method="GET" class="flex gap-2 mb-6 font-tajawal text-sm">
    <input type="number" name="year" value="<?php echo e($year); ?>" class="w-24 rounded-lg border-gray-300" min="2020">
    <input type="number" name="month" value="<?php echo e($month); ?>" class="w-20 rounded-lg border-gray-300" min="1" max="12">
    <button class="px-3 py-1 border rounded-lg">عرض</button>
</form>

<div class="grid md:grid-cols-3 gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الرواتب', 'value' => $money($runs->sum('net_pay')), 'compact' => true, 'href' => route('crm.compensation.reports.index') . '#page-data', 'linkLabel' => 'عرض التقارير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'العمولات', 'value' => $money($runs->sum('commission_total')), 'compact' => true, 'accent' => 'green', 'href' => route('crm.compensation.reports.index') . '#page-data', 'linkLabel' => 'عرض التقارير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المكافآت المعتمدة', 'value' => $money($bonuses->sum('amount')), 'compact' => true, 'accent' => 'amber', 'href' => route('crm.compensation.reports.index') . '#page-data', 'linkLabel' => 'عرض التقارير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl border p-5 font-tajawal text-sm mb-6">
    <h3 class="font-bold mb-3">تقرير الرواتب الشهري</h3>
    <table class="min-w-full"><thead><tr class="text-gray-500 border-b"><th class="text-right py-2">الموظف</th><th class="text-center">KPI</th><th class="text-center">عمولة</th><th class="text-center">صافي</th></tr></thead>
        <tbody><?php $__currentLoopData = $runs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr class="border-b"><td class="py-2"><?php echo e($r->user?->name); ?></td><td class="text-center"><?php echo e(round($r->kpi_score ?? 0,1)); ?>%</td><td class="text-center"><?php echo e($money($r->commission_total)); ?></td><td class="text-center font-bold"><?php echo e($money($r->net_pay)); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tbody>
    </table>
</div>

<div class="grid md:grid-cols-2 gap-4 text-sm font-tajawal">
    <div class="bg-white rounded-2xl border p-4"><h4 class="font-bold mb-2">المكافآت</h4><?php $__empty_1 = true; $__currentLoopData = $bonuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="py-1 border-b"><?php echo e($b->user?->name); ?>: <?php echo e($money($b->amount)); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-gray-500">لا يوجد</p><?php endif; ?></div>
    <div class="bg-white rounded-2xl border p-4"><h4 class="font-bold mb-2">الخصومات</h4><?php $__empty_1 = true; $__currentLoopData = $deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><div class="py-1 border-b"><?php echo e($d->user?->name); ?>: <?php echo e($money($d->amount)); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><p class="text-gray-500">لا يوجد</p><?php endif; ?></div>
</div>

<a href="<?php echo e(route('crm.compensation.dashboard')); ?>" class="inline-block mt-6 text-sm" style="color:<?php echo e($themeColor); ?>">← العودة</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\compensation\admin\reports\index.blade.php ENDPATH**/ ?>
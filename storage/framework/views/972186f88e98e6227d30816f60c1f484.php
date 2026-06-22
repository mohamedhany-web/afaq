
<?php $__env->startSection('page-title', 'إدارة التعويضات والرواتب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'لوحة الرواتب والتعويضات',
    'subtitle' => $period->starts_at->locale('ar')->translatedFormat('F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 0v8m0-8l-6 6-3-3-6 6" />',
    'actionUrl' => route('crm.compensation.reports.index'),
    'actionLabel' => 'التقارير',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الرواتب', 'value' => $money($stats['total_payroll']), 'compact' => true, 'href' => route('crm.compensation.dashboard') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'العمولات', 'value' => $money($stats['total_commission']), 'compact' => true, 'accent' => 'green', 'href' => route('crm.compensation.dashboard') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المكافآت', 'value' => $money($stats['total_bonus']), 'compact' => true, 'accent' => 'amber', 'href' => route('crm.compensation.dashboard') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الخصومات', 'value' => $money($stats['total_deduction']), 'compact' => true, 'accent' => 'red', 'href' => route('crm.compensation.dashboard') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متوسط KPI', 'value' => $stats['avg_kpi'] . '%', 'compact' => true, 'accent' => 'purple', 'href' => route('crm.compensation.dashboard') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الموظفون', 'value' => $stats['employees'], 'compact' => true, 'accent' => 'blue', 'href' => route('crm.compensation.dashboard') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6 mb-6">
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b flex justify-between items-center" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold font-tajawal">أعلى الرواتب</h3>
            <form method="POST" action="<?php echo e(route('crm.compensation.payroll.recalculate')); ?>"><?php echo csrf_field(); ?><button class="text-sm px-3 py-1 border rounded-lg">إعادة حساب الكل</button></form>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full text-sm font-tajawal">
                <thead><tr class="text-gray-500 border-b"><th class="text-right py-2">الموظف</th><th class="text-center">KPI</th><th class="text-center">الصافي</th><th class="text-center">الحالة</th><th></th></tr></thead>
                <tbody>
                <?php $__currentLoopData = $runs->take(15); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $run): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b border-gray-100">
                        <td class="py-2"><?php echo e($run->user?->name); ?></td>
                        <td class="text-center"><?php echo e(round($run->kpi_score ?? 0, 1)); ?>%</td>
                        <td class="text-center font-semibold"><?php echo e($money($run->net_pay)); ?></td>
                        <td class="text-center"><?php echo e($run->status); ?></td>
                        <td class="text-left flex gap-2 justify-end">
                            <a href="<?php echo e(route('crm.compensation.payroll.show', $run)); ?>" class="text-xs" style="color:<?php echo e($themeColor); ?>">عرض</a>
                            <?php if($run->status !== 'approved'): ?>
                            <form method="POST" action="<?php echo e(route('crm.compensation.payroll.approve', $run)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('POST'); ?><button class="text-xs text-green-700">اعتماد</button></form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b" style="<?php echo e($headerStyle); ?>"><h3 class="font-bold font-tajawal">توزيع مستويات KPI</h3></div>
        <ul class="p-4 space-y-2 font-tajawal text-sm">
            <?php $__currentLoopData = $kpiDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex justify-between"><span><?php echo e($row['label']); ?></span><span class="font-bold"><?php echo e($row['count']); ?></span></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>

<?php if($pendingAdjustments->isNotEmpty()): ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b" style="<?php echo e($headerStyle); ?>"><h3 class="font-bold font-tajawal">طلبات مكافآت/خصومات للاعتماد</h3></div>
    <div class="p-4 space-y-3">
        <?php $__currentLoopData = $pendingAdjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3 text-sm font-tajawal">
                <div>
                    <span class="font-semibold"><?php echo e($adj->user?->name); ?></span> —
                    <?php echo e($adj->type === 'bonus' ? 'مكافأة' : 'خصم'); ?>:
                    <?php echo e($money($adj->amount)); ?>

                    <div class="text-gray-500 text-xs"><?php echo e($adj->reason); ?></div>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="<?php echo e(route('crm.compensation.adjustments.review', $adj)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="approve"><button class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs">موافقة</button></form>
                    <form method="POST" action="<?php echo e(route('crm.compensation.adjustments.review', $adj)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="reject"><button class="px-3 py-1 rounded-lg bg-red-100 text-red-700 text-xs">رفض</button></form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="flex flex-wrap gap-3 font-tajawal text-sm">
    <a href="<?php echo e(route('crm.compensation.kpi.index')); ?>" class="px-4 py-2 rounded-xl border">قوالب KPI</a>
    <a href="<?php echo e(route('crm.compensation.profiles.index')); ?>" class="px-4 py-2 rounded-xl border">هياكل التعويض</a>
    <a href="<?php echo e(route('crm.compensation.reports.index')); ?>" class="px-4 py-2 rounded-xl text-white" style="background:<?php echo e($themeColor); ?>">التقارير</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\compensation\admin\dashboard.blade.php ENDPATH**/ ?>
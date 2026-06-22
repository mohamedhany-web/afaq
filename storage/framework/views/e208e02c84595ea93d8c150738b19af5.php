
<?php $__env->startSection('page-title', 'تعويضات الفريق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $myKpi = $myRun->breakdown['kpi']['level']['label'] ?? '—';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تعويضات الفريق',
    'subtitle' => $period->starts_at->locale('ar')->translatedFormat('F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'راتبي الأساسي', 'value' => $money($myRun->base_salary), 'compact' => true, 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'درجة KPI', 'value' => round($myRun->kpi_score ?? 0, 1) . '%', 'compact' => true, 'accent' => 'purple', 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أداء الفريق', 'value' => round($myRun->team_score ?? 0, 1) . '%', 'compact' => true, 'accent' => 'green', 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صافي راتبي', 'value' => $money($myRun->net_pay), 'compact' => true, 'accent' => 'theme', 'href' => route('crm.compensation.dashboard') . '#payroll-details', 'linkLabel' => 'عرض الراتب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3" style="<?php echo e($headerStyle); ?>">
        <h3 class="font-bold text-lg font-tajawal">أداء وتعويضات الفريق</h3>
        <form method="POST" action="<?php echo e(route('crm.compensation.payroll.recalculate')); ?>"><?php echo csrf_field(); ?><button type="submit" class="text-sm px-3 py-1.5 rounded-lg border">إعادة حساب الفريق</button></form>
    </div>
    <div class="p-4 overflow-x-auto">
        <table class="min-w-full text-sm font-tajawal">
            <thead><tr class="text-gray-500 border-b"><th class="text-right py-2">الموظف</th><th class="text-center">KPI</th><th class="text-center">عمولة</th><th class="text-center">مكافآت</th><th class="text-center">خصومات</th><th class="text-center">الصافي</th><th></th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $teamRuns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-100">
                    <td class="py-2"><?php echo e($tr->user?->name); ?></td>
                    <td class="text-center"><?php echo e(round($tr->kpi_score ?? 0, 1)); ?>%</td>
                    <td class="text-center"><?php echo e($money($tr->commission_total)); ?></td>
                    <td class="text-center"><?php echo e($money($tr->bonus_total)); ?></td>
                    <td class="text-center"><?php echo e($money($tr->deduction_total)); ?></td>
                    <td class="text-center font-semibold"><?php echo e($money($tr->net_pay)); ?></td>
                    <td class="text-left"><a href="<?php echo e(route('crm.compensation.payroll.show', $tr)); ?>" class="text-sm" style="color:<?php echo e($themeColor); ?>">تفاصيل</a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="py-6 text-center text-gray-500">لا توجد بيانات للفريق بعد</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php echo $__env->make('crm.compensation.partials.adjustment-form', ['users' => $teamRuns->pluck('user')->filter()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($pendingAdjustments->isNotEmpty()): ?>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 font-tajawal text-sm">
    <strong>طلبات قيد الاعتماد:</strong> <?php echo e($pendingAdjustments->count()); ?>

</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\compensation\manager\dashboard.blade.php ENDPATH**/ ?>
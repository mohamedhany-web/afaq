

<?php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
?>

<?php $__env->startSection('page-title', 'قائمة الدخل'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.report-header', [
    'title' => 'قائمة الدخل',
    'subtitle' => 'من ' . $reportStartDate->format('Y/m/d') . ' إلى ' . $reportEndDate->format('Y/m/d'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('accounting.partials.report-toolbar', [
    'filterType' => 'range',
    'startDate' => $startDate,
    'endDate' => $endDate,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الإيرادات', 'value' => $money($totalRevenue), 'accent' => 'green', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي المصروفات', 'value' => $money($totalExpenses), 'accent' => 'red', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => $netIncome >= 0 ? 'صافي الربح' : 'صافي الخسارة',
        'value' => $money(abs($netIncome)),
        'accent' => $netIncome >= 0 ? 'blue' : 'red',
        'compact' => true,
        'footer' => $totalRevenue > 0 ? '<span class="text-gray-500">هامش: ' . number_format($profitMargin, 1) . '%</span>' : null,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الفترة', 'value' => $reportStartDate->format('Y/m/d') . ' — ' . $reportEndDate->format('Y/m/d'), 'accent' => 'theme', 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold"><?php echo $__env->make('accounting.partials.company-name', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h2>
        <h3 class="text-lg font-bold mt-3">قائمة الدخل</h3>
        <p class="text-sm text-gray-700">من <?php echo e($reportStartDate->format('Y/m/d')); ?> إلى <?php echo e($reportEndDate->format('Y/m/d')); ?></p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>cc 100%);">
            <h2 class="text-xl font-bold"><?php echo $__env->make('accounting.partials.company-name', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">قائمة الدخل</h3>
            <p class="text-sm opacity-90 mt-1">من <?php echo e($reportStartDate->format('Y/m/d')); ?> إلى <?php echo e($reportEndDate->format('Y/m/d')); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="border-l border-gray-100">
                <div class="<?php echo e($sectionHeader); ?> bg-green-50 text-green-900">الإيرادات</div>
                <div class="p-5 sm:p-6">
                    <?php if($revenues->count() > 0): ?>
                        <?php echo $__env->make('accounting.partials.report-account-tree', ['accounts' => $revenues], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="flex justify-between py-3 mt-4 px-4 rounded-xl bg-green-50 border border-green-100">
                            <span class="font-bold text-green-800">إجمالي الإيرادات</span>
                            <span class="font-bold text-green-800 tabular-nums"><?php echo e($money($totalRevenue)); ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-center py-10 text-gray-500 text-sm">لا توجد إيرادات للفترة المحددة.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <div class="<?php echo e($sectionHeader); ?> bg-red-50 text-red-900">المصروفات</div>
                <div class="p-5 sm:p-6">
                    <?php if($expenses->count() > 0): ?>
                        <?php echo $__env->make('accounting.partials.report-account-tree', ['accounts' => $expenses], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="flex justify-between py-3 mt-4 px-4 rounded-xl bg-red-50 border border-red-100">
                            <span class="font-bold text-red-800">إجمالي المصروفات</span>
                            <span class="font-bold text-red-800 tabular-nums"><?php echo e($money($totalExpenses)); ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-center py-10 text-gray-500 text-sm">لا توجد مصروفات للفترة المحددة.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="p-6 border-t-2 border-gray-200 <?php echo e($netIncome >= 0 ? 'bg-green-50' : 'bg-red-50'); ?>">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <span class="text-xl font-bold <?php echo e($netIncome >= 0 ? 'text-green-900' : 'text-red-900'); ?>">
                    <?php echo e($netIncome >= 0 ? 'صافي الربح' : 'صافي الخسارة'); ?>

                </span>
                <span class="text-2xl font-bold tabular-nums <?php echo e($netIncome >= 0 ? 'text-green-900' : 'text-red-900'); ?>">
                    <?php echo e($money(abs($netIncome))); ?>

                </span>
            </div>
            <?php if($totalRevenue > 0): ?>
            <p class="text-sm text-center mt-3 text-gray-600">
                هامش الربح: <strong class="<?php echo e($netIncome >= 0 ? 'text-green-700' : 'text-red-700'); ?>"><?php echo e(number_format($profitMargin, 2)); ?>%</strong>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $__env->make('accounting.partials.report-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\reports\income-statement.blade.php ENDPATH**/ ?>
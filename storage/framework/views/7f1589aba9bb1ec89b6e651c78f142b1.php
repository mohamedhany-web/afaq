

<?php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
    $isBalanced = abs($totalAssets - $totalLiabilitiesEquity) < 0.01;
?>

<?php $__env->startSection('page-title', 'الميزانية العمومية'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.report-header', [
    'title' => 'الميزانية العمومية',
    'subtitle' => 'حتى تاريخ ' . $reportDate->format('Y/m/d'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('accounting.partials.report-toolbar', ['filterType' => 'date', 'date' => $date], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الأصول', 'value' => $money($totalAssets), 'accent' => 'green', 'compact' => true, 'href' => route('accounting.reports.balance-sheet') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الخصوم', 'value' => $money($totalLiabilities), 'accent' => 'amber', 'compact' => true, 'href' => route('accounting.reports.balance-sheet') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حقوق الملكية', 'value' => $money($totalEquity), 'accent' => 'blue', 'compact' => true, 'href' => route('accounting.reports.balance-sheet') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التوازن', 'value' => $isBalanced ? 'متوازنة' : 'غير متوازنة', 'accent' => $isBalanced ? 'green' : 'red', 'compact' => true, 'href' => route('accounting.reports.balance-sheet') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold"><?php echo $__env->make('accounting.partials.company-name', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h2>
        <p class="text-sm text-gray-600 mt-1"><?php echo e(\App\Helpers\SettingsHelper::getCompanyAddress()); ?></p>
        <h3 class="text-lg font-bold mt-3">الميزانية العمومية</h3>
        <p class="text-sm text-gray-700">حتى تاريخ: <?php echo e($reportDate->format('Y/m/d')); ?></p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>cc 100%);">
            <h2 class="text-xl font-bold"><?php echo $__env->make('accounting.partials.company-name', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">الميزانية العمومية</h3>
            <p class="text-sm opacity-90 mt-1">حتى تاريخ: <?php echo e($reportDate->format('Y/m/d')); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?> bg-green-50 text-green-900">الأصول</div>
            <div class="p-5 sm:p-6">
                <?php if($assets->count() > 0): ?>
                    <?php echo $__env->make('accounting.partials.report-account-tree', ['accounts' => $assets], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <div class="flex justify-between py-3 mt-4 px-4 rounded-xl bg-green-50 border border-green-100">
                        <span class="font-bold text-green-800">إجمالي الأصول</span>
                        <span class="font-bold text-green-800 tabular-nums"><?php echo e($money($totalAssets)); ?></span>
                    </div>
                <?php else: ?>
                    <p class="text-center py-10 text-gray-500 text-sm">لا توجد أصول مسجّلة.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?> bg-blue-50 text-blue-900">الخصوم وحقوق الملكية</div>
            <div class="p-5 sm:p-6">
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">الخصوم</h4>
                <?php if($liabilities->count() > 0): ?>
                    <?php echo $__env->make('accounting.partials.report-account-tree', ['accounts' => $liabilities], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <div class="flex justify-between py-2 px-3 mb-4 rounded-lg bg-amber-50 border border-amber-100 text-sm">
                        <span class="font-bold text-amber-800">إجمالي الخصوم</span>
                        <span class="font-bold text-amber-800 tabular-nums"><?php echo e($money($totalLiabilities)); ?></span>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500 mb-4">لا توجد خصوم.</p>
                <?php endif; ?>

                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">حقوق الملكية</h4>
                <?php if($equity->count() > 0): ?>
                    <?php echo $__env->make('accounting.partials.report-account-tree', ['accounts' => $equity], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <div class="flex justify-between py-2 border-b border-gray-100 text-sm">
                    <span class="text-gray-700">الأرباح المحتجزة</span>
                    <span class="font-bold tabular-nums"><?php echo e($money($retainedEarnings)); ?></span>
                </div>
                <div class="flex justify-between py-2 px-3 mt-2 mb-4 rounded-lg bg-blue-50 border border-blue-100 text-sm">
                    <span class="font-bold text-blue-800">إجمالي حقوق الملكية</span>
                    <span class="font-bold text-blue-800 tabular-nums"><?php echo e($money($totalEquity)); ?></span>
                </div>

                <div class="flex justify-between py-3 px-4 rounded-xl bg-blue-50 border border-blue-100">
                    <span class="font-bold text-blue-900">إجمالي الخصوم وحقوق الملكية</span>
                    <span class="font-bold text-blue-900 tabular-nums"><?php echo e($money($totalLiabilitiesEquity)); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 text-center">
        <?php if($isBalanced): ?>
        <div class="inline-flex items-center gap-2 text-green-700 font-bold text-lg">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            الميزانية متوازنة
        </div>
        <p class="text-sm text-gray-600 mt-2">إجمالي الأصول = إجمالي الخصوم وحقوق الملكية</p>
        <?php else: ?>
        <div class="inline-flex items-center gap-2 text-red-700 font-bold text-lg">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            الميزانية غير متوازنة
        </div>
        <p class="text-sm text-gray-600 mt-2">الفرق: <?php echo e($money(abs($totalAssets - $totalLiabilitiesEquity))); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php echo $__env->make('accounting.partials.report-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\reports\balance-sheet.blade.php ENDPATH**/ ?>
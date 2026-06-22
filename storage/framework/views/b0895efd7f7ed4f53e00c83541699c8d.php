

<?php
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
    $reportStartDate = \Carbon\Carbon::parse($startDate);
    $reportEndDate = \Carbon\Carbon::parse($endDate);
?>

<?php $__env->startSection('page-title', 'قائمة التدفق النقدي'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.report-header', [
    'title' => 'قائمة التدفق النقدي',
    'subtitle' => 'من ' . $reportStartDate->format('Y/m/d') . ' إلى ' . $reportEndDate->format('Y/m/d'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('accounting.partials.report-toolbar', [
    'filterType' => 'range',
    'startDate' => $startDate,
    'endDate' => $endDate,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 no-print">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تشغيلي', 'value' => ($operatingCashFlow >= 0 ? '+' : '-') . $money(abs($operatingCashFlow)), 'accent' => $operatingCashFlow >= 0 ? 'green' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'استثماري', 'value' => ($investingCashFlow >= 0 ? '+' : '-') . $money(abs($investingCashFlow)), 'accent' => $investingCashFlow >= 0 ? 'purple' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تمويلي', 'value' => ($financingCashFlow >= 0 ? '+' : '-') . $money(abs($financingCashFlow)), 'accent' => $financingCashFlow >= 0 ? 'blue' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صافي التغير', 'value' => ($netCashFlow >= 0 ? '+' : '-') . $money(abs($netCashFlow)), 'accent' => $netCashFlow >= 0 ? 'theme' : 'red', 'compact' => true, 'href' => route('accounting.reports.cash-flow') . '#page-data', 'linkLabel' => 'عرض التقرير'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="report-document" class="font-tajawal">
    <div class="report-print-header text-center mb-6 pb-4 border-b-2 border-gray-900">
        <h2 class="text-xl font-bold"><?php echo $__env->make('accounting.partials.company-name', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h2>
        <h3 class="text-lg font-bold mt-3">قائمة التدفق النقدي</h3>
        <p class="text-sm text-gray-700">من <?php echo e($reportStartDate->format('Y/m/d')); ?> إلى <?php echo e($reportEndDate->format('Y/m/d')); ?></p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 no-print">
        <div class="px-6 py-5 text-white text-center" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>cc 100%);">
            <h2 class="text-xl font-bold"><?php echo $__env->make('accounting.partials.company-name', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></h2>
            <h3 class="text-base font-semibold mt-1 opacity-95">قائمة التدفق النقدي</h3>
            <p class="text-sm opacity-90 mt-1">من <?php echo e($reportStartDate->format('Y/m/d')); ?> إلى <?php echo e($reportEndDate->format('Y/m/d')); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <?php
            $sections = [
                ['title' => 'الأنشطة التشغيلية', 'amount' => $operatingCashFlow, 'bg' => 'bg-blue-50 text-blue-900', 'totalBg' => 'bg-blue-50 border-blue-100 text-blue-800'],
                ['title' => 'الأنشطة الاستثمارية', 'amount' => $investingCashFlow, 'bg' => 'bg-purple-50 text-purple-900', 'totalBg' => 'bg-purple-50 border-purple-100 text-purple-800'],
                ['title' => 'الأنشطة التمويلية', 'amount' => $financingCashFlow, 'bg' => 'bg-green-50 text-green-900', 'totalBg' => 'bg-green-50 border-green-100 text-green-800'],
            ];
        ?>

        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border-b border-gray-100 last:border-b-0">
            <div class="<?php echo e($sectionHeader); ?> <?php echo e($section['bg']); ?>"><?php echo e($section['title']); ?></div>
            <div class="p-5 sm:p-6">
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-gray-700">صافي التدفق</span>
                    <span class="font-bold tabular-nums <?php echo e($section['amount'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        <?php echo e($section['amount'] >= 0 ? '+' : '-'); ?><?php echo e($money(abs($section['amount']))); ?>

                    </span>
                </div>
                <div class="flex justify-between py-3 mt-3 px-4 rounded-xl border <?php echo e($section['totalBg']); ?>">
                    <span class="font-bold">صافي <?php echo e($section['title']); ?></span>
                    <span class="font-bold tabular-nums <?php echo e($section['amount'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        <?php echo e($section['amount'] >= 0 ? '+' : '-'); ?><?php echo e($money(abs($section['amount']))); ?>

                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="p-5 sm:p-6" style="background: <?php echo e($themeColor); ?>08;">
            <div class="space-y-3">
                <div class="flex justify-between py-2">
                    <span class="font-bold text-gray-900">صافي التغير في النقدية</span>
                    <span class="font-bold tabular-nums <?php echo e($netCashFlow >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        <?php echo e($netCashFlow >= 0 ? '+' : '-'); ?><?php echo e($money(abs($netCashFlow))); ?>

                    </span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-200 text-sm">
                    <span class="text-gray-600">النقدية في بداية الفترة</span>
                    <span class="font-bold tabular-nums"><?php echo e($money($beginningCash)); ?></span>
                </div>
                <div class="flex justify-between py-4 px-4 rounded-xl text-white font-bold"
                     style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                    <span>النقدية في نهاية الفترة</span>
                    <span class="text-lg tabular-nums"><?php echo e($money($endingCash)); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('accounting.partials.report-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\reports\cash-flow.blade.php ENDPATH**/ ?>
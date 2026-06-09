
<?php $__env->startSection('page-title', 'التقارير المالية'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.context', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'التقارير المالية',
    'subtitle' => 'تقارير مالية شاملة — ميزانية، دخل، تدفق نقدي',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />',
    'actionUrl' => route('accounting.index'),
    'actionLabel' => 'لوحة المحاسبة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('accounting.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $reports = [
        ['route' => 'accounting.reports.balance-sheet', 'title' => 'الميزانية العمومية', 'desc' => 'الأصول والخصوم وحقوق الملكية', 'accent' => 'blue', 'badge' => 'أساسي'],
        ['route' => 'accounting.reports.income-statement', 'title' => 'قائمة الدخل', 'desc' => 'الإيرادات والمصروفات وصافي الدخل', 'accent' => 'green', 'badge' => 'أساسي'],
        ['route' => 'accounting.reports.trial-balance', 'title' => 'ميزان المراجعة', 'desc' => 'أرصدة الحسابات مدين ودائن', 'accent' => 'purple', 'badge' => 'تحليلي'],
        ['route' => 'accounting.reports.cash-flow', 'title' => 'التدفق النقدي', 'desc' => 'حركة النقد التشغيلية والاستثمارية', 'accent' => 'amber', 'badge' => 'تحليلي'],
    ];
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route($report['route'])); ?>" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 hover:shadow-xl transition-all font-tajawal group">
        <div class="flex items-start justify-between mb-3">
            <h3 class="font-bold text-gray-900 text-lg group-hover:underline"><?php echo e($report['title']); ?></h3>
            <span class="text-xs font-bold px-2 py-1 rounded-lg bg-gray-100 text-gray-600"><?php echo e($report['badge']); ?></span>
        </div>
        <p class="text-sm text-gray-600 mb-4"><?php echo e($report['desc']); ?></p>
        <span class="text-sm font-bold" style="color:<?php echo e($themeColor); ?>">عرض التقرير ←</span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\reports\index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('page-title', 'تقارير النظام'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<div class="w-full font-tajawal">
    <?php echo $__env->make('crm.partials.page-header', [
        'title' => 'تقارير النظام الشاملة',
        'subtitle' => 'استعرض التقارير التفصيلية وصدّرها إلى Excel بتنسيق احترافي',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-6 rounded-2xl border p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
         style="background: <?php echo e($themeColor); ?>08; border-color: <?php echo e($themeColor); ?>25;">
        <p class="text-sm text-gray-700">تشمل تقارير CRM والموارد البشرية والمشاريع العقارية والتعويضات — مع إمكانية التصفية بالتاريخ وتصدير ملف Excel.</p>
        <a href="<?php echo e(route('reports.index')); ?>" class="text-sm font-semibold whitespace-nowrap" style="color: <?php echo e($themeColor); ?>">التقارير التقليدية (HR) ←</a>
    </div>

    <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="mb-10">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-1.5 h-6 rounded-full" style="background: <?php echo e($themeColor); ?>"></span>
            <?php echo e($group['label']); ?>

        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <?php $__currentLoopData = $group['reports']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 flex-1">
                    <h3 class="text-base font-bold text-gray-900 mb-1"><?php echo e($report['title']); ?></h3>
                    <p class="text-sm text-gray-600 leading-relaxed"><?php echo e($report['description']); ?></p>
                    <?php if($report['supports_date_filter'] ?? false): ?>
                    <span class="inline-block mt-3 text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">فلترة بالتاريخ</span>
                    <?php endif; ?>
                </div>
                <div class="p-4 bg-gray-50 flex gap-2">
                    <a href="<?php echo e(route('admin.system-reports.show', $key)); ?>"
                       class="flex-1 text-center py-2.5 rounded-xl text-white text-sm font-semibold"
                       style="background: <?php echo e($themeColor); ?>">عرض</a>
                    <a href="<?php echo e(route('admin.system-reports.export', $key)); ?>"
                       class="px-4 py-2.5 rounded-xl border-2 text-sm font-semibold hover:bg-white transition-colors"
                       style="border-color: <?php echo e($themeColor); ?>; color: <?php echo e($themeColor); ?>"
                       title="تصدير Excel">
                        Excel
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/admin/system-reports/index.blade.php ENDPATH**/ ?>
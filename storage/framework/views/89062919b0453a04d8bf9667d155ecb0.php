

<?php $__env->startSection('page-title', 'عرض تقرير'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-6xl mx-auto">
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">تقرير القسم</h1>
            <p class="text-gray-600">
                الحالة:
                <?php if($report->status === 'submitted'): ?>
                    <span class="font-bold text-green-700">مُرسل</span>
                <?php else: ?>
                    <span class="font-bold text-gray-800">مسودة</span>
                <?php endif; ?>
                <span class="text-gray-300 mx-2">|</span>
                تاريخ الإنشاء: <?php echo e($report->created_at?->format('Y-m-d H:i')); ?>

            </p>
        </div>
        <a href="<?php echo e(route('department-manager.reports.index')); ?>" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 shadow-sm">
            رجوع
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <h2 class="text-lg font-extrabold text-gray-900 mb-4">الملخص</h2>
                <div class="text-gray-700 whitespace-pre-wrap leading-relaxed"><?php echo e($report->summary); ?></div>
            </div>

            <?php if(is_array($report->kpis) && count($report->kpis)): ?>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                    <h2 class="text-lg font-extrabold text-gray-900 mb-4">KPIs</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $report->kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="text-xs font-bold text-gray-500 uppercase"><?php echo e($key); ?></div>
                                <div class="text-xl font-extrabold text-gray-900 mt-2"><?php echo e(is_scalar($value) ? $value : json_encode($value)); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-extrabold text-gray-900 mb-4">معلومات</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">القسم</span>
                        <span class="font-bold text-gray-900"><?php echo e($report->department?->name ?? '—'); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">المشروع</span>
                        <span class="font-bold text-gray-900"><?php echo e($report->project?->name ?? '—'); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">المنشئ</span>
                        <span class="font-bold text-gray-900"><?php echo e($report->creator?->name ?? '—'); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">الفترة</span>
                        <span class="font-bold text-gray-900">
                            <?php if($report->period_start || $report->period_end): ?>
                                <?php echo e($report->period_start?->format('Y-m-d') ?? '—'); ?> → <?php echo e($report->period_end?->format('Y-m-d') ?? '—'); ?>

                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if(is_array($report->attachments) && count($report->attachments)): ?>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-extrabold text-gray-900 mb-4">المرفقات</h3>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $report->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="block text-blue-600 hover:underline text-sm"
                               href="<?php echo e(asset('storage/' . ($file['path'] ?? ''))); ?>"
                               target="_blank" rel="noopener">
                                <?php echo e($file['name'] ?? 'ملف'); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\department-reports\manager\show.blade.php ENDPATH**/ ?>
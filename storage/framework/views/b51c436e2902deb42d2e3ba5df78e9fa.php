

<?php $__env->startSection('page-title', 'تقويم المواعيد'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">المواعيد المؤكدة</h1>
            <p class="text-gray-600 text-sm">اجتماعات تم تأكيدها من الإدارة مع التاريخ والرابط إن وُجد.</p>
        </div>
        <a href="<?php echo e(route('client.dashboard')); ?>" class="px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">العودة</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-xs font-mono text-gray-500 mb-1"><?php echo e($m->reference_code); ?></p>
                    <h2 class="text-lg font-bold text-gray-900"><?php echo e($m->title); ?></h2>
                    <p class="text-sm text-gray-600 mt-1"><?php echo e($m->scheduled_at?->format('l، Y/m/d — H:i')); ?></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if($m->meeting_link): ?>
                        <a href="<?php echo e($m->meeting_link); ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">رابط الاجتماع</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('client.meeting-requests.show', $m)); ?>" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-800 hover:bg-gray-50">تفاصيل الطلب</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center text-gray-500">لا توجد مواعيد مؤكدة حالياً.</div>
        <?php endif; ?>
    </div>
    <div class="mt-6"><?php echo e($meetings->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\calendar.blade.php ENDPATH**/ ?>
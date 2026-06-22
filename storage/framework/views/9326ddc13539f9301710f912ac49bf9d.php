<?php $__env->startSection('page-title', 'بلاغات مشاكل الموقع'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">بلاغات مشاكل الموقع</h1>
            <p class="text-gray-600 text-sm sm:text-base">سجّل مشكلة واجهتها في الموقع أو البوابة مع إمكانية إرفاق لقطات شاشة. يتابعها فريق الدعم ويحدّث الحالة.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('client.website-issues.create')); ?>" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">بلاغ جديد</a>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition text-sm">العودة</a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm font-medium">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">المرجع</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">العنوان</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">الحالة</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">التاريخ</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 min-w-[8rem]">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono font-semibold text-gray-900"><?php echo e($issue->reference_code); ?></td>
                            <td class="px-6 py-4 text-gray-900"><?php echo e($issue->title); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                    <?php if($issue->status === 'open'): ?> bg-red-100 text-red-800
                                    <?php elseif($issue->status === 'in_progress'): ?> bg-amber-100 text-amber-900
                                    <?php elseif($issue->status === 'resolved'): ?> bg-green-100 text-green-800
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php echo e($issue->status_label); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap"><?php echo e($issue->created_at->format('Y/m/d')); ?></td>
                            <td class="px-6 py-4 text-left">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="<?php echo e(route('client.website-issues.show', $issue)); ?>" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">عرض</a>
                                    <?php if($issue->status === 'open'): ?>
                                        <form action="<?php echo e(route('client.website-issues.destroy', $issue)); ?>" method="POST" class="inline"
                                              onsubmit="return confirm('حذف هذا البلاغ نهائياً؟ لا يمكن التراجع.');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">حذف</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">لا توجد بلاغات بعد. استخدم «بلاغ جديد» عند مواجهة أي مشكلة.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($issues->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <?php echo e($issues->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\website-issues\index.blade.php ENDPATH**/ ?>
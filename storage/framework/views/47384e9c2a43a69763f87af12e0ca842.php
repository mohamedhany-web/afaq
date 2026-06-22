

<?php $__env->startSection('page-title', 'مشاريع العميل'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">المشاريع</h1>
                <p class="text-gray-600 text-sm sm:text-base">مشاريع العميل: <?php echo e($client->name); ?></p>
            </div>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-all duration-200 text-sm">العودة</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">المشروع</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">التقدم</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">تاريخ البدء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900"><?php echo e($project->name); ?></div>
                                <div class="text-sm text-gray-500 line-clamp-1"><?php echo e($project->description); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                    <?php if($project->status === 'planning'): ?> bg-amber-100 text-amber-900
                                    <?php elseif($project->status === 'in_progress'): ?> bg-blue-100 text-blue-900
                                    <?php elseif($project->status === 'completed'): ?> bg-green-100 text-green-800
                                    <?php elseif($project->status === 'on_hold'): ?> bg-orange-100 text-orange-900
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php echo e($project->status_name); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($project->progress_percentage ?? 0); ?>%</td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e(optional($project->start_date)->format('Y/m/d') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">لا توجد مشاريع.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($projects->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\projects.blade.php ENDPATH**/ ?>
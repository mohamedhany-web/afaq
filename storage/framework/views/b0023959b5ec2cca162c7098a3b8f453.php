

<?php $__env->startSection('page-title', 'تقرير الأداء'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="<?php echo e(route('reports.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">تقرير الأداء</h1>
        </div>
        <p class="text-gray-600">من <?php echo e($start_date); ?> إلى <?php echo e($end_date); ?></p>
    </div>

    <!-- Performance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">القسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">معدل الحضور</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المهام المكتملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التقييم</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $employeeMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo e($metric['name']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($metric['department']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-20 bg-gray-200 rounded-full h-2 ml-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo e($metric['attendance_rate']); ?>%"></div>
                                </div>
                                <span class="font-medium"><?php echo e($metric['attendance_rate']); ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($metric['tasks_completed']); ?></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ممتاز
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\performance.blade.php ENDPATH**/ ?>
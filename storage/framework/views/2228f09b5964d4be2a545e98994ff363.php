<?php $__env->startSection('page-title', 'تقرير الأقسام'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('reports.index')); ?>" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">تقرير الأقسام</h1>
            </div>
            <a href="<?php echo e(route('reports.departments.print')); ?>" target="_blank" class="bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-900 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة التقرير
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الأقسام</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo e($summary['total_departments']); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الموظفين</p>
            <p class="text-3xl font-bold text-blue-600"><?php echo e($summary['total_employees']); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المشاريع</p>
            <p class="text-3xl font-bold text-green-600"><?php echo e($summary['total_projects']); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">متوسط الموظفين</p>
            <p class="text-3xl font-bold text-purple-600"><?php echo e($summary['average_employees']); ?></p>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم القسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الموظفين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد المشاريع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo e($department->name); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo e($department->manager ? $department->manager->first_name . ' ' . $department->manager->last_name : '-'); ?>

                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?php echo e($department->employees_count); ?> موظف
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?php echo e($department->projects_count); ?> مشروع
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="<?php echo e(route('departments.show', $department)); ?>" class="text-blue-600 hover:text-blue-900">
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\departments.blade.php ENDPATH**/ ?>
<?php $__env->startSection('page-title', 'الأقسام'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">الأقسام</h1>
                <p class="text-sm sm:text-base text-gray-600">إدارة أقسام الشركة والموظفين والمشاريع</p>
            </div>
            <a href="<?php echo e(route('departments.create')); ?>" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden sm:inline">إضافة قسم جديد</span>
                <span class="sm:hidden">قسم جديد</span>
            </a>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200">
            <!-- Department Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 sm:p-5 lg:p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 sm:p-3 bg-white rounded-lg shadow-sm flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-xs font-medium <?php echo e($department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo e($department->is_active ? 'نشط' : 'غير نشط'); ?>

                    </span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1"><?php echo e($department->name); ?></h3>
                <p class="text-xs sm:text-sm text-gray-600"><?php echo e($department->code); ?></p>
            </div>

            <!-- Department Info -->
            <div class="p-4 sm:p-5 lg:p-6">
                <!-- Manager -->
                <?php if($department->manager): ?>
                <div class="flex items-center mb-4 p-3 bg-blue-50 rounded-lg">
                    <div class="p-2 bg-blue-100 rounded-lg ml-2 sm:ml-3 flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-600">مدير القسم</p>
                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($department->manager->first_name); ?> <?php echo e($department->manager->last_name); ?></p>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex items-center mb-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs sm:text-sm text-gray-500">لم يتم تعيين مدير للقسم</p>
                </div>
                <?php endif; ?>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4">
                    <div class="text-center p-2.5 sm:p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl sm:text-2xl font-bold text-blue-600"><?php echo e($department->employees_count); ?></p>
                        <p class="text-xs text-gray-600 mt-1">موظف</p>
                    </div>
                    <div class="text-center p-2.5 sm:p-3 bg-gray-50 rounded-lg">
                        <p class="text-xl sm:text-2xl font-bold text-green-600"><?php echo e($department->projects_count); ?></p>
                        <p class="text-xs text-gray-600 mt-1">مشروع</p>
                    </div>
                </div>

                <!-- Description -->
                <?php if($department->description): ?>
                <p class="text-xs sm:text-sm text-gray-600 mb-4 line-clamp-2"><?php echo e($department->description); ?></p>
                <?php endif; ?>

                <!-- Contact Info -->
                <?php if($department->phone || $department->email): ?>
                <div class="border-t pt-4 space-y-2">
                    <?php if($department->phone): ?>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <?php echo e($department->phone); ?>

                    </div>
                    <?php endif; ?>
                    <?php if($department->email): ?>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <?php echo e($department->email); ?>

                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t">
                    <a href="<?php echo e(route('departments.show', $department)); ?>" class="text-center px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-xs sm:text-sm font-medium">
                        عرض
                    </a>
                    <a href="<?php echo e(route('departments.edit', $department)); ?>" class="text-center px-3 sm:px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-xs sm:text-sm font-medium">
                        تعديل
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-lg font-medium text-gray-900 mb-2">لا توجد أقسام</p>
                <p class="text-gray-600 mb-4">ابدأ بإضافة أول قسم للشركة</p>
                <a href="<?php echo e(route('departments.create')); ?>" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة قسم جديد
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Summary Stats -->
    <?php if($departments->count() > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الأقسام</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($departments->count()); ?></p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الموظفين</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($departments->sum('employees_count')); ?></p>
                </div>
                <div class="p-3 bg-green-50 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المشاريع</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($departments->sum('projects_count')); ?></p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">الأقسام النشطة</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($departments->where('is_active', true)->count()); ?></p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\departments\index.blade.php ENDPATH**/ ?>
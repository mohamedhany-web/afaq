<?php $__env->startSection('page-title', 'تفاصيل القسم - ' . $department->name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                    <a href="<?php echo e(route('departments.index')); ?>" class="text-gray-600 hover:text-gray-900 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate"><?php echo e($department->name); ?></h1>
                    <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-medium flex-shrink-0 <?php echo e($department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo e($department->is_active ? 'نشط' : 'غير نشط'); ?>

                    </span>
                </div>
                <p class="text-sm sm:text-base text-gray-600"><?php echo e($department->code); ?></p>
            </div>
            <a href="<?php echo e(route('departments.edit', $department)); ?>" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span class="hidden sm:inline">تعديل القسم</span>
                <span class="sm:hidden">تعديل</span>
            </a>
        </div>
    </div>

    <!-- Department Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">إجمالي الموظفين</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($stats['total_employees']); ?></p>
                </div>
                <div class="hidden sm:block p-3 bg-blue-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">المشاريع النشطة</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($stats['active_projects']); ?></p>
                    <p class="text-xs text-gray-500 mt-1">من <?php echo e($stats['active_projects'] + $stats['completed_projects']); ?> مشروع</p>
                </div>
                <div class="hidden sm:block p-3 bg-green-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">الوحدات المتاحة</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($stats['available_units']); ?></p>
                    <p class="text-xs text-gray-500 mt-1">من <?php echo e($stats['total_units']); ?> وحدة</p>
                </div>
                <div class="hidden sm:block p-3 bg-orange-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">ميزانية المشاريع</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_budget'], 0)); ?> ج.م</p>
                </div>
                <div class="hidden sm:block p-3 bg-purple-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Department Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">معلومات القسم</h3>
                
                <?php if($department->description): ?>
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-600 mb-2">الوصف:</p>
                    <p class="text-gray-700"><?php echo e($department->description); ?></p>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4">
                    <?php if($department->location): ?>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">الموقع:</p>
                        <p class="text-gray-900"><?php echo e($department->location); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($department->phone): ?>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">الهاتف:</p>
                        <p class="text-gray-900"><?php echo e($department->phone); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($department->email): ?>
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">البريد الإلكتروني:</p>
                        <p class="text-gray-900"><?php echo e($department->email); ?></p>
                    </div>
                    <?php endif; ?>

                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">متوسط الراتب:</p>
                        <p class="text-gray-900"><?php echo e(number_format($stats['average_salary'], 0)); ?> ج.م</p>
                    </div>
                </div>
            </div>

            <!-- Employees List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">الموظفون (<?php echo e($department->employees->count()); ?>)</h3>
                </div>
                
                <?php if($department->employees->count() > 0): ?>
                <div class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $department->employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg ml-3">
                                    <?php echo e(substr($employee->first_name, 0, 1)); ?><?php echo e(substr($employee->last_name, 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900"><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo e($employee->position); ?></p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-900"><?php echo e(number_format($employee->salary, 0)); ?> ج.م</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e($employee->status === 'active' ? 'نشط' : 'غير نشط'); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="p-8 text-center text-gray-500">
                    <p>لا يوجد موظفون في هذا القسم</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Projects List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 sm:px-5 lg:px-6 py-3 sm:py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">المشاريع (<?php echo e($department->projects->count()); ?>)</h3>
                </div>
                
                <?php if($department->projects->count() > 0): ?>
                <div class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $department->projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-gray-900"><?php echo e($project->name); ?></h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-<?php echo e($project->status_color); ?>-100 text-<?php echo e($project->status_color); ?>-800">
                                        <?php echo e($project->status); ?>

                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2"><?php echo e(Str::limit($project->description, 100)); ?></p>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <?php echo e($project->client ? $project->client->name : 'لا يوجد عميل'); ?>

                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <?php echo e($project->listing_status_name ?? '—'); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="text-left mr-4">
                                <p class="text-lg font-bold text-gray-900"><?php echo e(number_format($project->budget, 0)); ?> ج.م</p>
                                <p class="text-xs text-gray-500">الميزانية</p>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($project->occupancy_percent); ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($project->occupancy_percent); ?>% مباع</p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="p-8 text-center text-gray-500">
                    <p>لا توجد مشاريع في هذا القسم</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Department Manager -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    مدير القسم
                </h3>
                
                <?php if($department->manager): ?>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto mb-3">
                        <?php echo e(substr($department->manager->first_name, 0, 1)); ?><?php echo e(substr($department->manager->last_name, 0, 1)); ?>

                    </div>
                    <h4 class="font-bold text-gray-900 mb-1"><?php echo e($department->manager->first_name); ?> <?php echo e($department->manager->last_name); ?></h4>
                    <p class="text-sm text-gray-600 mb-3"><?php echo e($department->manager->position); ?></p>
                    
                    <div class="border-t pt-3 space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <?php echo e($department->manager->email); ?>

                        </div>
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <?php echo e($department->manager->phone); ?>

                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <p class="text-gray-500 mb-3">لم يتم تعيين مدير للقسم</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 lg:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">إجراءات سريعة</h3>
                <div class="space-y-2">
                    <a href="<?php echo e(route('departments.edit', $department)); ?>" class="flex items-center p-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 ml-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        تعديل معلومات القسم
                    </a>
                    
                    <a href="<?php echo e(route('employees.index')); ?>?department=<?php echo e($department->id); ?>" class="flex items-center p-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 ml-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        إضافة موظف جديد
                    </a>
                    
                    <a href="<?php echo e(route('crm.projects.create')); ?>?department=<?php echo e($department->id); ?>" class="flex items-center p-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 ml-3 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        إضافة مشروع جديد
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\departments\show.blade.php ENDPATH**/ ?>
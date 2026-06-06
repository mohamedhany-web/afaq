<?php $__env->startSection('page-title', 'إدارة المستخدمين'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">إدارة المستخدمين</h1>
                <p class="text-sm sm:text-base text-gray-600">إدارة المستخدمين والصلاحيات في النظام</p>
            </div>
            <a href="<?php echo e(route('users.create')); ?>" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden sm:inline">إضافة مستخدم جديد</span>
                <span class="sm:hidden">مستخدم جديد</span>
            </a>
        </div>
    </div>

<!-- Quick Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">إجمالي المستخدمين</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900"><?php echo e($users->total()); ?></p>
            </div>
            <div class="hidden sm:block p-3 bg-blue-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">مستخدمين نشطين</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900"><?php echo e($users->where('email_verified_at', '!=', null)->count()); ?></p>
            </div>
            <div class="hidden sm:block p-3 bg-green-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Pending Verification -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">في انتظار التأكيد</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900"><?php echo e($users->where('email_verified_at', null)->count()); ?></p>
            </div>
            <div class="hidden sm:block p-3 bg-yellow-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Admins -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2">المديرين</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900"><?php echo e($users->filter(function($user) { return $user->hasRole('admin'); })->count()); ?></p>
            </div>
            <div class="hidden sm:block p-3 bg-purple-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900">قائمة المستخدمين</h3>
    </div>
    
    <!-- Mobile Cards View -->
    <div class="block lg:hidden">
        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="p-4 border-b border-gray-200 hover:bg-gray-50">
            <div class="flex items-start gap-3 mb-3">
                <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-base font-medium text-white"><?php echo e(substr($user->name, 0, 1)); ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($user->name); ?></p>
                    <p class="text-xs text-gray-600 truncate"><?php echo e($user->email); ?></p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <?php if($user->roles->count() > 0): ?>
                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e($role->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <a href="<?php echo e(route('users.show', $user)); ?>" class="px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-xs text-center font-medium">
                    عرض
                </a>
                <a href="<?php echo e(route('users.edit', $user)); ?>" class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-xs text-center font-medium">
                    تعديل
                </a>
                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors text-xs font-medium">
                        حذف
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="p-8 text-center text-gray-500">
            <p>لا يوجد مستخدمين</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto w-full">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الصلاحيات</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الإنشاء</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 w-1/5">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3">
                                    <span class="text-sm font-medium text-white"><?php echo e(substr($user->name, 0, 1)); ?></span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></div>
                                    <div class="text-sm text-gray-500">ID: <?php echo e($user->id); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/4">
                            <div class="text-sm text-gray-900"><?php echo e($user->email); ?></div>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <?php if($user->roles->count() > 0): ?>
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($role->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="text-sm text-gray-500">لا توجد صلاحيات</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-500">
                            <?php echo e($user->created_at->format('Y/m/d')); ?>

                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('users.show', $user)); ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-xs font-medium">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    عرض
                                </a>
                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-xs font-medium">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    تعديل
                                </a>
                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors text-xs font-medium">
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">لا يوجد مستخدمين</h3>
                        <p class="mt-1 text-sm text-gray-500">ابدأ بإنشاء مستخدم جديد.</p>
                        <div class="mt-6">
                            <a href="<?php echo e(route('users.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                إضافة مستخدم جديد
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if($users->hasPages()): ?>
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                عرض <span class="font-medium"><?php echo e($users->firstItem()); ?></span> إلى <span class="font-medium"><?php echo e($users->lastItem()); ?></span> من <span class="font-medium"><?php echo e($users->total()); ?></span> نتيجة
            </div>
            <div>
                <?php echo e($users->links()); ?>

            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/users/index.blade.php ENDPATH**/ ?>
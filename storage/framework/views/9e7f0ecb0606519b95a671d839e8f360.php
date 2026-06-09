<?php $__env->startSection('page-title', 'تفاصيل المستخدم'); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="h-14 w-14 sm:h-16 sm:w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                <span class="text-xl sm:text-2xl font-bold text-white"><?php echo e(substr($user->name, 0, 1)); ?></span>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1 truncate"><?php echo e($user->name); ?></h1>
                <p class="text-sm sm:text-base text-gray-600 truncate"><?php echo e($user->email); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="<?php echo e(route('users.edit', $user)); ?>" class="flex-1 sm:flex-none bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm">
                <svg class="h-4 w-4 ml-1 sm:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                تعديل
            </a>
            <a href="<?php echo e(route('users.index')); ?>" class="flex-1 sm:flex-none bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-gray-300 shadow-sm text-sm">
                العودة
            </a>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        <!-- Content -->
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">الاسم الكامل</span>
                                <span class="text-sm text-gray-900"><?php echo e($user->name); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">البريد الإلكتروني</span>
                                <span class="text-sm text-gray-900"><?php echo e($user->email); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">تاريخ الإنشاء</span>
                                <span class="text-sm text-gray-900"><?php echo e($user->created_at->format('Y/m/d H:i')); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-500">آخر تحديث</span>
                                <span class="text-sm text-gray-900"><?php echo e($user->updated_at->format('Y/m/d H:i')); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">الصلاحيات</h2>
                        <div class="space-y-2">
                            <?php if($user->roles->count() > 0): ?>
                                <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($role->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">لا توجد صلاحيات محددة</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">الصلاحيات المباشرة</h2>
                        <div class="space-y-2">
                            <?php if($user->permissions->count() > 0): ?>
                                <?php $__currentLoopData = $user->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <?php echo e($permission->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">لا توجد صلاحيات مباشرة</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Activity -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">النشاط الأخير</h2>
                        <div class="space-y-3">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="h-2 w-2 bg-green-500 rounded-full ml-3"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">تم إنشاء الحساب</p>
                                    <p class="text-xs text-gray-500"><?php echo e($user->created_at->diffForHumans()); ?></p>
                                </div>
                            </div>
                            <?php if($user->updated_at != $user->created_at): ?>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="h-2 w-2 bg-blue-500 rounded-full ml-3"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">آخر تحديث</p>
                                    <p class="text-xs text-gray-500"><?php echo e($user->updated_at->diffForHumans()); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\users\show.blade.php ENDPATH**/ ?>
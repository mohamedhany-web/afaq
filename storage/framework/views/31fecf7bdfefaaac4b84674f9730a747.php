<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-6">
    <div class="w-full max-w-full">
        <!-- Header -->
        <div class="mb-6">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6" style="border-color: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>30;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-xl shadow-lg" style="background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>dd 100%);">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 font-tajawal">نظام المراقبة الشامل</h1>
                            <p class="text-sm text-gray-600 font-tajawal">مراقبة جميع العمليات والأنشطة في النظام</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-tajawal">إجمالي الأنشطة</p>
                        <p class="text-2xl font-bold text-gray-900 font-tajawal"><?php echo e(number_format($stats['total_activities'])); ?></p>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-tajawal">أنشطة اليوم</p>
                        <p class="text-2xl font-bold text-gray-900 font-tajawal"><?php echo e(number_format($stats['today_activities'] + $stats['today_login_activities'])); ?></p>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-tajawal">تسجيلات دخول ناجحة</p>
                        <p class="text-2xl font-bold text-green-600 font-tajawal"><?php echo e(number_format($stats['successful_logins'])); ?></p>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-tajawal">محاولات فاشلة</p>
                        <p class="text-2xl font-bold text-red-600 font-tajawal"><?php echo e(number_format($stats['failed_logins'])); ?></p>
                    </div>
                    <div class="p-3 rounded-lg bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <form method="GET" action="<?php echo e(route('system-monitoring.index')); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">نوع النشاط</label>
                        <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all" <?php echo e($filters['type'] === 'all' ? 'selected' : ''); ?>>الكل</option>
                            <option value="activity" <?php echo e($filters['type'] === 'activity' ? 'selected' : ''); ?>>أنشطة النظام</option>
                            <option value="login" <?php echo e($filters['type'] === 'login' ? 'selected' : ''); ?>>تسجيل الدخول</option>
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">نوع العملية</label>
                        <select name="action" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all" <?php echo e($filters['action'] === 'all' ? 'selected' : ''); ?>>الكل</option>
                            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($action); ?>" <?php echo e($filters['action'] === $action ? 'selected' : ''); ?>><?php echo e($action); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- User Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">المستخدم</label>
                        <select name="user_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">الكل</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e($filters['user_id'] == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">الحالة</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all" <?php echo e($filters['status'] === 'all' ? 'selected' : ''); ?>>الكل</option>
                            <option value="success" <?php echo e($filters['status'] === 'success' ? 'selected' : ''); ?>>نجح</option>
                            <option value="failed" <?php echo e($filters['status'] === 'failed' ? 'selected' : ''); ?>>فشل</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">من تاريخ</label>
                        <input type="date" name="date_from" value="<?php echo e($filters['date_from']); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">إلى تاريخ</label>
                        <input type="date" name="date_to" value="<?php echo e($filters['date_to']); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- IP Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 font-tajawal">عنوان IP</label>
                        <input type="text" name="ip_address" value="<?php echo e($filters['ip_address']); ?>" placeholder="192.168.1.1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-tajawal" style="background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>dd 100%);">
                        تصفية
                    </button>
                    <a href="<?php echo e(route('system-monitoring.index')); ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-tajawal">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <!-- Activities Table -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-tajawal">المستخدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-tajawal">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-tajawal">العملية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-tajawal">الوصف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-tajawal">IP</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider font-tajawal">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $paginatedActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php if($activity['user'] && $activity['user']->profile_picture): ?>
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?php echo e(asset('storage/' . $activity['user']->profile_picture)); ?>" alt="">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: linear-gradient(135deg, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?> 0%, <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>dd 100%);">
                                                    <?php echo e(substr($activity['user']->name ?? 'N', 0, 1)); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900 font-tajawal"><?php echo e($activity['user']->name ?? 'غير معروف'); ?></div>
                                            <div class="text-sm text-gray-500 font-tajawal"><?php echo e($activity['user']->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($activity['type'] === 'activity' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'); ?> font-tajawal">
                                        <?php echo e($activity['type'] === 'activity' ? 'نشاط' : 'تسجيل دخول'); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $colorClass = match($activity['color']) {
                                            'green' => 'bg-green-100 text-green-800',
                                            'blue' => 'bg-blue-100 text-blue-800',
                                            'red' => 'bg-red-100 text-red-800',
                                            'yellow' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($colorClass); ?> font-tajawal">
                                        <?php echo e($activity['action_name']); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-tajawal"><?php echo e($activity['description']); ?></div>
                                    <?php if(isset($activity['model_name'])): ?>
                                        <div class="text-xs text-gray-500 font-tajawal">نوع: <?php echo e($activity['model_name']); ?></div>
                                    <?php endif; ?>
                                    <?php if(isset($activity['related_code'])): ?>
                                        <div class="text-xs text-gray-500 font-tajawal">كود: <?php echo e(substr($activity['related_code'], 0, 2)); ?>****</div>
                                    <?php endif; ?>
                                    <?php if(isset($activity['target_email'])): ?>
                                        <div class="text-xs text-gray-500 font-tajawal">إلى: <?php echo e($activity['target_email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    <?php echo e($activity['ip_address'] ?? 'غير متوفر'); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-tajawal">
                                    <?php echo e($activity['created_at']->format('Y/m/d H:i:s')); ?>

                                    <div class="text-xs text-gray-400"><?php echo e($activity['created_at']->diffForHumans()); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-tajawal">لا توجد أنشطة مسجلة</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <?php echo e($paginatedActivities->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\system-monitoring\index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('page-title', 'إدارة العملاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">إدارة العملاء</h1>
                <p class="text-sm sm:text-base text-gray-600">إدارة قاعدة بيانات العملاء والعلاقات التجارية</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-clients')): ?>
            <a href="<?php echo e(route('client-service-reports.index')); ?>" class="bg-violet-600 text-white px-4 py-2.5 sm:px-5 sm:py-3 rounded-lg hover:bg-violet-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                تقارير العملاء
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('clients.create')); ?>" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden sm:inline">عميل جديد</span>
                <span class="sm:hidden">جديد</span>
            </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Clients -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">إجمالي العملاء</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e(\App\Models\Client::count()); ?></p>
                </div>
                <div class="hidden sm:block p-3 sm:p-4 bg-blue-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Clients -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">عملاء نشطين</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(\App\Models\Client::where('status', 'active')->count()); ?></p>
                    <p class="text-xs text-green-600 mt-1"><?php echo e(\App\Models\Client::count() > 0 ? round((\App\Models\Client::where('status', 'active')->count() / \App\Models\Client::count()) * 100, 1) : 0); ?>% من إجمالي العملاء</p>
                </div>
                <div class="hidden sm:block p-3 sm:p-4 bg-green-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- New Clients This Month -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">عملاء جدد</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(\App\Models\Client::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()); ?></p>
                    <p class="text-xs text-purple-600 mt-1">هذا الشهر</p>
                </div>
                <div class="hidden sm:block p-3 sm:p-4 bg-purple-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue from Sales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الإيرادات</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(number_format(\App\Models\Sale::sumAmount())); ?> ج.م</p>
                    <p class="text-xs text-orange-600 mt-1">من جميع المبيعات</p>
                </div>
                <div class="hidden sm:block p-3 sm:p-4 bg-orange-50 rounded-lg flex-shrink-0 mt-2 sm:mt-0">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">قائمة العملاء</h3>
                <div class="flex items-center gap-2">
                    <input type="text" placeholder="البحث عن عميل..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>جميع العملاء</option>
                        <option>نشط</option>
                        <option>غير نشط</option>
                        <option>عميل جديد</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">العميل</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">الشركة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">الهاتف</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">تاريخ الإضافة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 w-1/6">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3">
                                    <span class="text-sm font-medium text-white"><?php echo e(substr($client->name, 0, 1)); ?></span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($client->name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($client->industry ?? 'غير محدد'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-900">
                            <?php echo e($client->company_name ?? 'غير محدد'); ?>

                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-900">
                            <?php echo e($client->email); ?>

                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-900">
                            <?php echo e($client->phone); ?>

                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-500">
                            <?php echo e($client->created_at->format('Y/m/d')); ?>

                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php if($client->status == 'active'): ?> bg-green-100 text-green-800
                                    <?php elseif($client->status == 'inactive'): ?> bg-gray-100 text-gray-800
                                    <?php elseif($client->status == 'suspended'): ?> bg-red-100 text-red-800
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php if($client->status == 'active'): ?> نشط
                                    <?php elseif($client->status == 'inactive'): ?> غير نشط
                                    <?php elseif($client->status == 'suspended'): ?> معلق
                                    <?php else: ?> <?php echo e($client->status); ?>

                                    <?php endif; ?>
                                </span>
                                <div class="flex gap-1">
                                    <a href="<?php echo e(route('clients.show', $client)); ?>" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        عرض
                                    </a>
                                    <a href="<?php echo e(route('clients.edit', $client)); ?>" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عملاء</h3>
                                <p class="text-gray-500 mb-4">ابدأ بإضافة عميل جديد لقاعدة البيانات</p>
                                <a href="<?php echo e(route('clients.create')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    إضافة عميل جديد
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\clients\index.blade.php ENDPATH**/ ?>
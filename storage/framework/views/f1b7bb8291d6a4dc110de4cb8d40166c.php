<?php $__env->startSection('page-title', 'لوحة التحكم المالية'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.context', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'لوحة المحاسبة والمالية',
    'subtitle' => 'نظرة شاملة على الوضع المالي — ' . \Carbon\Carbon::now()->locale('ar')->translatedFormat('F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />',
    'actionUrl' => route('accounting.journal-entries.create'),
    'actionLabel' => 'قيد محاسبي جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('accounting.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الأصول', 'value' => $money($totalAssets), 'accent' => 'green', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الخصوم', 'value' => $money($totalLiabilities), 'accent' => 'amber', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صافي الدخل', 'value' => $money(abs($netIncome)), 'accent' => $netIncome >= 0 ? 'green' : 'red', 'compact' => true, 'footer' => $netIncome >= 0 ? '<span class="text-green-600">ربح</span>' : '<span class="text-red-600">خسارة</span>', 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الرصيد المتاح', 'value' => $money($totalAssets - $totalLiabilities), 'accent' => 'blue', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="w-full">

    <!-- Monthly Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Revenue vs Expenses -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 font-tajawal">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg ml-3">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">أداء الشهر الحالي</h3>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-600">الإيرادات</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gradient-to-r from-red-500 to-red-600 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-600">المصروفات</span>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-5 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 rounded-lg ml-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-700 mb-1">إيرادات الشهر</p>
                            <p class="text-2xl font-bold text-green-900"><?php echo e($money($monthlyRevenue)); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-green-600 font-medium"><?php echo e($monthlyRevenue > 0 ? '+' : ''); ?><?php echo e(number_format(($monthlyRevenue / max($totalRevenue, 1)) * 100, 1)); ?>%</p>
                        <p class="text-xs text-gray-500">من الإجمالي</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-5 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-500 rounded-lg ml-4">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-700 mb-1">مصروفات الشهر</p>
                            <p class="text-2xl font-bold text-red-900"><?php echo e($money($monthlyExpenses)); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-red-600 font-medium"><?php echo e(number_format(($monthlyExpenses / max($totalExpenses, 1)) * 100, 1)); ?>%</p>
                        <p class="text-xs text-gray-500">من الإجمالي</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                    <span class="text-sm font-semibold text-blue-900">صافي أداء الشهر</span>
                    <span class="text-lg font-bold <?php echo e(($monthlyRevenue - $monthlyExpenses) >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        <?php echo e($money($monthlyRevenue - $monthlyExpenses)); ?>

                    </span>
                </div>
            </div>
        </div>

        <!-- Pending Items -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 font-tajawal">
            <div class="flex items-center mb-6">
                <div class="p-2 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg ml-3">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">معاملات معلقة</h3>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-500 rounded-lg ml-3">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-yellow-900">فواتير معلقة</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-yellow-900 mr-11"><?php echo e($money($pendingInvoices)); ?></p>
                </div>
                <div class="p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border border-orange-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="p-2 bg-orange-500 rounded-lg ml-3">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-orange-900">مدفوعات معلقة</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-orange-900 mr-11"><?php echo e($money($pendingPayments)); ?></p>
                </div>
                <div class="p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-purple-900">إجمالي المعلق</span>
                        <span class="text-lg font-bold text-purple-900"><?php echo e($money($pendingInvoices + $pendingPayments)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-8 font-tajawal">
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الأصول', 'value' => $money($totalAssets), 'accent' => 'green', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الخصوم', 'value' => $money($totalLiabilities), 'accent' => 'amber', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حقوق الملكية', 'value' => $money($totalEquity ?? 0), 'accent' => 'blue', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الإيرادات', 'value' => $money($totalRevenue ?? 0), 'accent' => 'purple', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المصروفات', 'value' => $money($totalExpenses ?? 0), 'accent' => 'red', 'compact' => true, 'href' => route('accounting.index') . '#page-data', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden font-tajawal">
        <div class="px-5 py-4 border-b flex justify-between items-center" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold">القيود المحاسبية الأخيرة</h3>
            <a href="<?php echo e(route('accounting.journal-entries')); ?>" class="text-xs font-bold" style="color:<?php echo e($themeColor); ?>">عرض الكل</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600">
                        <th class="p-4 text-right font-bold">التاريخ</th>
                        <th class="p-4 text-right font-bold">المرجع</th>
                        <th class="p-4 text-right font-bold">الوصف</th>
                        <th class="p-4 text-center font-bold">المجموع</th>
                        <th class="p-4 text-center font-bold">الحالة</th>
                        <th class="p-4 text-center font-bold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="p-2 bg-gray-100 rounded-lg ml-3">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4h8m-8 0H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-2" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900"><?php echo e($entry->date->format('Y-m-d')); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded"><?php echo e($entry->reference); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600"><?php echo e(Str::limit($entry->description, 50)); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900"><?php echo e($money($entry->total_debit)); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($entry->status_color); ?>">
                                <?php echo e($entry->status_in_arabic); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('accounting.journal-entries.show', $entry)); ?>" class="text-blue-600 hover:text-blue-800 font-semibold bg-blue-50 px-3 py-1 rounded-lg hover:bg-blue-100 transition-colors duration-200">عرض</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-100 rounded-full mb-4">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد قيود محاسبية</h3>
                                <p class="text-gray-600 mb-4">ابدأ بإنشاء قيد محاسبي جديد لإدارة معاملاتك المالية</p>
                                <a href="<?php echo e(route('accounting.journal-entries.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                    إنشاء قيد جديد
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\index.blade.php ENDPATH**/ ?>
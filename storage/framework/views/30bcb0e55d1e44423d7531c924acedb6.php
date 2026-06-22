

<?php $__env->startSection('page-title', 'تفاصيل عملية البيع'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تفاصيل عملية البيع</h1>
                <p class="text-gray-600">عرض تفاصيل عملية البيع: <?php echo e($sale->product_service); ?></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('sales.edit', $sale)); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل
                </a>
                <a href="<?php echo e(route('sales.index')); ?>" class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Sale Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo e($sale->product_service); ?></h2>
                        <p class="text-gray-600">للعميل: <?php echo e($sale->client->name); ?></p>
                    </div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        <?php if($sale->stage == 'lead'): ?> bg-gray-100 text-gray-800
                        <?php elseif($sale->stage == 'prospect'): ?> bg-blue-100 text-blue-800
                        <?php elseif($sale->stage == 'proposal'): ?> bg-yellow-100 text-yellow-800
                        <?php elseif($sale->stage == 'negotiation'): ?> bg-orange-100 text-orange-800
                        <?php elseif($sale->stage == 'closed_won'): ?> bg-green-100 text-green-800
                        <?php elseif($sale->stage == 'closed_lost'): ?> bg-red-100 text-red-800
                        <?php else: ?> bg-gray-100 text-gray-800
                        <?php endif; ?>">
                        <?php if($sale->stage == 'lead'): ?> عميل محتمل
                        <?php elseif($sale->stage == 'prospect'): ?> عميل مؤهل
                        <?php elseif($sale->stage == 'proposal'): ?> عرض سعر
                        <?php elseif($sale->stage == 'negotiation'): ?> مفاوضات
                        <?php elseif($sale->stage == 'closed_won'): ?> مكتمل - فائز
                        <?php elseif($sale->stage == 'closed_lost'): ?> مكتمل - خاسر
                        <?php else: ?> <?php echo e($sale->stage); ?>

                        <?php endif; ?>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sale Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات البيع</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">القيمة المتوقعة</p>
                                    <p class="text-sm text-gray-600"><?php echo e(number_format($sale->estimated_value)); ?> ج.م</p>
                                </div>
                            </div>
                            <?php if($sale->actual_value): ?>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">القيمة الفعلية</p>
                                    <p class="text-sm text-gray-600"><?php echo e(number_format($sale->actual_value)); ?> ج.م</p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">نسبة الاحتمال</p>
                                    <p class="text-sm text-gray-600"><?php echo e($sale->probability_percentage); ?>%</p>
                                </div>
                            </div>
                            <?php if($sale->lead_source): ?>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">مصدر العميل المحتمل</p>
                                    <p class="text-sm text-gray-600">
                                        <?php if($sale->lead_source == 'website'): ?> الموقع الإلكتروني
                                        <?php elseif($sale->lead_source == 'referral'): ?> إحالة
                                        <?php elseif($sale->lead_source == 'cold_call'): ?> مكالمة باردة
                                        <?php elseif($sale->lead_source == 'email'): ?> البريد الإلكتروني
                                        <?php elseif($sale->lead_source == 'social_media'): ?> وسائل التواصل الاجتماعي
                                        <?php elseif($sale->lead_source == 'advertisement'): ?> إعلان
                                        <?php elseif($sale->lead_source == 'event'): ?> فعالية
                                        <?php elseif($sale->lead_source == 'other'): ?> أخرى
                                        <?php else: ?> <?php echo e($sale->lead_source); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Timeline Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">الجدول الزمني</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">تاريخ الإنشاء</p>
                                    <p class="text-sm text-gray-600"><?php echo e($sale->created_at->format('Y/m/d H:i')); ?></p>
                                </div>
                            </div>
                            <?php if($sale->expected_close_date): ?>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">تاريخ الإغلاق المتوقع</p>
                                    <p class="text-sm text-gray-600"><?php echo e($sale->expected_close_date->format('Y/m/d')); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if($sale->actual_close_date): ?>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">تاريخ الإغلاق الفعلي</p>
                                    <p class="text-sm text-gray-600"><?php echo e($sale->actual_close_date->format('Y/m/d')); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">آخر تحديث</p>
                                    <p class="text-sm text-gray-600"><?php echo e($sale->updated_at->format('Y/m/d H:i')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($sale->competitors && count($sale->competitors) > 0): ?>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">المنافسون</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $sale->competitors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competitor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <?php echo e($competitor); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($sale->decision_makers && count($sale->decision_makers) > 0): ?>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">صانعو القرار</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $sale->decision_makers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo e($maker); ?>

                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($sale->notes): ?>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ملاحظات</h3>
                    <p class="text-sm text-gray-600 leading-relaxed"><?php echo e($sale->notes); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Related Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Client Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">معلومات العميل</h3>
                        <a href="<?php echo e(route('clients.show', $sale->client)); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                            عرض التفاصيل
                        </a>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3">
                                <span class="text-sm font-medium text-white"><?php echo e(substr($sale->client->name, 0, 1)); ?></span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($sale->client->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($sale->client->company_name ?? 'غير محدد'); ?></p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p><?php echo e($sale->client->email); ?></p>
                            <?php if($sale->client->phone): ?>
                                <p><?php echo e($sale->client->phone); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sales Rep Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">مندوب المبيعات</h3>
                        <?php if($sale->employee): ?>
                            <a href="<?php echo e(route('employees.show', $sale->employee)); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                عرض التفاصيل
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center ml-3">
                                <span class="text-sm font-medium text-white"><?php echo e(substr($sale->salesRep->name, 0, 1)); ?></span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($sale->salesRep->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($sale->employee->position ?? 'مندوب مبيعات'); ?></p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p><?php echo e($sale->salesRep->email); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">إحصائيات سريعة</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">القيمة المتوقعة</span>
                        <span class="text-sm font-medium text-gray-900"><?php echo e(number_format($sale->estimated_value)); ?> ج.م</span>
                    </div>
                    <?php if($sale->actual_value): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">القيمة الفعلية</span>
                        <span class="text-sm font-medium text-green-600"><?php echo e(number_format($sale->actual_value)); ?> ج.م</span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">نسبة الاحتمال</span>
                        <span class="text-sm font-medium text-blue-600"><?php echo e($sale->probability_percentage); ?>%</span>
                    </div>
                    <?php if($sale->estimated_value && $sale->probability_percentage): ?>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">القيمة المرجحة</span>
                        <span class="text-sm font-medium text-purple-600"><?php echo e(number_format(($sale->estimated_value * $sale->probability_percentage) / 100)); ?> ج.م</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
                <div class="space-y-3">
                    <a href="<?php echo e(route('sales.edit', $sale)); ?>" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        تعديل عملية البيع
                    </a>
                <?php if($sale->stage == 'closed_won'): ?>
                    <?php if($existingInvoice): ?>
                        <a href="<?php echo e(route('invoices.show', $existingInvoice)); ?>" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            عرض الفاتورة
                        </a>
                    <?php else: ?>
                        <form action="<?php echo e(route('sales.generate-invoice', $sale)); ?>" method="POST" class="w-full" onsubmit="return confirm('هل أنت متأكد من إنشاء الفاتورة؟');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('POST'); ?>
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                إنشاء فاتورة
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
                    <a href="mailto:<?php echo e($sale->client->email); ?>" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        إرسال بريد إلكتروني
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\sales\show.blade.php ENDPATH**/ ?>
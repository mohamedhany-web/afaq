

<?php $__env->startSection('page-title', 'تفاصيل الراتب'); ?>

<?php $__env->startSection('content'); ?>
<!-- Enhanced Header Section -->
<div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl mb-8 shadow-2xl">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
    
    <div class="relative p-8 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-start gap-6">
                <!-- Salary Icon -->
                <div class="h-20 w-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl">
                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>

                <!-- Salary Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h1 class="text-3xl font-bold">تفاصيل الراتب</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            <?php if($salary->status == 'paid'): ?> bg-green-500/20 text-green-100 border border-green-400/30
                            <?php elseif($salary->status == 'approved'): ?> bg-blue-500/20 text-blue-100 border border-blue-400/30
                            <?php else: ?> bg-yellow-500/20 text-yellow-100 border border-yellow-400/30 <?php endif; ?>">
                                <?php if($salary->status == 'paid'): ?> مدفوع
                                <?php elseif($salary->status == 'approved'): ?> موافق عليه
                                <?php else: ?> معلق <?php endif; ?>
                            </span>
                        </div>
                    
                    <div class="flex flex-wrap items-center gap-4 text-purple-100">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span><?php echo e($salary->employee->first_name); ?> <?php echo e($salary->employee->last_name); ?></span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span><?php echo e($salary->month_name); ?> <?php echo e($salary->year); ?></span>
                        </div>
                        
                        <?php if($salary->employee->department): ?>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span><?php echo e($salary->employee->department->name); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('salaries.index')); ?>" 
                   class="bg-white text-indigo-600 px-6 py-3 rounded-xl hover:bg-gray-100 transition-all duration-200 flex items-center gap-2 shadow-lg">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Salary Breakdown -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        تفاصيل الراتب
                    </h2>
                </div>
                <div class="p-6">
                    <!-- Earnings Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            الإيرادات والبدلات
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">الراتب الأساسي</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900"><?php echo e(number_format($salary->base_salary, 2)); ?> ج.م</span>
                            </div>
                            
                            <?php if($salary->overtime_amount > 0): ?>
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">الساعات الإضافية</span>
                                </div>
                                <span class="text-lg font-bold text-green-600">+ <?php echo e(number_format($salary->overtime_amount, 2)); ?> ج.م</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($salary->bonus > 0): ?>
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">المكافأة</span>
                                </div>
                                <span class="text-lg font-bold text-purple-600">+ <?php echo e(number_format($salary->bonus, 2)); ?> ج.م</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($salary->allowances > 0): ?>
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">البدلات</span>
                                </div>
                                <span class="text-lg font-bold text-orange-600">+ <?php echo e(number_format($salary->allowances, 2)); ?> ج.م</span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Total Earnings -->
                            <div class="flex items-center justify-between py-4 px-4 bg-green-50 rounded-lg border-2 border-green-200 mt-4">
                                <span class="text-base font-bold text-gray-900">إجمالي الإيرادات</span>
                                <span class="text-xl font-bold text-green-600"><?php echo e(number_format($grossSalary, 2)); ?> ج.م</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deductions Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                            الخصومات
                        </h3>
                        <div class="space-y-3">
                            <?php if($salary->tax > 0): ?>
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">الضريبة</span>
                                </div>
                                <span class="text-lg font-bold text-red-600">- <?php echo e(number_format($salary->tax, 2)); ?> ج.م</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($salary->deductions > 0): ?>
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">خصومات أخرى</span>
                                </div>
                                <span class="text-lg font-bold text-red-600">- <?php echo e(number_format($salary->deductions, 2)); ?> ج.م</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($totalDeductions == 0): ?>
                            <div class="text-center py-4 px-4 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-500">لا توجد خصومات</span>
                            </div>
                            <?php else: ?>
                            <!-- Total Deductions -->
                            <div class="flex items-center justify-between py-4 px-4 bg-red-50 rounded-lg border-2 border-red-200 mt-4">
                                <span class="text-base font-bold text-gray-900">إجمالي الخصومات</span>
                                <span class="text-xl font-bold text-red-600">- <?php echo e(number_format($totalDeductions, 2)); ?> ج.م</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Net Salary -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100 mb-1">الراتب الصافي</p>
                                <p class="text-3xl font-bold"><?php echo e(number_format($salary->net_salary, 2)); ?> ج.م</p>
                            </div>
                            <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="h-8 w-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        معلومات إضافية
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">تاريخ الإنشاء</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo e($salary->created_at->format('Y/m/d H:i')); ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">آخر تحديث</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo e($salary->updated_at->format('Y/m/d H:i')); ?></span>
                        </div>
                        
                        <?php if($salary->payment_date): ?>
                        <div class="flex items-center justify-between py-3 px-4 bg-green-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">تاريخ الدفع</span>
                            <span class="text-sm font-semibold text-green-600"><?php echo e($salary->payment_date->format('Y/m/d')); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($salary->approvedBy): ?>
                        <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600">الموافق عليه</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo e($salary->approvedBy->name); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($salary->notes): ?>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الملاحظات</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm text-gray-900"><?php echo e($salary->notes); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Employee Info Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                    <div class="text-center">
                        <div class="h-16 w-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white"><?php echo e(substr($salary->employee->first_name, 0, 1)); ?></span>
                        </div>
                        <h3 class="text-lg font-bold mb-2"><?php echo e($salary->employee->first_name); ?> <?php echo e($salary->employee->last_name); ?></h3>
                        <p class="text-sm text-blue-100"><?php echo e($salary->employee->position); ?></p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">القسم</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo e($salary->employee->department->name ?? 'غير محدد'); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">رقم الموظف</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo e($salary->employee->employee_id ?? 'غير محدد'); ?></span>
                        </div>
                        <?php if($salary->employee->hire_date): ?>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">تاريخ التوظيف</span>
                            <span class="text-sm font-semibold text-gray-900"><?php echo e(\Carbon\Carbon::parse($salary->employee->hire_date)->format('Y/m/d')); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">حالة الراتب</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-medium
                            <?php if($salary->status == 'paid'): ?> bg-green-100 text-green-800
                            <?php elseif($salary->status == 'approved'): ?> bg-blue-100 text-blue-800
                            <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?> mb-4">
                            <?php if($salary->status == 'paid'): ?> مدفوع
                            <?php elseif($salary->status == 'approved'): ?> موافق عليه
                            <?php else: ?> معلق <?php endif; ?>
                        </span>
                        <?php if($salary->status == 'paid' && $salary->payment_date): ?>
                        <p class="text-sm text-gray-600 mt-2">تم الدفع في <?php echo e($salary->payment_date->format('Y/m/d')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-salaries')): ?>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">إجراءات سريعة</h3>
                </div>
                <div class="p-6 space-y-3">
                    <?php if($salary->status == 'pending'): ?>
                    <button onclick="approveSalary(<?php echo e($salary->id); ?>)" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-lg">
                        <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        الموافقة على الراتب
                    </button>
                    <?php endif; ?>
                    
                    <?php if($salary->status == 'approved'): ?>
                    <button onclick="markAsPaid(<?php echo e($salary->id); ?>)" 
                            class="w-full flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors shadow-lg">
                        <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        تسجيل كمدفوع
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('salaries.index')); ?>" 
                       class="w-full flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors shadow-lg">
                        <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function approveSalary(salaryId) {
    if (!confirm('هل أنت متأكد من الموافقة على هذا الراتب؟')) {
        return;
    }
    
    fetch(`/salaries/${salaryId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في الموافقة على الراتب', 'error');
    });
}

function markAsPaid(salaryId) {
    if (!confirm('هل أنت متأكد من تسجيل هذا الراتب كمدفوع؟')) {
        return;
    }
    
    fetch(`/salaries/${salaryId}/mark-paid`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في تسجيل الراتب كمدفوع', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\salaries\show.blade.php ENDPATH**/ ?>
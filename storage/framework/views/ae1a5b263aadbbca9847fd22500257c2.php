<?php $__env->startSection('page-title', 'إدارة المرتبات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">إدارة المرتبات</h1>
                <p class="text-sm sm:text-base text-gray-600">إدارة رواتب الموظفين والحسابات</p>
            </div>
            <?php if(auth()->user()->hasRole(['admin', 'hr', 'super_admin'])): ?>
            <button id="generateSalariesBtn" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                إنشاء مرتبات الشهر
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Salaries -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المرتبات</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['total_salaries'], 2)); ?> ج.م</p>
                    <p class="text-xs text-green-600 mt-1"><?php echo e($currentMonth); ?>/<?php echo e($currentYear); ?></p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Paid Salaries -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مرتبات مدفوعة</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['paid_salaries'], 2)); ?> ج.م</p>
                    <p class="text-xs text-green-600 mt-1"><?php echo e($stats['payment_rate']); ?>% من الإجمالي</p>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Salaries -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">مرتبات معلقة</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['pending_salaries'], 2)); ?> ج.م</p>
                    <p class="text-xs text-orange-600 mt-1">يحتاج للمعالجة</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Salary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">متوسط الراتب</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e(number_format($stats['average_salary'], 2)); ?> ج.م</p>
                    <p class="text-xs text-purple-600 mt-1">شهرياً</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">سجل المرتبات</h3>
                <div class="flex items-center gap-2">
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="<?php echo e($currentMonth); ?>"><?php echo e(date('F', mktime(0, 0, 0, $currentMonth, 1))); ?> <?php echo e($currentYear); ?></option>
                        <?php for($i = 1; $i <= 12; $i++): ?>
                        <?php if($i != $currentMonth): ?>
                        <option value="<?php echo e($i); ?>"><?php echo e(date('F', mktime(0, 0, 0, $i, 1))); ?> <?php echo e($currentYear); ?></option>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                    <?php if(auth()->user()->hasRole(['admin', 'hr', 'super_admin']) && $employees->count() > 0): ?>
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>جميع الموظفين</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموظف</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشهر</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الراتب الأساسي</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البدلات</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخصومات</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصافي</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الدفع</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $salaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <!-- الموظف -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3 flex-shrink-0">
                                    <span class="text-sm font-medium text-white">
                                        <?php echo e(substr($salary->employee->first_name ?? 'م', 0, 1)); ?>

                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e($salary->employee->first_name); ?> <?php echo e($salary->employee->last_name); ?>

                                    </div>
                                    <div class="text-xs text-gray-500"><?php echo e($salary->employee->position); ?></div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- الشهر -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($salary->month_name); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($salary->year); ?></div>
                        </td>
                        
                        <!-- الراتب الأساسي -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900"><?php echo e(number_format($salary->base_salary, 0)); ?></div>
                            <div class="text-xs text-gray-500">ج.م</div>
                        </td>
                        
                        <!-- البدلات -->
                        <td class="px-6 py-4">
                            <?php
                                $totalAllowances = $salary->bonus + $salary->allowances + $salary->overtime_amount;
                            ?>
                            <div class="text-sm font-medium text-green-600"><?php echo e(number_format($totalAllowances, 0)); ?></div>
                            <?php if($totalAllowances > 0): ?>
                            <div class="text-xs text-gray-500">
                                <?php if($salary->bonus > 0): ?>
                                <div>مكافأة: <?php echo e(number_format($salary->bonus, 0)); ?></div>
                                <?php endif; ?>
                                <?php if($salary->allowances > 0): ?>
                                <div>بدلات: <?php echo e(number_format($salary->allowances, 0)); ?></div>
                                <?php endif; ?>
                                <?php if($salary->overtime_amount > 0): ?>
                                <div>إضافي: <?php echo e(number_format($salary->overtime_amount, 0)); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-xs text-gray-500">لا يوجد</div>
                            <?php endif; ?>
                        </td>
                        
                        <!-- الخصومات -->
                        <td class="px-6 py-4">
                            <?php
                                $totalDeductions = $salary->deductions + $salary->tax;
                            ?>
                            <div class="text-sm font-medium text-red-600"><?php echo e(number_format($totalDeductions, 0)); ?></div>
                            <?php if($totalDeductions > 0): ?>
                            <div class="text-xs text-gray-500">
                                <?php if($salary->deductions > 0): ?>
                                <div>خصومات: <?php echo e(number_format($salary->deductions, 0)); ?></div>
                                <?php endif; ?>
                                <?php if($salary->tax > 0): ?>
                                <div>ضريبة: <?php echo e(number_format($salary->tax, 0)); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-xs text-gray-500">لا يوجد</div>
                            <?php endif; ?>
                        </td>
                        
                        <!-- الصافي -->
                        <td class="px-6 py-4">
                            <div class="text-lg font-bold text-blue-600"><?php echo e(number_format($salary->net_salary, 0)); ?></div>
                            <div class="text-xs text-gray-500">ج.م</div>
                        </td>
                        
                        <!-- تاريخ الدفع -->
                        <td class="px-6 py-4">
                            <?php if($salary->payment_date): ?>
                            <div class="text-sm text-gray-900"><?php echo e($salary->payment_date->format('Y-m-d')); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($salary->payment_date->diffForHumans()); ?></div>
                            <?php else: ?>
                            <div class="text-sm text-gray-400">-</div>
                            <?php endif; ?>
                        </td>
                        
                        <!-- الحالة -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <?php if($salary->status == 'paid'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    مدفوع
                                </span>
                                <?php elseif($salary->status == 'approved'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    موافق عليه
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    معلق
                                </span>
                                <?php endif; ?>
                                
                                <?php if(auth()->user()->hasRole(['admin', 'hr', 'super_admin'])): ?>
                                    <?php if($salary->status == 'pending'): ?>
                                    <button onclick="approveSalary(<?php echo e($salary->id); ?>)" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        موافقة
                                    </button>
                                    <?php elseif($salary->status == 'approved'): ?>
                                    <button onclick="markAsPaid(<?php echo e($salary->id); ?>)" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        دفع
                                    </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <button onclick="viewSalaryDetails(<?php echo e($salary->id); ?>)" class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    عرض
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 font-medium">لا توجد مرتبات للعرض</p>
                                <p class="text-sm text-gray-400 mt-1">قم بإنشاء مرتبات الشهر أولاً</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Salaries Modal -->
<div id="generateSalariesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">إنشاء مرتبات الشهر</h3>
                <button onclick="closeGenerateSalariesModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="generateSalariesForm" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الشهر</label>
                    <select name="month" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo e($i); ?>" <?php echo e($i == $currentMonth ? 'selected' : ''); ?>><?php echo e(date('F', mktime(0, 0, 0, $i, 1))); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السنة</label>
                    <input type="number" name="year" value="<?php echo e($currentYear); ?>" required min="2020" max="2030" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-yellow-800">تنبيه</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>سيتم إنشاء المرتبات لجميع الموظفين النشطين بناءً على سجلات الحضور.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" onclick="closeGenerateSalariesModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        إنشاء المرتبات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if(auth()->user()->hasRole(['admin', 'hr', 'super_admin'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate Salaries Button
    const generateSalariesBtn = document.getElementById('generateSalariesBtn');
    if (generateSalariesBtn) {
        generateSalariesBtn.addEventListener('click', function() {
            document.getElementById('generateSalariesModal').classList.remove('hidden');
        });
    }
    
    // Generate Salaries Form
    const generateSalariesForm = document.getElementById('generateSalariesForm');
    if (generateSalariesForm) {
        generateSalariesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            generateSalaries();
        });
    }
});

function closeGenerateSalariesModal() {
    document.getElementById('generateSalariesModal').classList.add('hidden');
    document.getElementById('generateSalariesForm').reset();
}

function generateSalaries() {
    const form = document.getElementById('generateSalariesForm');
    const formData = new FormData(form);
    
    fetch('<?php echo e(route("salaries.generate")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeGenerateSalariesModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في إنشاء المرتبات', 'error');
    });
}

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

function viewSalaryDetails(salaryId) {
    window.location.href = `/salaries/${salaryId}`;
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
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\salaries\index.blade.php ENDPATH**/ ?>
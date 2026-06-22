<?php $__env->startSection('page-title', 'تقرير الرواتب'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('reports.index')); ?>" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">تقرير الرواتب</h1>
            </div>
            <a href="<?php echo e(route('reports.salaries.print', request()->query())); ?>" target="_blank" class="bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-900 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة التقرير
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الرواتب</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($summary['total_amount'], 0)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">الراتب الأساسي</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo e(number_format($summary['total_basic'], 0)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">البدلات</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($summary['total_bonuses'], 0)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">الخصومات</p>
            <p class="text-2xl font-bold text-red-600"><?php echo e(number_format($summary['total_deductions'], 0)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">عدد السجلات</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo e($summary['total_salaries']); ?></p>
        </div>
    </div>

    <!-- Salaries Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">القسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الراتب الأساسي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البدلات والمكافآت</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الخصومات والضرائب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الصافي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشهر / السنة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $salaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <?php echo e($salary->employee->first_name ?? ''); ?> <?php echo e($salary->employee->last_name ?? ''); ?>

                            <div class="text-xs text-gray-500"><?php echo e($salary->employee->position ?? ''); ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($salary->employee->department->name ?? '-'); ?></td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900"><?php echo e(number_format($salary->base_salary, 0)); ?> ريال</td>
                        <td class="px-6 py-4">
                            <?php
                                $totalAllowances = ($salary->bonus ?? 0) + ($salary->allowances ?? 0) + ($salary->overtime_amount ?? 0);
                            ?>
                            <div class="text-sm font-medium text-green-600"><?php echo e(number_format($totalAllowances, 0)); ?> ريال</div>
                            <?php if($totalAllowances > 0): ?>
                            <div class="text-xs text-gray-500">
                                <?php if($salary->bonus > 0): ?>مكافأة: <?php echo e(number_format($salary->bonus, 0)); ?><br><?php endif; ?>
                                <?php if($salary->allowances > 0): ?>بدلات: <?php echo e(number_format($salary->allowances, 0)); ?><br><?php endif; ?>
                                <?php if($salary->overtime_amount > 0): ?>إضافي: <?php echo e(number_format($salary->overtime_amount, 0)); ?><?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                                $totalDeductions = ($salary->deductions ?? 0) + ($salary->tax ?? 0);
                            ?>
                            <div class="text-sm font-medium text-red-600"><?php echo e(number_format($totalDeductions, 0)); ?> ريال</div>
                            <?php if($totalDeductions > 0): ?>
                            <div class="text-xs text-gray-500">
                                <?php if($salary->deductions > 0): ?>خصومات: <?php echo e(number_format($salary->deductions, 0)); ?><br><?php endif; ?>
                                <?php if($salary->tax > 0): ?>ضرائب: <?php echo e(number_format($salary->tax, 0)); ?><?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600"><?php echo e(number_format($salary->net_salary, 0)); ?> ريال</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo e($salary->month_name); ?> <?php echo e($salary->year); ?>

                        </td>
                        <td class="px-6 py-4">
                            <?php if($salary->status == 'paid'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">مدفوع</span>
                            <?php elseif($salary->status == 'approved'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">موافق عليه</span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">معلق</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\salaries.blade.php ENDPATH**/ ?>
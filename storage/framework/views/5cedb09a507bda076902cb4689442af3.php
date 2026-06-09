<?php $__env->startSection('page-title', 'تقرير المبيعات'); ?>

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
                <h1 class="text-3xl font-bold text-gray-900">تقرير المبيعات</h1>
            </div>
            <a href="<?php echo e(route('reports.sales.print', request()->query())); ?>" target="_blank" class="bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-900 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                طباعة التقرير
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المبيعات</p>
            <p class="text-3xl font-bold text-gray-900"><?php echo e($summary['total_sales']); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المبلغ</p>
            <p class="text-2xl font-bold text-green-600"><?php echo e(number_format($summary['total_amount'], 0)); ?> ج.م</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-600 mb-1">متوسط قيمة البيع</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($summary['average_sale'], 0)); ?> ج.م</p>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العميل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج/الخدمة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($sale->sale_date); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($sale->client->name ?? '-'); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($sale->product_service); ?></td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600"><?php echo e(number_format($sale->amount, 0)); ?> ج.م</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?php echo e($sale->status); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\reports\sales.blade.php ENDPATH**/ ?>
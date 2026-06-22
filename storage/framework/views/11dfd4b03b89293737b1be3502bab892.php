

<?php $__env->startSection('page-title', 'فواتير العميل'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">الفواتير والحسابات</h1>
                <p class="text-gray-600 text-sm sm:text-base">العميل: <?php echo e($client->name); ?></p>
            </div>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-all duration-200 text-sm">العودة</a>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/80 p-4 text-sm text-gray-800">
        <span class="font-bold text-gray-900">للاستفسار عن الدفع أو الاستحقاق:</span>
        هاتف الشركة: <?php echo e(\App\Helpers\SettingsHelper::getCompanyPhone() ?: '—'); ?> —
        البريد: <?php echo e(\App\Helpers\SettingsHelper::getCompanyEmail() ?: '—'); ?>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="font-bold text-gray-900">الفواتير (العادية)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الرقم</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الإجمالي</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">المتبقي</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الاستحقاق</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الدفع</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo e($inv->invoice_number); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo e(number_format($inv->total_amount)); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo e(number_format($inv->balance_amount)); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap"><?php echo e($inv->due_date?->format('Y/m/d') ?? '—'); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if($inv->payment_link): ?>
                                        <a href="<?php echo e($inv->payment_link); ?>" target="_blank" rel="noopener" class="text-blue-600 font-semibold hover:underline">رابط دفع</a>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($inv->status); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">لا توجد فواتير.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($invoices->appends(request()->query())->links()); ?>

            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="font-bold text-gray-900">الفواتير (المالية)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الرقم</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الإجمالي</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">المتبقي</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الاستحقاق</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الدفع</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $financialInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $finv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo e($finv->invoice_number); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo e(number_format($finv->total_amount)); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo e(number_format($finv->balance_due)); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap"><?php echo e($finv->due_date?->format('Y/m/d') ?? '—'); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if($finv->payment_link): ?>
                                        <a href="<?php echo e($finv->payment_link); ?>" target="_blank" rel="noopener" class="text-blue-600 font-semibold hover:underline">رابط دفع</a>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($finv->payment_status); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">لا توجد فواتير مالية.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($financialInvoices->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\invoices.blade.php ENDPATH**/ ?>
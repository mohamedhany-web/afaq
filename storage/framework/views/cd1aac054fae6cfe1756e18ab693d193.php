

<?php $__env->startSection('page-title', 'تذاكر الدعم'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">ما بعد البيع - تذاكر الدعم</h1>
                <p class="text-gray-600 text-sm sm:text-base">العميل: <?php echo e($client->name); ?></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('client.support.tickets.create')); ?>" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm">تذكرة جديدة</a>
                <a href="<?php echo e(route('client.dashboard')); ?>" class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-all duration-200 text-sm">العودة</a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الرقم</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الموضوع</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">المسند إليه</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 w-24">تفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo e($ticket->ticket_number); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo e($ticket->subject); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($ticket->status_name); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($ticket->priority); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e(optional($ticket->assignedTo)->name ?? '—'); ?></td>
                            <td class="px-6 py-4 text-left">
                                <a href="<?php echo e(route('client.support.tickets.show', $ticket)); ?>" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">عرض</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">لا توجد تذاكر.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($tickets->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\support\tickets\index.blade.php ENDPATH**/ ?>
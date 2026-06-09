

<?php $__env->startSection('page-title', 'حسابات العملاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">حسابات العملاء</h1>
                <p class="text-gray-600 text-sm sm:text-base">إدارة حسابات دخول العملاء لبوابة العميل</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-clients')): ?>
            <a href="<?php echo e(route('client-accounts.create')); ?>" class="bg-blue-600 text-white px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
                <svg class="h-4 w-4 sm:h-5 sm:w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                حساب جديد
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم/البريد/العميل..."
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <button class="bg-gray-900 text-white px-6 py-2.5 rounded-lg hover:bg-gray-800 transition">بحث</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">العميل</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">اسم الحساب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">البريد</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900"><?php echo e(optional($acc->client)->name ?? '—'); ?></div>
                                <div class="text-xs text-gray-500"><?php echo e(optional($acc->client)->company ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900"><?php echo e($acc->name); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($acc->email); ?></td>
                            <td class="px-6 py-4">
                                <?php if($acc->is_active): ?>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">مفعل</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">غير مفعل</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-clients')): ?>
                                    <a href="<?php echo e(route('client-accounts.edit', $acc)); ?>" class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs font-medium">تعديل</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-clients')): ?>
                                    <form method="POST" action="<?php echo e(route('client-accounts.destroy', $acc)); ?>" onsubmit="return confirm('هل أنت متأكد من حذف الحساب؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-xs font-medium">حذف</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">لا توجد حسابات عملاء.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($accounts->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-accounts\index.blade.php ENDPATH**/ ?>
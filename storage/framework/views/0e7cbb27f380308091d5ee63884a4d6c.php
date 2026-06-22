<?php $__env->startSection('page-title', 'طلبات اجتماعات العملاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">طلبات اجتماعات العملاء</h1>
            <p class="text-gray-600 text-sm sm:text-base">طلبات جدولة اجتماعات وردّ الفريق عليها من بوابة العميل.</p>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <form method="get" action="<?php echo e(route('client-meeting-requests.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="عنوان، وصف، رقم مرجعي..."
                       class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="pending" <?php if(request('status')==='pending'): echo 'selected'; endif; ?>>قيد المراجعة</option>
                    <option value="confirmed" <?php if(request('status')==='confirmed'): echo 'selected'; endif; ?>>تم التأكيد</option>
                    <option value="declined" <?php if(request('status')==='declined'): echo 'selected'; endif; ?>>مرفوض</option>
                    <option value="completed" <?php if(request('status')==='completed'): echo 'selected'; endif; ?>>مكتمل</option>
                    <option value="cancelled" <?php if(request('status')==='cancelled'): echo 'selected'; endif; ?>>ملغى</option>
                </select>
            </div>
            <div>
                <?php echo $__env->make('partials.client-search-select', ['required' => false, 'value' => request('client_id'), 'inputClass' => 'w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm', 'crmScope' => false, 'placeholder' => 'ابحث لتصفية حسب عميل...'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="md:col-span-4 flex flex-wrap gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">تصفية</button>
                <a href="<?php echo e(route('client-meeting-requests.index')); ?>" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">إعادة تعيين</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">المرجع</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الموضوع</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">العميل</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الموعد المفضل</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">الحالة</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $meetingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono font-semibold"><?php echo e($mr->reference_code); ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e(\Illuminate\Support\Str::limit($mr->title, 55)); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('clients.show', $mr->client)); ?>" class="text-blue-600 hover:underline"><?php echo e($mr->client->name); ?></a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap"><?php echo e($mr->preferred_at->format('Y/m/d H:i')); ?></td>
                            <td class="px-4 py-3"><?php echo e($mr->status_label); ?></td>
                            <td class="px-4 py-3 text-left">
                                <a href="<?php echo e(route('client-meeting-requests.show', $mr)); ?>" class="text-blue-600 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($meetingRequests->hasPages()): ?>
            <div class="px-4 py-3 border-t bg-gray-50"><?php echo e($meetingRequests->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\client-meeting-requests\index.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'موافقات الانصراف'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'موافقات الانصراف',
    'subtitle' => 'طلبات انصراف الموظفين — يجب اعتمادها قبل تسجيل الخروج',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
    'actionUrl' => route('operations.attendance-reviews.index'),
    'actionLabel' => 'مراجعة الغياب',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بانتظار الموافقة', 'value' => $stats['pending'], 'accent' => 'amber', 'compact' => true, 'href' => route('operations.checkout-reviews.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'عرض المعلّقة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معتمد اليوم', 'value' => $stats['approved'], 'accent' => 'green', 'compact' => true, 'href' => route('operations.checkout-reviews.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => 'عرض المعتمد'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مرفوض اليوم', 'value' => $stats['rejected'], 'accent' => 'red', 'compact' => true, 'href' => route('operations.checkout-reviews.index', ['status' => 'rejected']) . '#page-data', 'linkLabel' => 'عرض المرفوض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl border p-5 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ</label>
            <input type="date" name="date" value="<?php echo e($date->toDateString()); ?>" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
            <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
                <option value="">معلّقة</option>
                <option value="approved" <?php if(request('status') === 'approved'): echo 'selected'; endif; ?>>معتمدة</option>
                <option value="rejected" <?php if(request('status') === 'rejected'): echo 'selected'; endif; ?>>مرفوضة</option>
                <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>كل المعلّقة</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">عرض</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="px-5 py-4 border-b font-bold">طلبات <?php echo e($date->format('Y-m-d')); ?></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-right">الموظف</th>
                    <th class="p-3 text-right">القسم</th>
                    <th class="p-3 text-right">الحضور</th>
                    <th class="p-3 text-right">طلب الانصراف</th>
                    <th class="p-3 text-right">الساعات</th>
                    <th class="p-3 text-right">الحالة</th>
                    <th class="p-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t border-gray-100 align-top">
                <td class="p-3 font-semibold"><?php echo e($review->employee?->first_name); ?> <?php echo e($review->employee?->last_name); ?></td>
                <td class="p-3 text-xs text-gray-600"><?php echo e($review->employee?->department?->name ?? '—'); ?></td>
                <td class="p-3 font-mono" dir="ltr"><?php echo e($review->attendance?->check_in?->format('H:i') ?? '—'); ?></td>
                <td class="p-3 font-mono font-bold text-red-600" dir="ltr"><?php echo e($review->requested_check_out_at->format('H:i')); ?></td>
                <td class="p-3"><?php echo e($review->total_hours_preview); ?>h
                    <?php if($review->is_early_departure): ?><span class="block text-xs text-red-600">مبكر</span><?php endif; ?>
                </td>
                <td class="p-3"><?php echo $__env->make('attendances.partials.status-badge', ['label' => $review->statusLabel(), 'color' => $review->isPending() ? 'amber' : ($review->status === 'approved' ? 'green' : 'red')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                <td class="p-3">
                    <?php if($review->isPending()): ?>
                    <div class="flex flex-col gap-2 min-w-[200px]">
                        <form method="POST" action="<?php echo e(route('operations.checkout-reviews.approve', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="ملاحظة (اختياري)" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs font-bold">اعتماد</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('operations.checkout-reviews.reject', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" required placeholder="سبب الرفض" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-bold">رفض</button>
                        </form>
                    </div>
                    <?php elseif($review->review_notes): ?>
                    <p class="text-xs text-gray-500"><?php echo e($review->review_notes); ?></p>
                    <?php else: ?>
                    —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="p-10 text-center text-gray-500">لا توجد طلبات انصراف</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reviews->hasPages()): ?><div class="px-5 py-4 border-t"><?php echo e($reviews->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/operations/checkout-reviews/index.blade.php ENDPATH**/ ?>
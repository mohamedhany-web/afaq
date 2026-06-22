
<?php $__env->startSection('page-title', 'مراجعة الغياب'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'مراجعة الغياب اليومية',
    'subtitle' => 'جميع غيابات الموظفين تمر على مدير العمليات للتأكيد أو الاعتماد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $filterDate = $date->toDateString();
    $filterParams = ['date' => $filterDate];
    $statusFilter = $status ?? '';
?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بانتظار المراجعة', 'value' => $stats['pending'], 'accent' => 'amber', 'href' => route('operations.attendance-reviews.index', array_merge($filterParams, ['status' => 'pending'])) . '#page-data', 'linkLabel' => 'عرض المعلّقة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غياب مؤكد', 'value' => $stats['confirmed_absent'], 'accent' => 'red', 'href' => route('operations.attendance-reviews.index', array_merge($filterParams, ['status' => 'confirmed_absent'])) . '#page-data', 'linkLabel' => 'عرض الغياب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حضور مؤكد', 'value' => $stats['confirmed_present'], 'accent' => 'green', 'href' => route('operations.attendance-reviews.index', array_merge($filterParams, ['status' => 'confirmed_present'])) . '#page-data', 'linkLabel' => 'عرض الحضور'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معذور', 'value' => $stats['excused'], 'accent' => 'blue', 'href' => route('operations.attendance-reviews.index', array_merge($filterParams, ['status' => 'excused'])) . '#page-data', 'linkLabel' => 'عرض المعذور'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl border p-5">
        <div class="flex flex-wrap gap-3 items-end">
            <form method="GET" action="<?php echo e(route('operations.attendance-reviews.index')); ?>" class="flex flex-wrap gap-3 items-end flex-1">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ المراجعة</label>
                    <input type="date" name="date" value="<?php echo e($filterDate); ?>" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                    <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
                        <option value="" <?php if($statusFilter === ''): echo 'selected'; endif; ?>>الكل</option>
                        <option value="pending" <?php if($statusFilter === 'pending'): echo 'selected'; endif; ?>>بانتظار المراجعة</option>
                        <option value="confirmed_absent" <?php if($statusFilter === 'confirmed_absent'): echo 'selected'; endif; ?>>غياب مؤكد</option>
                        <option value="confirmed_present" <?php if($statusFilter === 'confirmed_present'): echo 'selected'; endif; ?>>حضور مؤكد</option>
                        <option value="excused" <?php if($statusFilter === 'excused'): echo 'selected'; endif; ?>>معذور</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">عرض</button>
            </form>
            <form method="POST" action="<?php echo e(route('operations.attendance-reviews.flag')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="date" value="<?php echo e($filterDate); ?>">
                <button type="submit" class="px-5 py-2.5 rounded-xl border-2 text-sm font-bold hover:bg-gray-50" style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>">
                    تحديث القائمة لهذا التاريخ
                </button>
            </form>
        </div>
        <p class="text-xs text-gray-500 mt-3">
            عرض <?php echo e($reviews->total()); ?> من <?php echo e($stats['total']); ?> سجل
            <?php if($statusFilter !== ''): ?>
                — فلتر الحالة: <strong><?php echo e($statusFilter); ?></strong>
            <?php endif; ?>
        </p>
    </div>
    <div class="bg-white rounded-2xl border p-5">
        <p class="font-bold text-gray-900 mb-3">الهرم الوظيفي</p>
        <ol class="space-y-2 text-sm">
            <?php $__currentLoopData = $hierarchy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex gap-2">
                <span class="font-bold text-gray-400"><?php echo e($level['level']); ?>.</span>
                <div>
                    <p class="font-semibold"><?php echo e($level['label']); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e(implode('، ', $level['roles'])); ?></p>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
        <p class="text-xs text-gray-500 mt-3">مدير العمليات يراجع غياب <strong>جميع الموظفين</strong> يومياً قبل احتساب الخصومات.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-right">الموظف</th>
                    <th class="p-3 text-right">القسم</th>
                    <th class="p-3 text-right">المدير المباشر</th>
                    <th class="p-3 text-right">السبب</th>
                    <th class="p-3 text-right">الحالة</th>
                    <th class="p-3 text-right">إجراء</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t border-gray-100 align-top">
                <td class="p-3">
                    <p class="font-semibold"><?php echo e($review->employee?->first_name); ?> <?php echo e($review->employee?->last_name); ?></p>
                    <p class="text-xs text-gray-500"><?php echo e($review->employee?->position); ?></p>
                </td>
                <td class="p-3"><?php echo e($review->employee?->department?->name ?? '—'); ?></td>
                <td class="p-3"><?php echo e($review->lineManager?->name ?? '—'); ?></td>
                <td class="p-3">
                    <?php echo e($review->reasonLabel()); ?>

                    <?php if($review->has_approved_leave): ?><span class="block text-xs text-blue-600">إجازة معتمدة</span><?php endif; ?>
                </td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-xs font-bold
                        <?php if($review->status === 'pending'): ?> bg-amber-100 text-amber-800
                        <?php elseif($review->status === 'confirmed_present'): ?> bg-green-100 text-green-800
                        <?php elseif($review->status === 'excused'): ?> bg-blue-100 text-blue-800
                        <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                        <?php echo e($review->statusLabel()); ?>

                    </span>
                    <?php if($review->review_notes): ?><p class="text-xs text-gray-500 mt-1"><?php echo e(Str::limit($review->review_notes, 60)); ?></p><?php endif; ?>
                </td>
                <td class="p-3">
                    <?php if($review->isPending()): ?>
                    <div class="flex flex-col gap-2 min-w-[200px]">
                        <form method="POST" action="<?php echo e(route('operations.attendance-reviews.confirm-present', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="ملاحظة (اختياري)" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs font-bold whitespace-nowrap">حضور فعلي</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('operations.attendance-reviews.confirm-absent', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="ملاحظة" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-red-600 text-white text-xs font-bold whitespace-nowrap">تأكيد غياب</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('operations.attendance-reviews.excuse', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="سبب العذر *" required class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg border text-xs font-bold whitespace-nowrap">معذور</button>
                        </form>
                    </div>
                    <?php elseif(in_array($review->status, ['confirmed_absent', 'auto_confirmed'])): ?>
                    <form method="POST" action="<?php echo e(route('operations.attendance-reviews.revoke', $review)); ?>" class="flex gap-1 min-w-[200px]">
                        <?php echo csrf_field(); ?>
                        <input type="text" name="notes" required placeholder="سبب إلغاء قرار الغياب" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold whitespace-nowrap">إلغاء القرار</button>
                    </form>
                    <?php else: ?>
                    <span class="text-xs text-gray-500"><?php echo e($review->reviewer?->name ?? '—'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="p-8 text-center text-gray-500">
                لا توجد سجلات غياب لتاريخ <strong><?php echo e($filterDate); ?></strong>
                <?php if($statusFilter !== ''): ?>
                    بالحالة المحددة.
                <?php else: ?>
                    .
                <?php endif; ?>
                <?php if($stats['total'] === 0): ?>
                    <span class="block mt-2 text-xs text-gray-400">اضغط «تحديث القائمة لهذا التاريخ» لإنشاء سجلات الغياب المحتمل تلقائياً.</span>
                <?php elseif($reviews->total() === 0): ?>
                    <span class="block mt-2 text-xs text-gray-400">يوجد <?php echo e($stats['total']); ?> سجل لهذا التاريخ — جرّب اختيار «الكل» من فلتر الحالة.</span>
                <?php endif; ?>
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reviews->hasPages()): ?><div class="p-4"><?php echo e($reviews->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\attendance-reviews\index.blade.php ENDPATH**/ ?>
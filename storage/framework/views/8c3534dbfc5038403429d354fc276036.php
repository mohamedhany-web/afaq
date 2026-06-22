
<?php $__env->startSection('page-title', 'مراجعة الغياب'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'سجل الغياب',
    'subtitle' => 'مراجعة وتأكيد غياب الموظفين — اعتماد العذر أو تأكيد الغياب',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($canReview): ?>
<form method="POST" action="<?php echo e(route('hr.absences.flag')); ?>" class="mb-4">
    <?php echo csrf_field(); ?>
    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تحديث قائمة الغياب</button>
</form>
<?php endif; ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بانتظار المراجعة', 'value' => $stats['pending'], 'accent' => 'amber', 'href' => route('hr.absences.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'عرض المعلّقة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غياب مؤكد', 'value' => $stats['confirmed_absent'], 'accent' => 'red', 'href' => route('hr.absences.index', ['status' => 'confirmed_absent']) . '#page-data', 'linkLabel' => 'عرض الغياب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حضور مؤكد', 'value' => $stats['confirmed_present'], 'accent' => 'green', 'href' => route('hr.absences.index', ['status' => 'confirmed_present']) . '#page-data', 'linkLabel' => 'عرض الحضور'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معذور', 'value' => $stats['excused'], 'accent' => 'blue', 'href' => route('hr.absences.index', ['status' => 'excused']) . '#page-data', 'linkLabel' => 'عرض المعذور'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl border p-5 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ المراجعة</label>
            <input type="date" name="date" value="<?php echo e($date->toDateString()); ?>" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
            <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm">
                <option value="">الكل</option>
                <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>بانتظار المراجعة</option>
                <option value="confirmed_absent" <?php if(request('status') === 'confirmed_absent'): echo 'selected'; endif; ?>>غياب مؤكد</option>
                <option value="confirmed_present" <?php if(request('status') === 'confirmed_present'): echo 'selected'; endif; ?>>حضور مؤكد</option>
                <option value="excused" <?php if(request('status') === 'excused'): echo 'selected'; endif; ?>>معذور</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">عرض</button>
    </form>
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
                </td>
                <td class="p-3">
                    <?php if($review->isPending() && $canReview): ?>
                    <div class="flex flex-col gap-2 min-w-[200px]">
                        <form method="POST" action="<?php echo e(route('hr.absences.confirm-present', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="ملاحظة (اختياري)" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-green-600 text-white text-xs font-bold whitespace-nowrap">حضور فعلي</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('hr.absences.confirm-absent', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="ملاحظة" class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg bg-red-600 text-white text-xs font-bold whitespace-nowrap">تأكيد غياب</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('hr.absences.excuse', $review)); ?>" class="flex gap-1">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="notes" placeholder="سبب العذر *" required class="flex-1 border rounded-lg px-2 py-1 text-xs">
                            <button type="submit" class="px-3 py-1 rounded-lg border text-xs font-bold whitespace-nowrap">معذور</button>
                        </form>
                    </div>
                    <?php else: ?>
                    <span class="text-xs text-gray-500"><?php echo e($review->reviewer?->name ?? '—'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="p-8 text-center text-gray-500">لا توجد سجلات غياب لهذا التاريخ.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($reviews->hasPages()): ?><div class="p-4"><?php echo e($reviews->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\absences\index.blade.php ENDPATH**/ ?>
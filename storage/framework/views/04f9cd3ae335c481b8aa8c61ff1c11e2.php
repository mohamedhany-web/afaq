
<?php $__env->startSection('page-title', 'مسار المبيعات — جدول'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'مسار المبيعات',
    'subtitle' => 'عرض جدولي — مناسب لآلاف الصفقات مع ترقيم الصفحات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    'actionUrl' => route('crm.pipeline.create'),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <input type="hidden" name="view" value="list">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث سريع..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-44">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">المرحلة</label>
            <select name="stage" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل المراحل</option>
                <?php $__currentLoopData = $stageLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if(request('stage') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
            <a href="<?php echo e(route('crm.pipeline.index', request()->except('view'))); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50">Kanban</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                <tr>
                    <th class="text-right py-3 px-4 font-tajawal">العميل</th>
                    <th class="text-right py-3 px-4 font-tajawal">المرحلة</th>
                    <th class="text-right py-3 px-4 font-tajawal">المشروع</th>
                    <th class="text-right py-3 px-4 font-tajawal">المندوب</th>
                    <th class="text-right py-3 px-4 font-tajawal">القيمة</th>
                    <th class="text-right py-3 px-4 font-tajawal">%</th>
                    <th class="text-right py-3 px-4 font-tajawal">تحديث</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-2.5 px-4">
                        <a href="<?php echo e(route('crm.pipeline.show', $deal)); ?>" class="font-medium font-tajawal" style="color: <?php echo e($themeColor); ?>;"><?php echo e($deal->client?->name ?? '—'); ?></a>
                    </td>
                    <td class="py-2.5 px-4 text-xs font-tajawal"><?php echo e($stageLabels[$deal->stage] ?? $deal->stage); ?></td>
                    <td class="py-2.5 px-4 text-gray-700 font-tajawal truncate max-w-[12rem]">
                        <?php echo $__env->make('crm.partials.entity-link', ['type' => 'project', 'entity' => $deal->project, 'linkClass' => 'hover:underline'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </td>
                    <td class="py-2.5 px-4 text-gray-600 font-tajawal">
                        <?php echo $__env->make('crm.partials.entity-link', ['type' => 'rep', 'entity' => $deal->salesRep, 'linkClass' => 'hover:underline'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </td>
                    <td class="py-2.5 px-4 font-semibold font-tajawal"><?php echo e($money($deal->estimated_value)); ?></td>
                    <td class="py-2.5 px-4 tabular-nums"><?php echo e($deal->probability_percentage); ?>%</td>
                    <td class="py-2.5 px-4 text-xs text-gray-500"><?php echo e($deal->updated_at->diffForHumans()); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="py-10 text-center text-gray-400 font-tajawal">لا توجد صفقات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($deals->hasPages()): ?>
    <div class="p-4 border-t border-gray-100"><?php echo e($deals->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\list.blade.php ENDPATH**/ ?>
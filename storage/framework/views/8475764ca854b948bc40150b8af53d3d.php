
<?php $__env->startSection('page-title', 'الوكلاء المستقلون'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $statuses = config('freelance_agents.contract_statuses');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'الوكلاء العقاريون المستقلون',
    'subtitle' => 'عقود Freelance Agent — هيكل العمولات وربط الصفقات والصرف',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.freelance-agents.create'),
    'actionLabel' => 'عقد وكيل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي العقود', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />', 'href' => route('crm.freelance-agents.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عقود نشطة', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.freelance-agents.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض النشطة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بأهداف ربع سنوية', 'value' => $stats['with_target'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => route('crm.freelance-agents.index') . '#page-data', 'linkLabel' => 'عرض العقود'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="mb-4 flex flex-wrap gap-2">
    <a href="<?php echo e(route('crm.freelance-agents.scheme')); ?>" class="px-4 py-2 rounded-xl border-2 text-sm font-semibold font-tajawal hover:bg-gray-50" style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>">عرض جدول هيكل العمولات</a>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم أو بريد الوكيل..." class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('status')===$k): echo 'selected'; endif; ?>><?php echo e($t); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b flex justify-between" style="<?php echo e($headerStyle); ?>">
        <h2 class="font-bold font-tajawal">قائمة العقود</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium" style="background:<?php echo e($themeColor); ?>15;color:<?php echo e($themeColor); ?>;"><?php echo e($contracts->total()); ?></span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[800px]">
            <thead class="bg-gray-50/80 border-b"><tr class="text-gray-600">
                <th class="text-right p-4 font-tajawal font-bold">الوكيل</th>
                <th class="text-right p-4 font-tajawal font-bold">رقم العقد</th>
                <th class="text-right p-4 font-tajawal font-bold">التارجت الربع سنوي</th>
                <th class="text-right p-4 font-tajawal font-bold">المدة</th>
                <th class="text-right p-4 font-tajawal font-bold">الحالة</th>
                <th class="text-right p-4 font-tajawal font-bold">إجراء</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/80">
                    <td class="p-4 font-semibold font-tajawal"><?php echo e($c->user?->name); ?></td>
                    <td class="p-4 text-gray-600 font-tajawal"><?php echo e($c->contract_number ?? '—'); ?></td>
                    <td class="p-4 text-xs font-tajawal">
                        <?php if($c->quarterly_target_deals): ?><?php echo e($c->quarterly_target_deals); ?> صفقة<?php endif; ?>
                        <?php if($c->quarterly_target_amount): ?><?php echo e($c->quarterly_target_deals ? ' / ' : ''); ?><?php echo e(\App\Helpers\SettingsHelper::formatMoney($c->quarterly_target_amount)); ?><?php endif; ?>
                        <?php if(!$c->quarterly_target_deals && !$c->quarterly_target_amount): ?>—<?php endif; ?>
                    </td>
                    <td class="p-4 text-xs font-tajawal whitespace-nowrap"><?php echo e($c->start_date?->format('Y/m/d')); ?> — <?php echo e($c->end_date?->format('Y/m/d') ?? 'مفتوح'); ?></td>
                    <td class="p-4"><span class="text-xs px-2 py-1 rounded-full font-semibold <?php echo e($c->status==='active'?'bg-green-100 text-green-800':'bg-gray-100 text-gray-600'); ?>"><?php echo e($statuses[$c->status] ?? $c->status); ?></span></td>
                    <td class="p-4"><a href="<?php echo e(route('crm.freelance-agents.show', $c)); ?>" class="text-xs font-bold px-2.5 py-1.5 rounded-lg text-white" style="background:<?php echo e($themeColor); ?>">عرض</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="p-10 text-center text-gray-400 font-tajawal">لا عقود — <a href="<?php echo e(route('crm.freelance-agents.create')); ?>" class="underline" style="color:<?php echo e($themeColor); ?>">أضف أول وكيل</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($contracts->hasPages()): ?><div class="p-4 border-t"><?php echo e($contracts->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\freelance-agents\index.blade.php ENDPATH**/ ?>
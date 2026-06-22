
<?php $__env->startSection('page-title', $contract->user?->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $statuses = config('freelance_agents.contract_statuses');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $contract->user?->name,
    'subtitle' => 'عقد وكيل مستقل — ' . ($statuses[$contract->status] ?? $contract->status),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.freelance-agents.edit', $contract),
    'actionLabel' => 'تعديل العقد',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="<?php echo e(route('crm.freelance-agents.contract-print', $contract)); ?>" target="_blank" class="px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold font-tajawal">طباعة مسودة العقد</a>
    <a href="<?php echo e(route('crm.freelance-agents.scheme')); ?>" class="px-4 py-2.5 rounded-xl border-2 text-sm font-semibold font-tajawal" style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>">جدول العمولات</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التارجت الربع سنوي', 'value' => $contract->quarterly_target_deals ? $contract->quarterly_target_deals.' صفقة' : ($contract->quarterly_target_amount ? $money($contract->quarterly_target_amount) : '—'), 'accent' => 'theme', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حالة التارجت', 'value' => $metTarget ? 'محقق' : 'غير محقق', 'accent' => $metTarget ? 'green' : 'amber', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بداية العقد', 'value' => $contract->start_date?->format('Y/m/d'), 'accent' => 'blue', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نهاية العقد', 'value' => $contract->end_date?->format('Y/m/d') ?? 'مفتوح', 'accent' => 'purple', 'compact' => true, 'href' => route('crm.freelance-agents.index'), 'linkLabel' => 'عرض العقود'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">بيانات العقد</div>
        <dl class="p-5 sm:p-6 space-y-3 text-sm font-tajawal">
            <div><dt class="text-xs font-bold text-gray-500">رقم العقد</dt><dd><?php echo e($contract->contract_number ?? '—'); ?></dd></div>
            <div><dt class="text-xs font-bold text-gray-500">الرقم القومي</dt><dd dir="ltr"><?php echo e($contract->national_id ?? '—'); ?></dd></div>
            <div><dt class="text-xs font-bold text-gray-500">الهاتف</dt><dd dir="ltr"><?php echo e($contract->phone ?? '—'); ?></dd></div>
            <div><dt class="text-xs font-bold text-gray-500">العنوان</dt><dd><?php echo e($contract->address ?? '—'); ?></dd></div>
            <div><dt class="text-xs font-bold text-gray-500">موقّع عن الشركة</dt><dd><?php echo e($contract->company_signatory_name ?? '—'); ?> <?php if($contract->company_signatory_title): ?>(<?php echo e($contract->company_signatory_title); ?>)<?php endif; ?></dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">آخر عمولات محسوبة</div>
        <div class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $recentSplits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $split): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-5 py-3 text-sm font-tajawal">
                <div class="font-semibold"><?php echo e($split->sale?->product_service); ?></div>
                <div class="text-xs text-gray-500"><?php echo e($split->agent_role); ?> — <?php echo e($money($split->amount)); ?> (<?php echo e($split->percent_of_company); ?>%)</div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-6 text-gray-400 text-sm text-center">لا عمولات بعد</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\freelance-agents\show.blade.php ENDPATH**/ ?>
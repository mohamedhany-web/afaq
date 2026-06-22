<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $items = [
        ['route' => 'accounting.index', 'label' => 'لوحة المحاسبة', 'match' => ['accounting.index']],
        ['route' => 'accounting.accounts', 'label' => 'دليل الحسابات', 'match' => ['accounting.accounts*']],
        ['route' => 'accounting.journal-entries', 'label' => 'القيود المحاسبية', 'match' => ['accounting.journal-entries*']],
        ['route' => 'financial-invoices.index', 'label' => 'الفواتير المالية', 'match' => ['financial-invoices.*']],
        ['route' => 'payments.index', 'label' => 'المدفوعات', 'match' => ['payments.*']],
        ['route' => 'expenses.index', 'label' => 'المصروفات', 'match' => ['expenses.*']],
        ['route' => 'accounting.reports.index', 'label' => 'التقارير المالية', 'match' => ['accounting.reports.*']],
        ['route' => 'invoices.index', 'label' => 'فواتير المبيعات', 'match' => ['invoices.*'], 'skip' => request()->routeIs('financial-invoices.*')],
        ['route' => 'crm.compensation.dashboard', 'label' => 'الرواتب والخصومات', 'match' => ['crm.compensation.*'], 'can' => 'view-reports'],
    ];
?>
<div class="mb-6 overflow-x-auto pb-1">
    <div class="flex gap-2 min-w-max font-tajawal">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!empty($item['skip']) && $item['skip']) continue; ?>
            <?php if(!empty($item['can']) && !auth()->user()?->can($item['can'])): ?>
                <?php continue; ?>
            <?php endif; ?>
            <?php $active = collect($item['match'])->contains(fn ($p) => request()->routeIs($p)); ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border"
               style="<?php echo e($active ? "background:linear-gradient(135deg,{$themeColor} 0%,{$themeColor}dd 100%);color:#fff;border-color:{$themeColor}" : "background:#fff;color:#374151;border-color:#e5e7eb"); ?>">
                <?php echo e($item['label']); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\partials\nav.blade.php ENDPATH**/ ?>
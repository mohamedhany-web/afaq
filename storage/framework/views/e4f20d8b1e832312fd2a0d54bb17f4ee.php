<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $dataTabUrl = route('operations.clients.index', array_filter(['bucket' => $bucket ?? 'all', 'search' => ($search ?? '') ?: null]));
    $distributionTabUrl = route('operations.clients.index', ['view' => 'distribution', 'filter' => $filter ?? 'unassigned']);
?>
<div class="mb-4 font-tajawal">
    <div class="flex flex-wrap gap-2 mb-3">
        <a href="<?php echo e($dataTabUrl); ?>#page-data"
           class="text-sm font-bold px-4 py-2.5 rounded-xl border transition-colors <?php echo e(($view ?? 'data') === 'data' ? 'text-white border-transparent' : 'text-gray-700 bg-white hover:bg-gray-50'); ?>"
           <?php if(($view ?? 'data') === 'data'): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
            <?php echo e(__('operations.clients.tab_data')); ?>

        </a>
        <a href="<?php echo e($distributionTabUrl); ?>#page-data"
           class="text-sm font-bold px-4 py-2.5 rounded-xl border transition-colors inline-flex items-center gap-2 <?php echo e(($view ?? 'data') === 'distribution' ? 'text-white border-transparent' : 'text-gray-700 bg-white hover:bg-gray-50'); ?>"
           <?php if(($view ?? 'data') === 'distribution'): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
            <?php echo e(__('operations.clients.tab_distribution')); ?>

            <?php if(($unassignedCount ?? 0) > 0): ?>
            <span class="text-[10px] px-1.5 py-0.5 rounded-full <?php echo e(($view ?? 'data') === 'distribution' ? 'bg-white/25' : 'bg-amber-100 text-amber-800'); ?>"><?php echo e($unassignedCount); ?></span>
            <?php endif; ?>
        </a>
    </div>
    <div class="rounded-xl border border-blue-100 bg-blue-50/80 px-4 py-3 text-xs text-blue-900 leading-relaxed">
        <p class="font-bold mb-1"><?php echo e(__('operations.clients.roles_title')); ?></p>
        <p><span class="font-semibold"><?php echo e(__('operations.clients.tab_data')); ?>:</span> <?php echo e(__('operations.clients.role_data')); ?></p>
        <p class="mt-1"><span class="font-semibold"><?php echo e(__('operations.clients.tab_distribution')); ?>:</span> <?php echo e(__('operations.clients.role_distribution')); ?></p>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\clients\partials\tabs.blade.php ENDPATH**/ ?>
<?php
    $statusColors = ['excellent' => 'text-green-700 bg-green-50', 'good' => 'text-blue-700 bg-blue-50', 'warning' => 'text-amber-700 bg-amber-50', 'critical' => 'text-red-700 bg-red-50'];
    $badge = $statusColors[$item['status']] ?? 'text-gray-700 bg-gray-50';
    $itemHref = $item['href'] ?? ($itemLinks[$item['slug'] ?? ''] ?? null);
    $detailArrow = app()->getLocale() === 'en' ? '→' : '←';
?>
<?php if($itemHref): ?>
<a href="<?php echo e($itemHref); ?>" class="block p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 hover:shadow-sm transition-all group text-start operations-kpi-card">
    <p class="text-xs text-gray-500 mb-1"><?php echo e($item['label']); ?></p>
    <div class="flex items-end justify-between gap-2">
        <p class="text-lg font-extrabold text-gray-900"><?php echo e(number_format($item['value'], 1)); ?> <span class="text-xs font-normal text-gray-500"><?php echo e($item['unit']); ?></span></p>
        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($badge); ?>"><?php echo e(number_format($item['achievement'], 0)); ?>%</span>
    </div>
    <p class="text-[10px] text-gray-400 mt-1"><?php echo e(__('operations.kpi.target')); ?>: <?php echo e(number_format($item['target'], 1)); ?> <?php echo e($item['unit']); ?></p>
    <span class="inline-flex items-center gap-1 text-[10px] font-bold mt-2 opacity-70 group-hover:opacity-100" style="color:<?php echo e($themeColor); ?>"><?php echo e(__('operations.actions.view_details')); ?> <?php echo e($detailArrow); ?></span>
</a>
<?php else: ?>
<div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-start operations-kpi-card">
    <p class="text-xs text-gray-500 mb-1"><?php echo e($item['label']); ?></p>
    <div class="flex items-end justify-between gap-2">
        <p class="text-lg font-extrabold text-gray-900"><?php echo e(number_format($item['value'], 1)); ?> <span class="text-xs font-normal text-gray-500"><?php echo e($item['unit']); ?></span></p>
        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($badge); ?>"><?php echo e(number_format($item['achievement'], 0)); ?>%</span>
    </div>
    <p class="text-[10px] text-gray-400 mt-1"><?php echo e(__('operations.kpi.target')); ?>: <?php echo e(number_format($item['target'], 1)); ?> <?php echo e($item['unit']); ?></p>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\partials\kpi-item.blade.php ENDPATH**/ ?>
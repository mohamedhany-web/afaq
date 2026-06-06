<?php
    $color = $stageColors[$stage] ?? ['bg' => $themeColor, 'light' => '#f3f4f6'];
    $deals = $deals ?? collect();
?>
<div class="pipeline-column w-[200px] sm:w-[212px] shrink-0 snap-start rounded-xl border border-gray-200 overflow-hidden flex flex-col bg-white max-h-[min(50vh,400px)]">
    <div class="px-2.5 py-2 border-b border-gray-100 shrink-0" style="background: <?php echo e($color['light']); ?>;">
        <div class="flex items-center justify-between gap-1">
            <h4 class="font-bold text-[10px] text-gray-900 font-tajawal truncate"><?php echo e($stageLabels[$stage]); ?></h4>
            <span class="deals-kanban-count text-[9px] font-bold text-white px-1.5 py-px rounded-full tabular-nums"
                  style="background: <?php echo e($color['bg']); ?>;" data-deal-stage="<?php echo e($stage); ?>"><?php echo e($deals->count()); ?></span>
        </div>
        <p class="text-[9px] text-gray-500 font-tajawal truncate"><?php echo e(\App\Helpers\SettingsHelper::formatMoney($total['value'] ?? 0)); ?></p>
    </div>
    <div class="deals-kanban-zone flex-1 p-1.5 space-y-1 overflow-y-auto bg-gray-50/80 min-h-[72px]"
         data-deal-stage="<?php echo e($stage); ?>">
        <?php $__empty_1 = true; $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php echo $__env->make('crm.pipeline.partials.deal-card-compact', ['deal' => $deal, 'accentColor' => $color['bg']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="kanban-empty flex items-center justify-center py-4 rounded-md border border-dashed border-gray-200">
            <span class="text-[9px] text-gray-400 font-tajawal">أفلت هنا</span>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/pipeline/partials/client-deal-column.blade.php ENDPATH**/ ?>
<?php
    $next = $client->listNextAction();
?>
<?php if($next): ?>
<div class="text-xs font-tajawal max-w-[200px]">
    <p class="font-semibold <?php echo e($next['overdue'] ? 'text-red-600' : 'text-gray-800'); ?>"><?php echo e($next['label']); ?></p>
    <p class="<?php echo e($next['overdue'] ? 'text-red-500' : 'text-gray-500'); ?> mt-0.5">
        <?php echo e($next['at']->locale('ar')->translatedFormat('d M Y — H:i')); ?>

        <?php if($next['overdue']): ?><span class="font-bold"> · متأخر</span><?php endif; ?>
    </p>
</div>
<?php else: ?>
<span class="text-xs text-gray-300">—</span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/list-next-action.blade.php ENDPATH**/ ?>
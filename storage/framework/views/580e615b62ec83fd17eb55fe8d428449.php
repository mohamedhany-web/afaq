<?php
    $lines = $client->leadSourceDetailLines();
?>
<?php if($lines !== []): ?>
<div class="sm:col-span-2 mt-1 space-y-1">
    <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <p class="text-sm text-gray-700 font-tajawal">
        <span class="text-xs font-bold text-gray-500"><?php echo e($line['label']); ?>:</span>
        <span class="font-semibold"><?php echo e($line['value']); ?></span>
    </p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\source-details-display.blade.php ENDPATH**/ ?>
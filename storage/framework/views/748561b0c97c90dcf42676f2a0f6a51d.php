<?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="flex items-center justify-between py-2.5 border-b border-gray-100">
    <span class="text-sm font-bold text-gray-900"><?php echo e($account->code ?? ''); ?> — <?php echo e($account->name ?? $account->account_name ?? 'غير محدد'); ?></span>
    <span class="text-sm font-bold text-gray-900 tabular-nums"><?php echo e($money($account->total_balance ?? $account->balance ?? 0)); ?></span>
</div>
<?php if(!empty($account->children) && $account->children->count() > 0): ?>
    <?php $__currentLoopData = $account->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="flex items-center justify-between py-2 pr-5 border-b border-gray-50">
        <span class="text-xs text-gray-600"><?php echo e($child->code ?? ''); ?> — <?php echo e($child->name ?? 'غير محدد'); ?></span>
        <span class="text-xs font-medium text-gray-700 tabular-nums"><?php echo e($money($child->computed_balance ?? $child->period_balance ?? 0)); ?></span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\partials\report-account-tree.blade.php ENDPATH**/ ?>
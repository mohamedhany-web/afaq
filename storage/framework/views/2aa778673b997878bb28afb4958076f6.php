
<?php $__env->startSection('page-title', __('operations.actions.search_sales_rep')); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => __('operations.actions.search_sales_rep'),
    'subtitle' => __('operations.rep_workspace.under_management'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
    'actionUrl' => route('operations.dashboard'),
    'actionLabel' => __('operations.dashboard_title'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('operations.partials.rep-search-form', [
    'salesReps' => $salesReps,
    'q' => $q,
    'selectedRepId' => $selectedRepId ?? null,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($q !== ''): ?>
<p class="text-xs text-gray-500 mb-3 font-tajawal">
    <?php echo e(__('operations.rep_workspace.search_results')); ?>: <strong><?php echo e($q); ?></strong> (<?php echo e($reps->count()); ?>)
</p>
<?php endif; ?>

<div class="bg-white rounded-2xl border divide-y font-tajawal" id="page-data">
    <?php $__empty_1 = true; $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('operations.reps.show', $rep)); ?>"
       class="flex items-center justify-between gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
        <div>
            <p class="font-bold text-gray-900"><?php echo e($rep->name); ?></p>
            <p class="text-xs text-gray-500"><?php echo e($rep->employee?->department?->name ?? '—'); ?></p>
        </div>
        <span class="text-xs font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e(__('operations.actions.open_rep_workspace')); ?> ←</span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="p-8 text-center text-gray-500"><?php echo e(__('operations.rep_workspace.no_results')); ?></p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\reps\search.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', __('operations.rep_workspace.title')); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $rep->name,
    'subtitle' => __('operations.rep_workspace.under_management'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    'actionUrl' => route('operations.dashboard'),
    'actionLabel' => __('operations.dashboard_title'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6 font-tajawal">
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => __('operations.sections.all'),
        'value' => $clientStats['all'],
        'accent' => 'theme',
        'href' => route('operations.clients.index', ['bucket' => 'all', 'employee_id' => $employeeId]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => __('operations.sections.interested'),
        'value' => $clientStats['interested'],
        'accent' => 'purple',
        'href' => route('operations.clients.index', ['bucket' => 'interested', 'employee_id' => $employeeId]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => __('operations.sections.follow_up'),
        'value' => $clientStats['follow_up'],
        'accent' => 'blue',
        'href' => route('operations.clients.index', ['bucket' => 'follow_up', 'employee_id' => $employeeId]) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 font-tajawal" id="page-data">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold"><?php echo e(__('operations.rep_workspace.clients')); ?></div>
        <ul class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $recentClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="px-5 py-3 flex items-center justify-between gap-2">
                <a href="<?php echo e($client->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($client->name); ?></a>
                <span class="text-xs text-gray-500"><?php echo e($client->lead_stage); ?></span>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="px-5 py-8 text-center text-gray-500 text-sm"><?php echo e(__('operations.clients.empty')); ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold"><?php echo e(__('operations.rep_workspace.tasks')); ?></div>
        <ul class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="px-5 py-3">
                <a href="<?php echo e(route('crm.tasks.show', $task)); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($task->title); ?></a>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($task->due_at?->format('Y-m-d H:i')); ?> · <?php echo e($task->statusLabel()); ?></p>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="px-5 py-8 text-center text-gray-500 text-sm">—</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\reps\show.blade.php ENDPATH**/ ?>
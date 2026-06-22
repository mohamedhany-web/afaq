
<?php $__env->startSection('page-title', __('operations.hr_requests.permits_title')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => __('operations.hr_requests.permits_title'),
    'subtitle' => __('operations.hr_requests.permits_subtitle'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>',
    'actionUrl' => route('operations.dashboard'),
    'actionLabel' => __('operations.dashboard_title'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.pending'), 'value' => $stats['pending'], 'accent' => 'amber', 'href' => route('operations.exit-permits.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.approved_month'), 'value' => $stats['approved_month'], 'accent' => 'green', 'href' => route('operations.exit-permits.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.rejected_month'), 'value' => $stats['rejected_month'], 'accent' => 'red', 'href' => route('operations.exit-permits.index', ['status' => 'rejected']) . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl border overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
        <h3 class="font-bold"><?php echo e(__('operations.hr_requests.permits_title')); ?></h3>
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                <option value=""><?php echo e(__('operations.hr_requests.all_statuses')); ?></option>
                <?php $__currentLoopData = config('exit_permits.status_labels', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(request('status') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="permit_type" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                <option value=""><?php echo e(__('operations.hr_requests.all_types')); ?></option>
                <?php $__currentLoopData = $permitTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(request('permit_type') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.employee')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.type')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.date')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.time')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.reason')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.status')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.action')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $permits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="font-semibold"><?php echo e($permit->employee?->first_name); ?> <?php echo e($permit->employee?->last_name); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($permit->employee?->department?->name); ?></div>
                    </td>
                    <td class="px-5 py-4"><?php echo e($permit->typeLabel()); ?></td>
                    <td class="px-5 py-4"><?php echo e($permit->permit_date->format('Y/m/d')); ?></td>
                    <td class="px-5 py-4 text-gray-600">
                        <?php if($permit->start_time && $permit->end_time): ?>
                            <?php echo e(\Carbon\Carbon::parse($permit->start_time)->format('H:i')); ?> — <?php echo e(\Carbon\Carbon::parse($permit->end_time)->format('H:i')); ?>

                        <?php elseif($permit->duration_minutes): ?>
                            <?php echo e($permit->duration_minutes); ?> <?php echo e(__('operations.hr_requests.minutes')); ?>

                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4 text-gray-600 max-w-[12rem] truncate" title="<?php echo e($permit->reason); ?>"><?php echo e($permit->reason); ?></td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($statusColors[$permit->status] ?? 'bg-gray-100'); ?>">
                            <?php echo e($permit->statusLabel()); ?>

                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <?php if($scope->canApprovePermit($permit)): ?>
                        <div class="flex flex-col gap-2 min-w-[180px]">
                            <form method="POST" action="<?php echo e(route('operations.exit-permits.approve', $permit)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold"><?php echo e(__('operations.hr_requests.approve')); ?></button>
                            </form>
                            <form method="POST" action="<?php echo e(route('operations.exit-permits.reject', $permit)); ?>" class="flex gap-1">
                                <?php echo csrf_field(); ?>
                                <input type="text" name="rejection_reason" placeholder="<?php echo e(__('operations.hr_requests.reject_reason')); ?>" required class="flex-1 border rounded-lg px-2 py-1 text-xs">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-bold"><?php echo e(__('operations.hr_requests.reject')); ?></button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span class="text-xs text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="px-5 py-16 text-center text-gray-500"><?php echo e(__('operations.hr_requests.empty_permits')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($permits->hasPages()): ?><div class="px-5 py-4 border-t"><?php echo e($permits->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\exit-permits\index.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', __('operations.hr_requests.leaves_title')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $typeColors = [
        'annual' => 'bg-green-100 text-green-800',
        'sick' => 'bg-blue-100 text-blue-800',
        'emergency' => 'bg-red-100 text-red-800',
        'maternity' => 'bg-pink-100 text-pink-800',
        'paternity' => 'bg-purple-100 text-purple-800',
        'unpaid' => 'bg-gray-100 text-gray-800',
        'compensatory' => 'bg-amber-100 text-amber-800',
    ];
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => __('operations.hr_requests.leaves_title'),
    'subtitle' => __('operations.hr_requests.leaves_subtitle'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    'actionUrl' => route('operations.dashboard'),
    'actionLabel' => __('operations.dashboard_title'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.pending'), 'value' => $stats['pending'], 'accent' => 'amber', 'href' => route('operations.leaves.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.approved_month'), 'value' => $stats['approved_month'], 'accent' => 'green', 'href' => route('operations.leaves.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.rejected_month'), 'value' => $stats['rejected_month'], 'accent' => 'red', 'href' => route('operations.leaves.index', ['status' => 'rejected']) . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => __('operations.hr_requests.days_year'), 'value' => $stats['total_days_year'], 'accent' => 'blue', 'href' => route('operations.leaves.index') . '#page-data', 'linkLabel' => __('operations.actions.view')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl border overflow-hidden font-tajawal">
    <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
        <h3 class="font-bold"><?php echo e(__('operations.hr_requests.leaves_title')); ?></h3>
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="border rounded-xl px-3 py-2 text-sm">
                <option value=""><?php echo e(__('operations.hr_requests.all_statuses')); ?></option>
                <?php $__currentLoopData = $statusColors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(($status ?? '') === $key): echo 'selected'; endif; ?>><?php echo e(config('leaves.status_labels.' . $key, $key)); ?></option>
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
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.period')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.days')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.reason')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.status')); ?></th>
                    <th class="px-5 py-3 text-right"><?php echo e(__('operations.hr_requests.action')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="font-semibold"><?php echo e($leave->employee?->first_name); ?> <?php echo e($leave->employee?->last_name); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($leave->employee?->department?->name); ?></div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($typeColors[$leave->leave_type] ?? 'bg-gray-100'); ?>">
                            <?php echo e($leaveTypes[$leave->leave_type] ?? $leave->leave_type); ?>

                        </span>
                    </td>
                    <td class="px-5 py-4"><?php echo e($leave->start_date->format('Y/m/d')); ?> — <?php echo e($leave->end_date->format('Y/m/d')); ?></td>
                    <td class="px-5 py-4"><?php echo e($leave->total_days); ?></td>
                    <td class="px-5 py-4 text-gray-600 max-w-[12rem] truncate" title="<?php echo e($leave->reason); ?>"><?php echo e($leave->reason); ?></td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($statusColors[$leave->status] ?? 'bg-gray-100'); ?>">
                            <?php echo e(config('leaves.status_labels.' . $leave->status, $leave->status)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <?php if($leave->status === 'pending'): ?>
                        <div class="flex flex-col gap-2 min-w-[180px]">
                            <form method="POST" action="<?php echo e(route('operations.leaves.approve', $leave)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold"><?php echo e(__('operations.hr_requests.approve')); ?></button>
                            </form>
                            <form method="POST" action="<?php echo e(route('operations.leaves.reject', $leave)); ?>" class="flex gap-1">
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
                <tr><td colspan="7" class="px-5 py-16 text-center text-gray-500"><?php echo e(__('operations.hr_requests.empty_leaves')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($leaves->hasPages()): ?><div class="px-5 py-4 border-t"><?php echo e($leaves->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\leaves\index.blade.php ENDPATH**/ ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => __('operations.clients.pending_distribution'),
        'value' => $stats['unassigned'],
        'accent' => 'amber',
        'href' => route('operations.clients.index', ['view' => 'distribution', 'filter' => 'unassigned']) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => __('operations.clients.stale_unassigned'),
        'value' => $stats['stale'],
        'accent' => 'red',
        'href' => route('operations.clients.index', ['view' => 'distribution', 'filter' => 'stale']) . '#page-data',
        'linkLabel' => __('operations.actions.view_details'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($leadKpis ?? null): ?>
<?php echo $__env->make('operations.partials.kpi-group', ['group' => $leadKpis, 'link' => route('operations.clients.index', ['view' => 'distribution']) . '#page-data'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <p class="font-bold"><?php echo e(($filter ?? 'unassigned') === 'stale' ? __('operations.clients.stale_list_title') : __('operations.clients.pending_list_title')); ?></p>
                <a href="<?php echo e(route('operations.clients.index', ['view' => 'distribution', 'filter' => 'unassigned'])); ?>#page-data"
                   class="text-xs font-bold px-2 py-1 rounded-lg <?php echo e(($filter ?? 'unassigned') !== 'stale' ? 'text-white' : 'border text-gray-600'); ?>"
                   <?php if(($filter ?? 'unassigned') !== 'stale'): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
                    <?php echo e(__('operations.clients.pending_distribution')); ?>

                </a>
                <a href="<?php echo e(route('operations.clients.index', ['view' => 'distribution', 'filter' => 'stale'])); ?>#page-data"
                   class="text-xs font-bold px-2 py-1 rounded-lg <?php echo e(($filter ?? '') === 'stale' ? 'text-white' : 'border text-gray-600'); ?>"
                   <?php if(($filter ?? '') === 'stale'): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
                    <?php echo e(__('operations.clients.stale_filter')); ?>

                </a>
            </div>
            <?php if(($filter ?? 'unassigned') !== 'stale'): ?>
            <form method="POST" action="<?php echo e(route('operations.leads.auto-distribute')); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:<?php echo e($themeColor); ?>"><?php echo e(__('operations.clients.auto_distribute')); ?></button>
            </form>
            <?php endif; ?>
        </div>
        <form method="GET" class="p-4 border-b">
            <input type="hidden" name="view" value="distribution">
            <input type="hidden" name="filter" value="<?php echo e($filter ?? 'unassigned'); ?>">
            <input type="search" name="search" value="<?php echo e($search ?? ''); ?>" placeholder="<?php echo e(__('operations.actions.search')); ?>..." class="w-full border rounded-xl px-4 py-2 text-sm">
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="p-3 text-right"><input type="checkbox" id="check-all"></th>
                    <th class="p-3 text-right"><?php echo e(__('operations.clients.client')); ?></th>
                    <th class="p-3 text-right"><?php echo e(__('operations.clients.phone')); ?></th>
                    <th class="p-3 text-right"><?php echo e(__('operations.clients.source')); ?></th>
                    <th class="p-3 text-right"><?php echo e(__('operations.clients.assign')); ?></th>
                    <th class="p-3 text-right"><?php echo e(__('operations.clients.actions')); ?></th>
                </tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t">
                    <td class="p-3"><input type="checkbox" name="client_ids[]" value="<?php echo e($lead->id); ?>" form="batch-form" class="lead-check"></td>
                    <td class="p-3">
                        <a href="<?php echo e($lead->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($lead->name); ?></a>
                    </td>
                    <td class="p-3" dir="ltr"><?php echo e($lead->phone); ?></td>
                    <td class="p-3 text-xs"><?php echo $__env->make('crm.clients.partials.source-badge', ['source' => $lead->lead_source], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                    <td class="p-3">
                        <?php if(($filter ?? 'unassigned') !== 'stale'): ?>
                        <form method="POST" action="<?php echo e(route('operations.leads.assign', $lead)); ?>" class="flex gap-1"><?php echo csrf_field(); ?>
                            <select name="employee_id" class="border rounded-lg text-xs px-2 py-1" required>
                                <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($rep->employee?->id); ?>"><?php echo e($rep->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button class="px-2 py-1 rounded-lg text-white text-xs" style="background:<?php echo e($themeColor); ?>"><?php echo e(__('operations.clients.assign_btn')); ?></button>
                        </form>
                        <?php else: ?>
                        <span class="text-xs text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <div class="flex flex-wrap gap-1">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewFullDetails', $lead)): ?>
                            <a href="<?php echo e(route('crm.clients.show', $lead)); ?>" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:<?php echo e($themeColor); ?>;border-color:<?php echo e($themeColor); ?>40"><?php echo e(__('operations.clients.full_profile')); ?></a>
                            <?php else: ?>
                            <a href="<?php echo e($lead->profileUrl()); ?>" class="px-2 py-1 rounded-lg text-xs font-bold border hover:bg-gray-50" style="color:<?php echo e($themeColor); ?>;border-color:<?php echo e($themeColor); ?>40"><?php echo e(__('operations.clients.pipeline')); ?></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="p-8 text-center text-gray-500"><?php echo e(__('operations.clients.distribution_empty')); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4"><?php echo e($leads->links()); ?></div>
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border p-5">
            <p class="font-bold mb-3"><?php echo e(__('operations.clients.rep_loads')); ?></p>
            <ul class="space-y-2 text-sm">
                <?php $__currentLoopData = $repLoads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex justify-between gap-2 p-2 rounded-lg bg-gray-50">
                    <span><?php echo e($row['employee']->user?->name ?? ($row['employee']->first_name . ' ' . $row['employee']->last_name)); ?></span>
                    <span class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($row['load']); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <form id="batch-form" method="POST" action="<?php echo e(route('operations.leads.distribute-batch')); ?>" class="bg-white rounded-2xl border p-5"><?php echo csrf_field(); ?>
            <p class="font-bold mb-2"><?php echo e(__('operations.clients.batch_distribute')); ?></p>
            <select name="employee_id" class="w-full border rounded-xl px-3 py-2 text-sm mb-3">
                <option value=""><?php echo e(__('operations.clients.auto_least_load')); ?></option>
                <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($rep->employee?->id); ?>"><?php echo e($rep->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="w-full py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>"><?php echo e(__('operations.clients.batch_distribute')); ?></button>
        </form>
    </div>
</div>
<script>
document.getElementById('check-all')?.addEventListener('change', function () {
    document.querySelectorAll('.lead-check').forEach(cb => cb.checked = this.checked);
});
</script>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\clients\partials\distribution-panel.blade.php ENDPATH**/ ?>
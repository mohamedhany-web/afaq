
<?php $__env->startSection('page-title', __('operations.clients.hub_title')); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => __('operations.clients.hub_title'),
    'subtitle' => __('operations.clients.hub_subtitle'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => __('operations.clients.new_client'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('operations.clients.partials.tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="mb-4 flex flex-wrap gap-2 font-tajawal">
    <a href="<?php echo e(route('crm.clients.create', ['tab' => 'import'])); ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold border-2 hover:bg-gray-50"
       style="border-color: <?php echo e($themeColor); ?>40; color: <?php echo e($themeColor); ?>;">
        <?php echo e(__('operations.clients.import_excel')); ?> / CSV
    </a>
    <a href="<?php echo e(route('crm.clients.import.template')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100">
        <?php echo e(__('operations.clients.download_template')); ?>

    </a>
</div>

<div id="page-data">
<?php if(($view ?? 'data') === 'distribution'): ?>
    <?php echo $__env->make('operations.clients.partials.distribution-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
    <div class="flex flex-wrap gap-2 mb-4 font-tajawal">
        <?php $__currentLoopData = $bucketLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('operations.clients.index', array_filter(['bucket' => $key, 'search' => $search ?: null, 'employee_id' => request('employee_id')]))); ?>#page-data"
           class="text-xs font-bold px-3 py-2 rounded-xl border transition-colors <?php echo e($bucket === $key ? 'text-white border-transparent' : 'text-gray-600 bg-white hover:bg-gray-50'); ?>"
           <?php if($bucket === $key): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
            <?php echo e($label); ?>

            <span class="opacity-80">(<?php echo e(number_format($bucketCounts[$key] ?? 0)); ?>)</span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="GET" class="mb-4 flex gap-2 font-tajawal">
        <input type="hidden" name="bucket" value="<?php echo e($bucket); ?>">
        <?php if(request('employee_id')): ?>
        <input type="hidden" name="employee_id" value="<?php echo e(request('employee_id')); ?>">
        <?php endif; ?>
        <input type="search" name="search" value="<?php echo e($search); ?>" placeholder="<?php echo e(__('operations.actions.search')); ?>..."
               class="flex-1 border rounded-xl px-4 py-2.5 text-sm">
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">
            <?php echo e(__('operations.actions.search')); ?>

        </button>
    </form>

    <div class="bg-white rounded-2xl border overflow-hidden font-tajawal">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <p class="font-bold"><?php echo e($bucketLabels[$bucket] ?? $bucket); ?></p>
            <p class="text-xs text-gray-500"><?php echo e(number_format($clients->total())); ?> <?php echo e(__('operations.clients.results')); ?></p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-right"><?php echo e(__('operations.clients.client')); ?></th>
                        <th class="p-3 text-right"><?php echo e(__('operations.clients.phone')); ?></th>
                        <th class="p-3 text-right"><?php echo e(__('operations.clients.stage')); ?></th>
                        <th class="p-3 text-right"><?php echo e(__('operations.clients.assigned')); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">
                            <a href="<?php echo e($client->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($client->name); ?></a>
                        </td>
                        <td class="p-3 text-gray-600" dir="ltr"><?php echo e($client->phone); ?></td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded-lg bg-gray-100"><?php echo e($client->lead_stage ?? '—'); ?></span>
                        </td>
                        <td class="p-3 text-gray-600">
                            <?php echo e($client->assignedEmployee ? trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) : '—'); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500"><?php echo e(__('operations.clients.empty')); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($clients->hasPages()): ?>
        <div class="p-4 border-t"><?php echo e($clients->links()); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\clients\index.blade.php ENDPATH**/ ?>
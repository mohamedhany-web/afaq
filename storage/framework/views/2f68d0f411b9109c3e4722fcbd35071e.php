
<?php $__env->startSection('page-title', 'المخزون العقاري'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $statusLabels = config('project_units.statuses', []);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'إدارة المخزون العقاري',
    'subtitle' => 'الوحدات المتاحة والمحجوزة والمباعة — دقة الأسعار',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
    'actionUrl' => route('crm.projects.index'),
    'actionLabel' => 'المشاريع والوحدات',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الوحدات', 'value' => $stats['total'], 'accent' => 'theme', 'href' => route('operations.inventory.index') . '#page-data', 'linkLabel' => 'عرض الوحدات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متاحة', 'value' => $stats['available'], 'accent' => 'green', 'href' => route('operations.inventory.index', ['status' => 'available']) . '#page-data', 'linkLabel' => 'عرض المتاح'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'محجوزة', 'value' => $stats['reserved'], 'accent' => 'amber', 'href' => route('operations.inventory.index', ['status' => 'reserved']) . '#page-data', 'linkLabel' => 'عرض المحجوز'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مباعة', 'value' => $stats['sold'], 'accent' => 'blue', 'href' => route('operations.inventory.index', ['status' => 'sold']) . '#page-data', 'linkLabel' => 'عرض المباع'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($inventoryKpis): ?>
<?php echo $__env->make('operations.partials.kpi-group', ['group' => $inventoryKpis, 'link' => route('operations.inventory.index') . '#page-data'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php if($selectedProject ?? null): ?>
<?php echo $__env->make('projects.partials.classification-filter', [
    'project' => $selectedProject,
    'themeColor' => $themeColor,
    'filterMode' => 'operations',
    'opsFilterUrl' => route('operations.inventory.index', array_filter([
        'project_id' => $selectedProject->id,
        'status' => request('status'),
        'search' => request('search'),
    ])),
    'defaultClass' => request('use_type'),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php echo $__env->make('operations.partials.unit-inventory-cards', compact('units', 'projects', 'statusFilter', 'themeColor', 'useTypeFilter', 'useTypeLabels'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6 font-tajawal">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold">المخزون حسب المشروع</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="p-3 text-right">المشروع</th>
                    <th class="p-3 text-right">متاح</th>
                    <th class="p-3 text-right">محجوز</th>
                    <th class="p-3 text-right">مباع</th>
                    <th class="p-3 text-right"></th>
                </tr></thead>
                <tbody>
                <?php $__currentLoopData = $byProject; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t">
                    <td class="p-3 font-semibold"><?php echo e($project->name); ?></td>
                    <td class="p-3 text-green-700 font-bold"><?php echo e($project->available_count); ?></td>
                    <td class="p-3 text-amber-700"><?php echo e($project->reserved_count); ?></td>
                    <td class="p-3 text-blue-700"><?php echo e($project->sold_count); ?></td>
                    <td class="p-3">
                        <a href="<?php echo e(route('crm.projects.show', $project)); ?>#building-units-root" class="text-xs font-bold hover:underline" style="color:<?php echo e($themeColor); ?>">الوحدات</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold text-red-700">وحدات بدون سعر</div>
        <ul class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $missingPrice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="p-4 text-sm">
                <a href="<?php echo e(route('crm.projects.show', $unit->project_id)); ?>?unit=<?php echo e($unit->id); ?>#building-units-root" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($unit->code); ?> — <?php echo e($unit->project?->name); ?></a>
                <p class="text-xs text-gray-500"><?php echo e($unit->useTypeLabel()); ?> — <?php echo e($unit->area_m2); ?> م²</p>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="p-6 text-center text-gray-500">جميع الوحدات المتاحة لها أسعار</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\inventory\index.blade.php ENDPATH**/ ?>
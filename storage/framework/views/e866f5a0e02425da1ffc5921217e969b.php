
<?php $__env->startSection('page-title', 'فريق التسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'فريق التسويق',
    'subtitle' => $department->name . ' — ' . $department->description,
    'actionUrl' => $canManage ? route('employees.create', ['marketing_only' => 1]) : null,
    'actionLabel' => 'إضافة موظف تسويق',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الفريق', 'value' => $stats['total'], 'accent' => 'purple', 'href' => route('marketing.team.index') . '#page-data', 'linkLabel' => 'عرض الفريق'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مديرون', 'value' => $stats['managers'], 'accent' => 'theme', 'href' => route('marketing.team.index') . '#page-data', 'linkLabel' => 'عرض الفريق'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'موظفون', 'value' => $stats['reps'], 'accent' => 'blue', 'href' => route('marketing.team.index') . '#page-data', 'linkLabel' => 'عرض الفريق'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-2xl shadow-lg border p-5 font-tajawal">
        <p class="font-bold text-gray-900"><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></p>
        <p class="text-xs text-gray-500 mt-1"><?php echo e($employee->position); ?> · <?php echo e($employee->email); ?></p>
        <?php $empRole = \App\Services\EmployeeRoleService::resolve($employee); ?>
        <p class="text-xs mt-2">
            <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700"><?php echo e($empRole['label']); ?></span>
        </p>
        <span class="inline-block mt-3 text-xs px-2 py-1 rounded <?php echo e($employee->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600'); ?>"><?php echo e($employee->status === 'active' ? 'نشط' : $employee->status); ?></span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="col-span-full text-center text-gray-500 py-10">لا يوجد فريق تسويق بعد. <?php if($canManage): ?> أضف موظفاً من زر «إضافة موظف تسويق». <?php endif; ?></p>
    <?php endif; ?>
</div>
<div class="mt-4"><?php echo e($employees->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\team\index.blade.php ENDPATH**/ ?>
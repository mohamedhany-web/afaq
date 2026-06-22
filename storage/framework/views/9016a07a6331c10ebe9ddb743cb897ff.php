
<?php $__env->startSection('page-title', 'صلاحيات الدور — ' . ($meta['label'] ?? $role->name)); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $color = $meta['color'] ?? $themeColor;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $meta['label'] ?? $role->name,
    'subtitle' => 'صلاحيات الدور الافتراضية — تُطبَّق على كل مستخدم بهذا الدور ما لم يُخصَّص له',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'actionUrl' => route('roles.index'),
    'actionLabel' => 'العودة للأدوار',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php echo $__env->make('roles.partials.permission-sync-status', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal"><?php echo e($meta['label']); ?></h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal"><?php echo e($meta['description'] ?? ''); ?> — <code dir="ltr"><?php echo e($role->name); ?></code></p>
        </div>
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full" style="background: <?php echo e($color); ?>15; color: <?php echo e($color); ?>;">
            <?php echo e(count($rolePermissions)); ?> صلاحية مفعّلة
        </span>
    </div>
    <div class="p-5 sm:p-6">
        <form action="<?php echo e(route('roles.update-permissions', $role)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('roles.partials.permission-matrix', [
                'permissionGroups' => $permissionGroups,
                'permissionModules' => $permissionModules,
                'checkedPermissions' => $rolePermissions,
                'showSource' => false,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex flex-wrap gap-3 justify-between pt-4 border-t border-gray-100">
                <div class="flex gap-2">
                    <button type="button" onclick="selectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">تحديد الكل</button>
                    <button type="button" onclick="deselectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">إلغاء الكل</button>
                </div>
                <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: <?php echo e($themeColor); ?>;">
                    حفظ صلاحيات الدور
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\roles\role-permissions.blade.php ENDPATH**/ ?>
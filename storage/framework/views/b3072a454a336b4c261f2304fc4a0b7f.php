<?php $__env->startSection('page-title', 'دور المستخدم — ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $displayMeta = $displayRole ? \App\Services\CrmRoleCatalogService::roleMeta($displayRole) : null;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $user->name,
    'subtitle' => 'تعيين الدور + تخصيص صلاحيات CRUD — ' . $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('roles.index'),
    'actionLabel' => 'العودة للأدوار',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php echo $__env->make('roles.partials.permission-sync-status', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 font-tajawal">الدور الوظيفي</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">
                <?php if($workspaceMeta): ?>
                    مساحة العمل: <strong><?php echo e($workspaceMeta['label']); ?></strong> —
                <?php endif; ?>
                الدور الحالي:
                <?php if($displayMeta): ?>
                    <strong style="color: <?php echo e($displayMeta['color']); ?>;"><?php echo e($displayMeta['label']); ?></strong>
                <?php else: ?>
                    <span class="text-gray-400">غير محدد</span>
                <?php endif; ?>
                <?php if($user->employee?->department): ?>
                    — القسم: <strong><?php echo e($user->employee->department->name); ?></strong>
                <?php endif; ?>
            </p>
            <p class="text-[11px] text-amber-700 mt-2 font-tajawal bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                تغيير الدور لا يمسح التخصيصات الفردية. بعد الحفظ، حدّث الصفحة (أو سجّل دخول المستخدم من جديد) لترى السايدبار محدّثاً.
            </p>
        </div>
        <div class="p-5 sm:p-6">
            <form action="<?php echo e(route('roles.assign-role', $user)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name);
                            $color = $meta['color'] ?? $themeColor;
                            $isSelected = $displayRole === $role->name;
                        ?>
                        <label class="relative flex items-start p-4 border-2 rounded-xl cursor-pointer transition font-tajawal"
                               style="border-color: <?php echo e($isSelected ? $color : '#e5e7eb'); ?>; background: <?php echo e($isSelected ? $color . '08' : '#fff'); ?>;">
                            <input type="radio" name="role" value="<?php echo e($role->name); ?>" class="mt-1 ml-3" <?php echo e($isSelected ? 'checked' : ''); ?> onchange="this.form.submit()">
                            <div>
                                <p class="font-bold text-sm text-gray-900"><?php echo e($meta['label']); ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e($meta['description']); ?></p>
                                <p class="text-xs mt-2 font-semibold" style="color: <?php echo e($color); ?>;"><?php echo e($role->permissions->count()); ?> صلاحية افتراضية</p>
                            </div>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 font-tajawal">معاينة السايدبار</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">ما يظهر للمستخدم حسب صلاحياته الفعّالة</p>
        </div>
        <div class="p-4 max-h-[420px] overflow-y-auto">
            <ul class="space-y-1.5 text-sm font-tajawal">
                <?php $__currentLoopData = $sidebarPreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center gap-2 px-3 py-2 rounded-lg <?php echo e($item['visible'] ? 'bg-green-50 text-green-800' : 'bg-gray-50 text-gray-400 line-through'); ?>">
                        <?php if($item['visible']): ?>
                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?php else: ?>
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <?php endif; ?>
                        <span><?php echo e($item['label']); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 font-tajawal">مصفوفة الصلاحيات (CRUD) — <?php echo e($permissions->count()); ?> صلاحية</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">
            <span class="text-green-600">دور</span> = من الدور الافتراضي —
            <span class="text-blue-600">+</span> = مضافة للمستخدم —
            <span class="text-red-600">−</span> = مُعطّلة رغم وجودها في الدور
        </p>
    </div>
    <div class="p-5 sm:p-6">
        <form action="<?php echo e(route('roles.assign-permissions', $user)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('roles.partials.permission-matrix', [
                'permissionGroups' => $permissionGroups,
                'permissionModules' => $permissionModules,
                'checkedPermissions' => $userPermissions,
                'rolePermissions' => $rolePermissions,
                'customPermissionsMap' => $customPermissionsMap,
                'showSource' => true,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex flex-wrap gap-3 justify-between pt-4 border-t border-gray-100">
                <div class="flex gap-2">
                    <button type="button" onclick="selectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">تحديد الكل</button>
                    <button type="button" onclick="deselectAllPermissions()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">إلغاء الكل</button>
                </div>
                <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: <?php echo e($themeColor); ?>;">حفظ تخصيص المستخدم</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\roles\user-permissions.blade.php ENDPATH**/ ?>
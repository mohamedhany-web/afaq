<?php $__env->startSection('page-title', 'دور المستخدم — ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $displayMeta = $displayRole ? \App\Services\CrmRoleCatalogService::roleMeta($displayRole) : null;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $user->name,
    'subtitle' => 'تعيين الدور وتخصيص الصلاحيات — ' . $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('roles.index'),
    'actionLabel' => 'العودة للأدوار',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 font-tajawal">الدور الوظيفي</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">
            الدور الحالي:
            <?php if($displayMeta): ?>
                <strong style="color: <?php echo e($displayMeta['color']); ?>;"><?php echo e($displayMeta['label']); ?></strong>
            <?php else: ?>
                <span class="text-gray-400">غير محدد</span>
            <?php endif; ?>
        </p>
    </div>
    <div class="p-5 sm:p-6">
        <form action="<?php echo e(route('roles.assign-role', $user)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
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
                            <p class="text-xs mt-2 font-semibold" style="color: <?php echo e($color); ?>;"><?php echo e($role->permissions->count()); ?> صلاحية</p>
                        </div>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </form>
    </div>
</div>

<?php if($userRole): ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 mb-6 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-900 font-tajawal">صلاحيات الدور الافتراضية</h3>
        <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-800 font-tajawal"><?php echo e($userRole->permissions->count()); ?> صلاحية</span>
    </div>
    <div class="p-5 sm:p-6 flex flex-wrap gap-2">
        <?php $__currentLoopData = $userRole->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="text-xs px-3 py-1.5 rounded-lg bg-green-50 text-green-800 border border-green-100 font-tajawal">
                <?php echo e(\App\Helpers\RoleHelper::getPermissionName($permission->name)); ?>

            </span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 font-tajawal">تخصيص الصلاحيات</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">يمكنك إضافة أو إزالة صلاحيات فوق الدور الأساسي</p>
    </div>
    <div class="p-5 sm:p-6">
        <form action="<?php echo e(route('roles.assign-permissions', $user)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="text" id="permissionSearch" placeholder="ابحث عن صلاحية…"
                   class="w-full mb-6 border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-tajawal"
                   onkeyup="filterPermissions()">

            <?php $__currentLoopData = $permissionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-6 permission-group">
                    <h4 class="text-sm font-bold text-gray-700 mb-3 font-tajawal border-r-4 pr-3" style="border-color: <?php echo e($themeColor); ?>;"><?php echo e($group['label']); ?></h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php $__currentLoopData = $group['permissions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $permission = $permissions->firstWhere('name', $permName);
                                if (!$permission) continue;
                                $isChecked = in_array($permName, $userPermissions);
                                $isFromRole = in_array($permName, $rolePermissions ?? []);
                                $hasCustomOverride = isset($customPermissionsMap[$permName]);
                                $isDisabled = $hasCustomOverride && !$customPermissionsMap[$permName];
                            ?>
                            <label class="permission-item flex items-start p-3 rounded-xl border-2 cursor-pointer font-tajawal
                                <?php echo e($isChecked ? 'border-green-200 bg-green-50' : ($isDisabled ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50')); ?>">
                                <input type="checkbox" name="permissions[]" value="<?php echo e($permName); ?>" class="mt-1 ml-2" <?php echo e($isChecked ? 'checked' : ''); ?>>
                                <div class="flex-1 min-w-0">
                                    <p class="permission-name text-sm font-medium text-gray-900"><?php echo e(\App\Helpers\RoleHelper::getPermissionName($permName)); ?></p>
                                    <?php if($isFromRole && !$hasCustomOverride): ?>
                                        <span class="text-[10px] text-green-600">من الدور</span>
                                    <?php elseif($isDisabled): ?>
                                        <span class="text-[10px] text-red-600">معطّلة</span>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="flex flex-wrap gap-3 justify-between pt-4 border-t border-gray-100">
                <div class="flex gap-2">
                    <button type="button" onclick="selectAll()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">تحديد الكل</button>
                    <button type="button" onclick="deselectAll()" class="px-4 py-2 rounded-lg bg-gray-100 text-sm font-tajawal">إلغاء الكل</button>
                </div>
                <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: <?php echo e($themeColor); ?>;">حفظ الصلاحيات</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterPermissions() {
    const q = document.getElementById('permissionSearch').value.toLowerCase();
    document.querySelectorAll('.permission-item').forEach(el => {
        const name = el.querySelector('.permission-name')?.textContent.toLowerCase() || '';
        el.style.display = name.includes(q) ? '' : 'none';
    });
}
function selectAll() { document.querySelectorAll('input[name="permissions[]"]').forEach(c => c.checked = true); }
function deselectAll() { document.querySelectorAll('input[name="permissions[]"]').forEach(c => c.checked = false); }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\roles\user-permissions.blade.php ENDPATH**/ ?>
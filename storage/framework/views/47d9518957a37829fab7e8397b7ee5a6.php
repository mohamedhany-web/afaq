
<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $inputName = $inputName ?? 'permissions[]';
    $rolePermissions = $rolePermissions ?? [];
    $customPermissionsMap = $customPermissionsMap ?? [];
    $showSource = $showSource ?? true;
?>

<input type="text" id="permissionSearch" placeholder="ابحث عن وحدة أو صلاحية…"
       class="w-full mb-6 border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-tajawal"
       onkeyup="filterPermissionMatrix()">

<?php $__currentLoopData = $permissionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="mb-8 permission-group-block" data-group="<?php echo e($groupKey); ?>">
        <h4 class="text-sm font-bold text-gray-800 mb-4 font-tajawal border-r-4 pr-3" style="border-color: <?php echo e($themeColor); ?>;">
            <?php echo e($group['label']); ?>

        </h4>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm font-tajawal permission-matrix-table min-w-[640px]">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-right px-4 py-3 w-1/3">الوحدة</th>
                        <th class="text-center px-3 py-3">عرض</th>
                        <th class="text-center px-3 py-3">إنشاء</th>
                        <th class="text-center px-3 py-3">تعديل</th>
                        <th class="text-center px-3 py-3">حذف</th>
                        <th class="text-right px-4 py-3">صلاحيات إضافية</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $group['modules'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="permission-module-row hover:bg-gray-50/50" data-module-label="<?php echo e($module['label']); ?>">
                            <td class="px-4 py-3 font-semibold text-gray-900 module-label"><?php echo e($module['label']); ?></td>
                            <?php $__currentLoopData = ['view', 'create', 'edit', 'delete']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $permKey = $module[$action] ?? null;
                                ?>
                                <td class="px-3 py-3 text-center">
                                    <?php if($permKey): ?>
                                        <?php
                                            $isChecked = in_array($permKey, $checkedPermissions);
                                            $isFromRole = in_array($permKey, $rolePermissions);
                                            $hasOverride = isset($customPermissionsMap[$permKey]);
                                            $isDisabled = $hasOverride && !$customPermissionsMap[$permKey];
                                        ?>
                                        <label class="inline-flex flex-col items-center gap-1 cursor-pointer permission-cell" data-perm="<?php echo e($permKey); ?>">
                                            <input type="checkbox" name="<?php echo e($inputName); ?>" value="<?php echo e($permKey); ?>" class="rounded border-gray-300"
                                                   <?php echo e($isChecked ? 'checked' : ''); ?>>
                                            <?php if($showSource && $isFromRole && !$hasOverride): ?>
                                                <span class="text-[9px] text-green-600">دور</span>
                                            <?php elseif($showSource && $hasOverride && $isChecked): ?>
                                                <span class="text-[9px] text-blue-600">+</span>
                                            <?php elseif($showSource && $isDisabled): ?>
                                                <span class="text-[9px] text-red-600">−</span>
                                            <?php endif; ?>
                                        </label>
                                    <?php else: ?>
                                        <span class="text-gray-300">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="px-4 py-3">
                                <?php if(!empty($module['extras'])): ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php $__currentLoopData = $module['extras']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extraKey => $extraLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $permKey = is_int($extraKey) ? $extraLabel : $extraKey;
                                                $label = is_int($extraKey) ? \App\Helpers\RoleHelper::getPermissionName($extraLabel) : $extraLabel;
                                                $isChecked = in_array($permKey, $checkedPermissions);
                                                $isFromRole = in_array($permKey, $rolePermissions);
                                                $hasOverride = isset($customPermissionsMap[$permKey]);
                                            ?>
                                            <label class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg border text-xs permission-cell <?php echo e($isChecked ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50'); ?>" data-perm="<?php echo e($permKey); ?>">
                                                <input type="checkbox" name="<?php echo e($inputName); ?>" value="<?php echo e($permKey); ?>" <?php echo e($isChecked ? 'checked' : ''); ?>>
                                                <span class="permission-name"><?php echo e($label); ?></span>
                                                <?php if($showSource && $isFromRole && !$hasOverride): ?>
                                                    <span class="text-[9px] text-green-600">دور</span>
                                                <?php endif; ?>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
function filterPermissionMatrix() {
    const q = (document.getElementById('permissionSearch')?.value || '').toLowerCase();
    document.querySelectorAll('.permission-module-row').forEach(row => {
        const label = (row.dataset.moduleLabel || '').toLowerCase();
        const cells = Array.from(row.querySelectorAll('.permission-cell')).map(c => (c.dataset.perm || '').toLowerCase());
        const match = label.includes(q) || cells.some(p => p.includes(q));
        row.style.display = match ? '' : 'none';
    });
}
function selectAllPermissions() {
    document.querySelectorAll('input[name="<?php echo e($inputName); ?>"]').forEach(c => c.checked = true);
}
function deselectAllPermissions() {
    document.querySelectorAll('input[name="<?php echo e($inputName); ?>"]').forEach(c => c.checked = false);
}
</script>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\roles\partials\permission-matrix.blade.php ENDPATH**/ ?>
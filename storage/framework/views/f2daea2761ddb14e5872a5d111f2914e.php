<?php
    $selected = old('role', $selected ?? '');
    $inputName = $inputName ?? 'role';
    $workspaceGroups = $workspaceGroups ?? \App\Services\CrmRoleCatalogService::workspaceGroups();
    $roleHints = $roleHints ?? \App\Services\CrmRoleCatalogService::roleAssignmentHints();
?>
<div x-data="{
    selected: <?php echo \Illuminate\Support\Js::from($selected)->toHtml() ?>,
    hints: <?php echo \Illuminate\Support\Js::from($roleHints)->toHtml() ?>,
    currentHint() { return this.hints[this.selected] || null; }
}">
    <div class="space-y-6">
        <?php $__currentLoopData = $workspaceGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $groupRoles = $assignableRoles->filter(fn ($role) => in_array($role->name, $group['roles'], true));
        ?>
        <?php if($groupRoles->isNotEmpty()): ?>
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-3 h-3 rounded-full shrink-0" style="background: <?php echo e($group['color']); ?>"></span>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 font-tajawal"><?php echo e($group['label']); ?></h3>
                    <p class="text-xs text-gray-500 font-tajawal"><?php echo e($group['description']); ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <?php $__currentLoopData = $groupRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $meta = \App\Services\CrmRoleCatalogService::roleMeta($role->name); $color = $meta['color'] ?? $group['color']; ?>
                <label class="cursor-pointer block" @click="selected = <?php echo \Illuminate\Support\Js::from($role->name)->toHtml() ?>">
                    <input type="radio" name="<?php echo e($inputName); ?>" value="<?php echo e($role->name); ?>" class="sr-only"
                           <?php if($selected === $role->name): echo 'checked'; endif; ?> @change="selected = <?php echo \Illuminate\Support\Js::from($role->name)->toHtml() ?>" required>
                    <div class="p-4 rounded-2xl border-2 bg-white transition-all h-full"
                         :class="selected === <?php echo \Illuminate\Support\Js::from($role->name)->toHtml() ?> ? 'shadow-md' : 'border-gray-200'"
                         :style="selected === <?php echo \Illuminate\Support\Js::from($role->name)->toHtml() ?> ? 'border-color: <?php echo e($color); ?>; background: <?php echo e($color); ?>08' : ''">
                        <div class="flex items-start gap-3">
                            <span class="w-3 h-3 rounded-full mt-1 shrink-0" style="background: <?php echo e($color); ?>"></span>
                            <div>
                                <p class="font-bold text-gray-900 text-sm"><?php echo e($meta['label'] ?? $role->name); ?></p>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed"><?php echo e($meta['description'] ?? ''); ?></p>
                            </div>
                        </div>
                    </div>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div x-show="currentHint()" x-cloak class="mt-5 p-4 rounded-2xl border-2 border-dashed font-tajawal"
         :style="currentHint() ? 'border-color:' + currentHint().color + '55; background:' + currentHint().color + '08' : ''">
        <p class="text-xs font-bold text-gray-500 mb-1">ما الذي يشمله هذا الدور؟</p>
        <p class="text-sm font-bold text-gray-900" x-text="currentHint()?.label"></p>
        <p class="text-sm text-gray-600 mt-1" x-text="currentHint()?.description"></p>
        <p class="text-xs text-gray-500 mt-2">
            القسم: <span class="font-semibold" x-text="currentHint()?.workspace_label"></span>
            <template x-if="currentHint()?.default_department">
                <span> · القسم التوظيفي: <span class="font-semibold" x-text="currentHint()?.default_department"></span></span>
            </template>
            <template x-if="currentHint()?.needs_employee">
                <span> · يُنشأ سجل موظف عند التفعيل</span>
            </template>
        </p>
    </div>
</div>
<?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\users\partials\role-picker.blade.php ENDPATH**/ ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6 font-tajawal">
    <form method="GET" action="<?php echo e($action); ?>" class="flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
        <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="<?php echo e($field['class'] ?? 'flex-1 min-w-[140px]'); ?>">
            <label class="block text-xs font-bold text-gray-500 mb-1.5"><?php echo e($field['label']); ?></label>
            <?php if(($field['type'] ?? 'text') === 'select'): ?>
            <select name="<?php echo e($field['name']); ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $optLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php if(request($field['name']) == $val && $val !== ''): echo 'selected'; endif; ?>><?php echo e($optLabel); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php elseif(($field['type'] ?? '') === 'date'): ?>
            <input type="date" name="<?php echo e($field['name']); ?>" value="<?php echo e(request($field['name'])); ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            <?php else: ?>
            <input type="text" name="<?php echo e($field['name']); ?>" value="<?php echo e(request($field['name'])); ?>" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
        <?php if(collect($fields)->pluck('name')->filter(fn ($n) => request($n))->isNotEmpty()): ?>
        <a href="<?php echo e($action); ?>" class="px-5 py-2.5 rounded-xl border text-sm font-semibold text-gray-600 hover:bg-gray-50">إعادة تعيين</a>
        <?php endif; ?>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\accounting\partials\filter-bar.blade.php ENDPATH**/ ?>
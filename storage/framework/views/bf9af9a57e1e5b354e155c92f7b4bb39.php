<?php
    $isEdit = isset($team);
    $selectedMembers = old('member_ids', $isEdit ? $team->members->pluck('id')->all() : []);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900" style="<?php echo e($headerStyle); ?>">
        بيانات الفريق
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="sm:col-span-2">
            <label class="<?php echo e($label); ?>">اسم الفريق *</label>
            <input name="name" value="<?php echo e(old('name', $team->name ?? '')); ?>" required class="<?php echo e($input); ?>" placeholder="مثال: فريق المبيعات — القاهرة">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">مدير المبيعات *</label>
            <?php if(!empty($lockManager)): ?>
                <input type="hidden" name="manager_id" value="<?php echo e(auth()->id()); ?>">
                <div class="<?php echo e($input); ?> bg-gray-50 text-gray-800 font-semibold"><?php echo e(auth()->user()->name); ?></div>
                <p class="mt-1 text-[11px] text-gray-400 font-tajawal">أنت مدير هذا الفريق — لا يمكن تعيينه لمدير آخر</p>
            <?php else: ?>
                <select name="manager_id" required class="<?php echo e($input); ?>">
                    <option value="">— اختر المدير —</option>
                    <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php if((int) old('manager_id', $team->manager_id ?? '') === $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php endif; ?>
            <?php $__errorArgs = ['manager_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php if($isEdit): ?>
        <div class="flex items-end">
            <label class="flex items-center gap-2 cursor-pointer font-tajawal text-sm text-gray-700 pb-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 w-4 h-4"
                       style="accent-color: <?php echo e($themeColor); ?>;"
                       <?php if(old('is_active', $team->is_active)): echo 'checked'; endif; ?>>
                <span>فريق نشط</span>
            </label>
        </div>
        <?php endif; ?>
        <div class="sm:col-span-2">
            <label class="<?php echo e($label); ?>">وصف الفريق</label>
            <textarea name="description" rows="3" class="<?php echo e($input); ?>" placeholder="اختصاص الفريق، المنطقة، أو أهدافه..."><?php echo e(old('description', $team->description ?? '')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2" style="<?php echo e($headerStyle); ?>">
        <span class="font-tajawal font-bold text-gray-900">أعضاء الفريق — مندوبو المبيعات</span>
        <span class="text-xs text-gray-500 font-tajawal">اختر مندوباً واحداً أو أكثر</span>
    </div>
    <div class="p-5 sm:p-6">
        <?php if($agents->isEmpty()): ?>
        <p class="text-sm text-gray-400 font-tajawal text-center py-6">لا يوجد مندوبو مبيعات مسجلون في النظام</p>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto">
            <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-100 hover:border-gray-200 cursor-pointer transition-colors has-[:checked]:border-current"
                   style="--tw-border-opacity: 1;">
                <input type="checkbox" name="member_ids[]" value="<?php echo e($agent->id); ?>"
                       class="rounded border-gray-300 w-4 h-4 shrink-0"
                       style="accent-color: <?php echo e($themeColor); ?>;"
                       <?php if(in_array($agent->id, $selectedMembers)): echo 'checked'; endif; ?>>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-gray-900 font-tajawal truncate"><?php echo e($agent->name); ?></span>
                    <?php if($agent->email): ?>
                    <span class="block text-[10px] text-gray-400 truncate" dir="ltr"><?php echo e($agent->email); ?></span>
                    <?php endif; ?>
                </span>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\teams\partials\form.blade.php ENDPATH**/ ?>
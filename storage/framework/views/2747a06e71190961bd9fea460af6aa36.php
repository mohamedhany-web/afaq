<?php $p = $portfolio ?? null; $input='w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm'; $label='block text-xs font-bold text-gray-500 mb-1.5'; ?>
<div class="bg-white rounded-2xl border p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2"><label class="<?php echo e($label); ?>">العنوان *</label><input name="title" required value="<?php echo e(old('title', $p->title ?? '')); ?>" class="<?php echo e($input); ?>"></div>
    <div class="sm:col-span-2"><label class="<?php echo e($label); ?>">الوصف</label><textarea name="description" rows="3" class="<?php echo e($input); ?>"><?php echo e(old('description', $p->description ?? '')); ?></textarea></div>
    <div><label class="<?php echo e($label); ?>">المدينة</label><input name="city" value="<?php echo e(old('city', $p->city ?? '')); ?>" class="<?php echo e($input); ?>"></div>
    <div><label class="<?php echo e($label); ?>">الموقع</label><input name="location" value="<?php echo e(old('location', $p->location ?? '')); ?>" class="<?php echo e($input); ?>"></div>
    <div><label class="<?php echo e($label); ?>">نوع المشروع</label><input name="project_type" value="<?php echo e(old('project_type', $p->project_type ?? '')); ?>" class="<?php echo e($input); ?>"></div>
    <div><label class="<?php echo e($label); ?>">السنة</label><input type="number" name="year" value="<?php echo e(old('year', $p->year ?? '')); ?>" class="<?php echo e($input); ?>"></div>
    <div class="flex items-center gap-2 pt-6"><input type="checkbox" name="is_published" value="1" <?php if(old('is_published', $p->is_published ?? true)): echo 'checked'; endif; ?>><label class="text-sm font-semibold">منشور للعرض</label></div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\portfolio\partials\form.blade.php ENDPATH**/ ?>
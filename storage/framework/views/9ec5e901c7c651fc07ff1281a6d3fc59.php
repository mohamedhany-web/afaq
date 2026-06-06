<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $campaign = $campaign ?? null;
?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="<?php echo e($label); ?>">اسم الحملة *</label>
        <input type="text" name="name" value="<?php echo e(old('name', $campaign?->name)); ?>" required class="<?php echo e($input); ?>">
    </div>
    <div>
        <label class="<?php echo e($label); ?>">القناة *</label>
        <select name="channel" required class="<?php echo e($input); ?>">
            <?php $__currentLoopData = $channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $chLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php if(old('channel', $campaign?->channel) === $key): echo 'selected'; endif; ?>><?php echo e($chLabel); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="<?php echo e($label); ?>">الحالة *</label>
        <select name="status" required class="<?php echo e($input); ?>">
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php if(old('status', $campaign?->status ?? 'draft') === $key): echo 'selected'; endif; ?>><?php echo e($stLabel); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="<?php echo e($label); ?>">الميزانية</label>
        <input type="number" step="0.01" name="budget" value="<?php echo e(old('budget', $campaign?->budget)); ?>" class="<?php echo e($input); ?>">
    </div>
    <div>
        <label class="<?php echo e($label); ?>">المصروف</label>
        <input type="number" step="0.01" name="spent_amount" value="<?php echo e(old('spent_amount', $campaign?->spent_amount ?? 0)); ?>" class="<?php echo e($input); ?>">
    </div>
    <div>
        <label class="<?php echo e($label); ?>">هدف Leads</label>
        <input type="number" name="target_leads" value="<?php echo e(old('target_leads', $campaign?->target_leads)); ?>" class="<?php echo e($input); ?>">
    </div>
    <div>
        <label class="<?php echo e($label); ?>">المشروع العقاري</label>
        <select name="project_id" class="<?php echo e($input); ?>">
            <option value="">— بدون —</option>
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($project->id); ?>" <?php if(old('project_id', $campaign?->project_id) == $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="<?php echo e($label); ?>">مدير الحملة</label>
        <select name="manager_id" class="<?php echo e($input); ?>">
            <option value="">— تلقائي —</option>
            <?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manager): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($manager->id); ?>" <?php if(old('manager_id', $campaign?->manager_id) == $manager->id): echo 'selected'; endif; ?>><?php echo e($manager->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="<?php echo e($label); ?>">تاريخ البداية</label>
        <input type="date" name="start_date" value="<?php echo e(old('start_date', $campaign?->start_date?->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
    </div>
    <div>
        <label class="<?php echo e($label); ?>">تاريخ النهاية</label>
        <input type="date" name="end_date" value="<?php echo e(old('end_date', $campaign?->end_date?->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
    </div>
    <div class="sm:col-span-2">
        <label class="<?php echo e($label); ?>">الوصف</label>
        <textarea name="description" rows="3" class="<?php echo e($input); ?>"><?php echo e(old('description', $campaign?->description)); ?></textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="<?php echo e($label); ?>">ملاحظات</label>
        <textarea name="notes" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('notes', $campaign?->notes)); ?></textarea>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/campaigns/partials/form.blade.php ENDPATH**/ ?>
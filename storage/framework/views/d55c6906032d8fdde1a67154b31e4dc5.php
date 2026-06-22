
<?php $__env->startSection('page-title', 'مهمة تسويقية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', ['title' => 'مهمة تسويقية جديدة', 'actionUrl' => route('marketing.activities.index'), 'actionLabel' => 'الجدول'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form action="<?php echo e(route('marketing.activities.store')); ?>" method="POST" class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal">
    <?php echo csrf_field(); ?>
    <div><label class="<?php echo e($label); ?>">العنوان *</label><input name="title" required class="<?php echo e($input); ?>" value="<?php echo e(old('title')); ?>"></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="<?php echo e($label); ?>">النوع</label><select name="type" class="<?php echo e($input); ?>"><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div><label class="<?php echo e($label); ?>">الأولوية</label><select name="priority" class="<?php echo e($input); ?>"><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div><label class="<?php echo e($label); ?>">الحالة</label><select name="status" class="<?php echo e($input); ?>"><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div><label class="<?php echo e($label); ?>">الحملة</label><select name="campaign_id" class="<?php echo e($input); ?>"><option value="">—</option><?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(old('campaign_id', $prefillCampaign) == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <?php if(!empty($plans) && count($plans)): ?>
        <div><label class="<?php echo e($label); ?>">خطة الشهر</label><select name="marketing_plan_id" class="<?php echo e($input); ?>"><option value="">—</option><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if(old('marketing_plan_id', $prefillPlan) == $p->id): echo 'selected'; endif; ?>><?php echo e($p->title); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <?php endif; ?>
        <div><label class="<?php echo e($label); ?>">المسؤول</label><select name="assigned_to" class="<?php echo e($input); ?>"><option value="">أنا</option><?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div><label class="<?php echo e($label); ?>">موعد التنفيذ</label><input type="datetime-local" name="due_at" class="<?php echo e($input); ?>" value="<?php echo e(old('due_at')); ?>"></div>
        <div><label class="<?php echo e($label); ?>">التكرار الدوري</label><select name="recurrence" class="<?php echo e($input); ?>"><?php $__currentLoopData = $recurrences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div><label class="<?php echo e($label); ?>">كل (فترة)</label><input type="number" name="recurrence_interval" min="1" max="12" value="<?php echo e(old('recurrence_interval', 1)); ?>" class="<?php echo e($input); ?>"></div>
    </div>
    <div><label class="<?php echo e($label); ?>">الوصف</label><textarea name="description" rows="3" class="<?php echo e($input); ?>"><?php echo e(old('description')); ?></textarea></div>
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold" style="background:<?php echo e($themeColor); ?>">حفظ</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\activities\create.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'إضافة Lead'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', ['title' => 'إضافة عميل محتمل', 'actionUrl' => route('marketing.leads.index'), 'actionLabel' => 'القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form action="<?php echo e(route('marketing.leads.store')); ?>" method="POST" class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal max-w-2xl">
    <?php echo csrf_field(); ?>
    <div><label class="<?php echo e($label); ?>">الاسم (اختياري)</label><input name="name" class="<?php echo e($input); ?>" value="<?php echo e(old('name')); ?>"></div>
    <div><label class="<?php echo e($label); ?>">الهاتف *</label><input name="phone" required class="<?php echo e($input); ?>" value="<?php echo e(old('phone')); ?>"></div>
    <div><label class="<?php echo e($label); ?>">البريد</label><input type="email" name="email" class="<?php echo e($input); ?>" value="<?php echo e(old('email')); ?>"></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="<?php echo e($label); ?>">الحملة</label><select name="marketing_campaign_id" class="<?php echo e($input); ?>"><option value="">—</option><?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(old('marketing_campaign_id', $prefillCampaign)==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div><label class="<?php echo e($label); ?>">المصدر</label><select name="lead_source" class="<?php echo e($input); ?>"><?php $__currentLoopData = $leadSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>"><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
    </div>
    <div><label class="<?php echo e($label); ?>">ملاحظات</label><textarea name="notes" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('notes')); ?></textarea></div>
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold" style="background:<?php echo e($themeColor); ?>">حفظ</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/leads/create.blade.php ENDPATH**/ ?>
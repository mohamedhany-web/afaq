
<?php $__env->startSection('page-title', 'تعديل عقد وكيل'); ?>
<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<?php echo $__env->make('crm.partials.page-header', ['title' => 'تعديل عقد: ' . $contract->user?->name, 'subtitle' => 'تحديث بيانات العقد والتارجت', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<form method="POST" action="<?php echo e(route('crm.freelance-agents.update', $contract)); ?>" class="space-y-6"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<?php echo $__env->make('crm.freelance-agents.partials.form', ['contract' => $contract], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="flex gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%);">حفظ</button>
    <a href="<?php echo e(route('crm.freelance-agents.show', $contract)); ?>" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold font-tajawal">رجوع</a>
</div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\freelance-agents\edit.blade.php ENDPATH**/ ?>
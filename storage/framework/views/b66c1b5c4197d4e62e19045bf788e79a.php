
<?php $__env->startSection('page-title', 'عقد وكيل جديد'); ?>
<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<?php echo $__env->make('crm.partials.page-header', ['title' => 'تسجيل وكيل عقاري مستقل', 'subtitle' => 'Freelance Agent Agreement — يُفعّل هيكل العمولات تلقائياً', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<form method="POST" action="<?php echo e(route('crm.freelance-agents.store')); ?>" class="space-y-6"><?php echo csrf_field(); ?>
<?php echo $__env->make('crm.freelance-agents.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="flex gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%);">حفظ العقد</button>
    <a href="<?php echo e(route('crm.freelance-agents.index')); ?>" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold font-tajawal">إلغاء</a>
</div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\freelance-agents\create.blade.php ENDPATH**/ ?>
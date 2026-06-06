
<?php $__env->startSection('page-title', 'حملة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<?php echo $__env->make('crm.partials.page-header', ['title' => 'إنشاء حملة تسويقية', 'subtitle' => 'قسم التسويق', 'actionUrl' => route('marketing.campaigns.index'), 'actionLabel' => 'العودة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form action="<?php echo e(route('marketing.campaigns.store')); ?>" method="POST" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('marketing.campaigns.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold text-sm font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ الحملة</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/campaigns/create.blade.php ENDPATH**/ ?>
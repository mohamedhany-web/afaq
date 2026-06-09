
<?php $__env->startSection('page-title', 'تعديل حملة'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<?php echo $__env->make('crm.partials.page-header', ['title' => 'تعديل: ' . $campaign->name, 'actionUrl' => route('marketing.campaigns.show', $campaign), 'actionLabel' => 'عرض الحملة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form action="<?php echo e(route('marketing.campaigns.update', $campaign)); ?>" method="POST" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('marketing.campaigns.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button type="submit" class="px-8 py-3 rounded-xl text-white font-semibold text-sm font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ التعديلات</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\campaigns\edit.blade.php ENDPATH**/ ?>


<?php $__env->startSection('page-title', 'المساعدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">مركز المساعدة</h1>
        <a href="<?php echo e(route('client.dashboard')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900">العودة للوحة</a>
    </div>
    <?php echo $__env->make('client-portal.partials.faq', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\help.blade.php ENDPATH**/ ?>
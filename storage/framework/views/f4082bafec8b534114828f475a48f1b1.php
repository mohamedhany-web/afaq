
<?php $__env->startSection('page-title', 'مشروع جديد'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-6">إضافة مشروع</h1>
<form method="POST" action="<?php echo e(route('developer.projects.store')); ?>"><?php echo csrf_field(); ?>
<?php echo $__env->make('developer-portal.projects.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<button type="submit" class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ المشروع</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\projects\create.blade.php ENDPATH**/ ?>
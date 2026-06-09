
<?php $__env->startSection('page-title', 'تعديل سابقة أعمال'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-6">تعديل: <?php echo e($portfolio->title); ?></h1>
<form method="POST" action="<?php echo e(route('developer.portfolio.update', $portfolio)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<?php echo $__env->make('developer-portal.portfolio.partials.form', ['portfolio' => $portfolio], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<button class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\portfolio\edit.blade.php ENDPATH**/ ?>
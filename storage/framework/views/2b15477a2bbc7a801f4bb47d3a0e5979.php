
<?php $__env->startSection('page-title', 'تعديل مشروع'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-6">تعديل: <?php echo e($project->name); ?></h1>
<form method="POST" action="<?php echo e(route('developer.projects.update', $project)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<?php echo $__env->make('developer-portal.projects.partials.form', ['project' => $project], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="mt-4 flex gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
    <a href="<?php echo e(route('developer.projects.show', $project)); ?>" class="px-6 py-3 rounded-xl border text-sm font-bold">رجوع</a>
</div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\projects\edit.blade.php ENDPATH**/ ?>
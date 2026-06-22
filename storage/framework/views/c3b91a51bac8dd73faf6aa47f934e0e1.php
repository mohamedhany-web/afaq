
<?php $__env->startSection('page-title', 'مشاريعي'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex justify-between items-center gap-3">
    <h1 class="text-2xl font-bold">مشاريعي</h1>
    <?php if(auth('developer')->user()->canManageProjects()): ?>
    <a href="<?php echo e(route('developer.projects.create')); ?>" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:var(--brand)">+ مشروع جديد</a>
    <?php endif; ?>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('developer.projects.show', $p)); ?>" class="bg-white rounded-2xl border p-5 hover:shadow-lg transition block">
        <div class="font-bold text-lg"><?php echo e($p->name); ?></div>
        <div class="text-sm text-gray-500 mt-1"><?php echo e($p->city); ?> <?php if($p->location): ?>— <?php echo e($p->location); ?><?php endif; ?></div>
        <div class="mt-3 text-xs font-semibold text-gray-600"><?php echo e($p->total_units); ?> وحدة · <?php echo e($p->available_units); ?> متاح</div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="col-span-full text-gray-400">لا مشاريع</div><?php endif; ?>
</div>
<div class="mt-4"><?php echo e($projects->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\projects\index.blade.php ENDPATH**/ ?>
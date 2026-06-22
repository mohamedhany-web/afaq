
<?php $__env->startSection('page-title', 'سابقة الأعمال'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">سابقة الأعمال</h1>
<?php if(auth('developer')->user()->canManagePortfolio()): ?><a href="<?php echo e(route('developer.portfolio.create')); ?>" class="px-4 py-2 rounded-xl text-white text-sm font-bold" style="background:var(--brand)">+ إضافة</a><?php endif; ?></div>
<div class="space-y-3">
<?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="bg-white rounded-2xl border p-5 flex justify-between gap-3">
    <div><div class="font-bold"><?php echo e($item->title); ?></div><div class="text-sm text-gray-500"><?php echo e($item->city); ?> <?php echo e($item->location); ?> <?php if($item->year): ?>— <?php echo e($item->year); ?><?php endif; ?></div><p class="text-sm text-gray-600 mt-2"><?php echo e(Str::limit($item->description, 120)); ?></p></div>
    <?php if(auth('developer')->user()->canManagePortfolio()): ?><a href="<?php echo e(route('developer.portfolio.edit', $item)); ?>" class="text-sm font-bold shrink-0" style="color:var(--brand)">تعديل</a><?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="text-gray-400">أضف مشاريع سابقة لعرض خبرة المطور</div><?php endif; ?>
</div>
<div class="mt-4"><?php echo e($items->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\portfolio\index.blade.php ENDPATH**/ ?>
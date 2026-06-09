
<?php $__env->startSection('page-title', 'لوحة التحكم'); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6"><h1 class="text-2xl font-bold">مرحباً، <?php echo e($developer->name); ?></h1><p class="text-sm text-gray-500">أدر مشاريعك ووحداتك — تظهر مباشرة لفريق المبيعات</p></div>
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    <?php $__currentLoopData = [['المشاريع',$stats['projects']],['معروض',$stats['active_listings']],['الوحدات',$stats['total_units']],['متاح',$stats['available_units']],['سابقة أعمال',$stats['portfolio']]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l,$v]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl border p-4"><div class="text-xs text-gray-500"><?php echo e($l); ?></div><div class="text-2xl font-bold"><?php echo e(number_format($v)); ?></div></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="bg-white rounded-2xl border">
    <div class="px-5 py-3 border-b font-bold flex justify-between"><span>أحدث المشاريع</span>
        <?php if($account->canManageProjects()): ?><a href="<?php echo e(route('developer.projects.create')); ?>" class="text-sm font-bold" style="color:var(--brand)">+ مشروع</a><?php endif; ?>
    </div>
    <div class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $recentProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('developer.projects.show', $p)); ?>" class="block px-5 py-3 hover:bg-gray-50 font-semibold text-sm"><?php echo e($p->name); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="p-6 text-gray-400 text-sm">ابدأ بإضافة أول مشروع</div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\dashboard.blade.php ENDPATH**/ ?>
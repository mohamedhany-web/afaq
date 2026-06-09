

<?php $__env->startSection('page-title', 'الإشعارات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">إشعاراتك</h1>
            <p class="text-gray-600 text-sm">تحديثات التقارير، البلاغات، الاجتماعات، التذاكر والفواتير.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="<?php echo e(route('client.notifications.read-all')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-800 hover:bg-gray-50">تعليم الكل كمقروء</button>
            </form>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">اللوحة</a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-xl border <?php echo e($n->read_at ? 'border-gray-200' : 'border-blue-200 ring-1 ring-blue-100'); ?> p-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="font-bold text-gray-900"><?php echo e($n->title); ?></p>
                    <?php if($n->body): ?>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($n->body); ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-400 mt-2"><?php echo e($n->created_at->format('Y/m/d H:i')); ?></p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <?php if($n->action_url): ?>
                        <a href="<?php echo e($n->action_url); ?>" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">عرض</a>
                    <?php endif; ?>
                    <?php if(! $n->read_at): ?>
                        <form method="POST" action="<?php echo e(route('client.notifications.read', ['clientNotification' => $n])); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">تم القراءة</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-500">لا توجد إشعارات بعد.</div>
        <?php endif; ?>
    </div>

    <div class="mt-6"><?php echo e($notifications->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\notifications.blade.php ENDPATH**/ ?>
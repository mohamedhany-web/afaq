
<?php $__env->startSection('page-title', 'تعديل مهمة'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تعديل المهمة',
    'subtitle' => $task->title,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
    'actionUrl' => route('crm.tasks.show', $task),
    'actionLabel' => 'عرض المهمة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 font-tajawal text-sm text-red-800">
    <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('crm.tasks.update', $task)); ?>" method="POST"
      class="w-full font-tajawal space-y-6"
      x-data="{ priority: <?php echo \Illuminate\Support\Js::from(old('priority', $task->priority))->toHtml() ?> }">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8"><?php echo $__env->make('crm.tasks.partials.form', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
        <aside class="xl:col-span-4">
            <div class="bg-white rounded-2xl border p-5 text-sm font-tajawal space-y-2">
                <p><span class="text-gray-500">الحالة:</span> <strong><?php echo e($task->statusLabel()); ?></strong></p>
                <p><span class="text-gray-500">أُنشئت:</span> <?php echo e($task->created_at->format('Y/m/d H:i')); ?></p>
                <?php if($task->assigner): ?><p><span class="text-gray-500">بواسطة:</span> <?php echo e($task->assigner->name); ?></p><?php endif; ?>
            </div>
        </aside>
    </div>
    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-2 border-t border-gray-200">
        <a href="<?php echo e(route('crm.tasks.show', $task)); ?>" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-sm font-semibold text-center">رجوع</a>
        <button type="submit" class="px-8 py-3 rounded-xl text-white text-sm font-bold shadow-md"
                style="background:linear-gradient(135deg,<?php echo e($themeColor); ?>,<?php echo e($themeColor); ?>dd)">حفظ التعديلات</button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\tasks\edit.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'تعديل — ' . $project->name); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تعديل المشروع',
    'subtitle' => $project->name,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
    'actionUrl' => route('crm.projects.show', $project),
    'actionLabel' => 'عرض المشروع',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
    <ul class="list-disc pr-5 text-sm text-red-700 font-tajawal space-y-1">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('crm.projects.update', $project)); ?>" method="POST" class="w-full space-y-6">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('projects.partials.form', ['project' => $project, 'users' => $users, 'themeColor' => $themeColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-6">
        <a href="<?php echo e(route('crm.projects.show', $project)); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ التعديلات</button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\projects\edit.blade.php ENDPATH**/ ?>
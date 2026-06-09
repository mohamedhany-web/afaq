
<?php $__env->startSection('page-title', 'إضافة مشروع عقاري'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'إضافة مشروع عقاري',
    'subtitle' => 'تسجيل مشروع جديد مع تحديد الموقع على الخريطة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => route('crm.projects.index'),
    'actionLabel' => 'قائمة المشاريع',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
    <ul class="list-disc pr-5 text-sm text-red-700 font-tajawal space-y-1">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('crm.projects.store')); ?>" method="POST" class="w-full space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('projects.partials.form', ['users' => $users, 'themeColor' => $themeColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-6">
        <a href="<?php echo e(route('crm.projects.index')); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ المشروع</button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\projects\create.blade.php ENDPATH**/ ?>
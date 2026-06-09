
<?php $__env->startSection('page-title', 'مطور جديد'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'إضافة مطور عقاري وتعاقد',
    'subtitle' => 'سجّل بيانات المطور والتعاقد وحساب بوابة الدخول في خطوة واحدة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5">
    <p class="font-bold text-red-800 font-tajawal mb-2">يرجى تصحيح الأخطاء التالية:</p>
    <ul class="list-disc pr-5 text-sm text-red-700 space-y-1 font-tajawal">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('admin.developers.store')); ?>" class="w-full space-y-6"><?php echo csrf_field(); ?>
<?php echo $__env->make('admin.developers.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="flex flex-col sm:flex-row gap-3">
    <button type="submit" class="px-6 py-3 rounded-xl text-white text-sm font-semibold font-tajawal shadow-md hover:shadow-lg transition-all"
            style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ وإنشاء البوابة</button>
    <a href="<?php echo e(route('admin.developers.index')); ?>" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 font-tajawal text-center">إلغاء</a>
</div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\developers\create.blade.php ENDPATH**/ ?>
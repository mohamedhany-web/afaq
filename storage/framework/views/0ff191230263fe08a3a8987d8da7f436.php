<?php $__env->startSection('page-title', 'تعديل عميل'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => ($requiresApproval ?? false) ? 'طلب تعديل عميل' : 'تعديل عميل',
    'subtitle' => $client->name,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($requiresApproval ?? false): ?>
<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    سيتم إرسال التعديلات لـ <strong>مدير العمليات</strong> للموافقة قبل تطبيقها على ملف العميل.
</div>
<?php endif; ?>

<form action="<?php echo e(route('crm.clients.update', $client)); ?>" method="POST" class="w-full space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('crm.clients.partials.form', ['client' => $client, 'marketingCampaigns' => $marketingCampaigns ?? collect()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
        <a href="<?php echo e($client->profileUrl()); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة لملف العميل
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            <?php echo e(($requiresApproval ?? false) ? 'إرسال طلب التعديل' : 'حفظ التعديلات'); ?>

        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\edit.blade.php ENDPATH**/ ?>
<?php $__env->startSection('page-title', 'رفع مستند للعميل'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full max-w-3xl">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900">رفع مستند مشترك</h1>
        <a href="<?php echo e(route('client-shared-documents.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900">العودة</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('client-shared-documents.store')); ?>" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <?php echo $__env->make('partials.client-search-select', ['required' => true, 'value' => old('client_id'), 'inputClass' => 'w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm', 'crmScope' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عنوان المستند</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required maxlength="255" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع (اختياري)</label>
                <input type="text" name="document_type" value="<?php echo e(old('document_type', 'general')); ?>" maxlength="64" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm" placeholder="contract, quote, delivery">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات داخلية</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('notes')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الملف</label>
                <input type="file" name="file" required class="block w-full text-sm">
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">رفع</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\client-shared-documents\create.blade.php ENDPATH**/ ?>
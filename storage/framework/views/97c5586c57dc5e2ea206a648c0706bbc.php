<?php $__env->startSection('page-title', 'صفقة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'صفقة جديدة',
    'subtitle' => 'إضافة صفقة إلى مسار المبيعات — القيم بالجنيه المصري',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form action="<?php echo e(route('crm.pipeline.store')); ?>" method="POST" class="w-full space-y-6">
    <?php echo csrf_field(); ?>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            بيانات العميل والمشروع
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <div class="md:col-span-1 xl:col-span-1">
                <?php echo $__env->make('partials.client-search-select', [
                    'required' => true,
                    'value' => old('client_id', request('client_id')),
                    'inputClass' => $input,
                    'crmScope' => true,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="md:col-span-1 xl:col-span-1">
                <label class="<?php echo e($label); ?>">المشروع العقاري</label>
                <select name="project_id" class="<?php echo e($input); ?>">
                    <option value="">— بدون مشروع —</option>
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php if(old('project_id', request('project_id')) == $p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?> <?php if($p->city): ?>(<?php echo e($p->city); ?>)<?php endif; ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-1">
                <label class="<?php echo e($label); ?>">مصدر العميل</label>
                <select name="lead_source" class="<?php echo e($input); ?>">
                    <option value="">—</option>
                    <?php $__currentLoopData = ['website' => 'الموقع', 'referral' => 'إحالة', 'walk_in' => 'زيارة مباشرة', 'social_media' => 'سوشيال', 'advertisement' => 'إعلان', 'call' => 'اتصال', 'other' => 'أخرى']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(old('lead_source') == $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            تفاصيل الصفقة
        </div>
        <div class="p-5 sm:p-6 space-y-4 sm:space-y-6">
            <div>
                <label class="<?php echo e($label); ?>">وصف الصفقة *</label>
                <input name="product_service" value="<?php echo e(old('product_service')); ?>" required class="<?php echo e($input); ?>"
                       placeholder="مثال: شقة 3 غرف — الدور الخامس — برج النخيل">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div>
                    <label class="<?php echo e($label); ?>">القيمة المتوقعة (ج.م) *</label>
                    <input name="estimated_value" type="number" step="0.01" min="0" value="<?php echo e(old('estimated_value')); ?>" required class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">احتمالية الإغلاق % *</label>
                    <input name="probability_percentage" type="number" min="0" max="100" value="<?php echo e(old('probability_percentage', 50)); ?>" required class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">نوع الوحدة</label>
                    <input name="unit_type" value="<?php echo e(old('unit_type')); ?>" class="<?php echo e($input); ?>" placeholder="شقة / فيلا / محل">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">نوع الاهتمام</label>
                    <input name="interest_type" value="<?php echo e(old('interest_type')); ?>" class="<?php echo e($input); ?>" placeholder="شراء / استثمار">
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            المرحلة والمواعيد
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">مرحلة الصفقة</label>
                <select name="stage" class="<?php echo e($input); ?>">
                    <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if(old('stage', 'lead') == $s): echo 'selected'; endif; ?>><?php echo e($stageLabels[$s] ?? $s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">تاريخ الإغلاق المتوقع</label>
                <input name="expected_close_date" type="date" value="<?php echo e(old('expected_close_date')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">موعد المعاينة</label>
                <input name="viewing_date" type="date" value="<?php echo e(old('viewing_date')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="<?php echo e($label); ?>">ملاحظات المعاينة</label>
                <input name="viewing_notes" value="<?php echo e(old('viewing_notes')); ?>" class="<?php echo e($input); ?>" placeholder="تفاصيل موعد المعاينة...">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="<?php echo e($label); ?>">ملاحظات عامة</label>
                <textarea name="notes" rows="4" class="<?php echo e($input); ?> resize-none"><?php echo e(old('notes')); ?></textarea>
            </div>
        </div>
    </div>

    
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
        <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة للمسار
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            حفظ الصفقة
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/pipeline/create.blade.php ENDPATH**/ ?>
<?php
    $rule = $rule ?? null;
    $prefix = $prefix ?? '';
?>
<div>
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">اسم القاعدة</label>
    <input type="text" name="name" value="<?php echo e(old('name', $rule?->name)); ?>" required
           class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm focus:border-gray-300 font-tajawal">
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">القسم</label>
        <select name="department_code" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
            <option value="">كل الأقسام</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($code); ?>" <?php if(old('department_code', $rule?->department_code) === $code): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">نوع المخالفة</label>
        <select name="source_type" required class="rule-source-type w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal" data-prefix="<?php echo e($prefix); ?>">
            <?php $__currentLoopData = $sourceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(old('source_type', $rule?->source_type) === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>
<div class="rule-report-period-wrap <?php echo e(in_array(old('source_type', $rule?->source_type), ['daily_sales_report', 'marketing_report'], true) ? '' : 'hidden'); ?>" data-prefix="<?php echo e($prefix); ?>">
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">دورة التقرير</label>
    <select name="report_period_type" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
        <?php $__currentLoopData = $reportPeriodTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php if(old('report_period_type', $rule?->report_period_type ?? 'daily') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">ينطبق على</label>
        <select name="applies_to" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
            <?php $__currentLoopData = $appliesTo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(old('applies_to', $rule?->applies_to ?? 'employee') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">مبلغ الخصم</label>
        <input type="number" name="amount" step="0.01" min="0" value="<?php echo e(old('amount', $rule?->amount ?? 50)); ?>" required
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
    </div>
</div>
<div>
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">ساعات السماح بعد الموعد</label>
    <input type="number" name="grace_hours" min="0" max="720" value="<?php echo e(old('grace_hours', $rule?->grace_hours ?? 2)); ?>" required
           class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal">
</div>
<div>
    <label class="block text-xs font-bold text-gray-600 mb-1.5 font-tajawal">وصف (اختياري)</label>
    <textarea name="description" rows="2" class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-tajawal"><?php echo e(old('description', $rule?->description)); ?></textarea>
</div>
<label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 font-tajawal">
    <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $rule?->is_active ?? true)): echo 'checked'; endif; ?> class="rounded border-gray-300">
    تفعيل القاعدة
</label>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\auto-penalties\partials\rule-form-fields.blade.php ENDPATH**/ ?>
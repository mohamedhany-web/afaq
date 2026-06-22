<?php
    $c = $contract ?? null;
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">بيانات الوكيل</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">حساب الوكيل (مندوب مبيعات) *</label>
                <select name="user_id" required class="<?php echo e($input); ?>" <?php if($c): ?> disabled <?php endif; ?>>
                    <option value="">— اختر —</option>
                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($a->id); ?>" <?php if(old('user_id', $c->user_id ?? '')==$a->id): echo 'selected'; endif; ?>><?php echo e($a->name); ?> (<?php echo e($a->email); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($c): ?><input type="hidden" name="user_id" value="<?php echo e($c->user_id); ?>"><?php endif; ?>
            </div>
            <div><label class="<?php echo e($label); ?>">رقم العقد</label><input name="contract_number" value="<?php echo e(old('contract_number', $c->contract_number ?? '')); ?>" class="<?php echo e($input); ?>"></div>
            <div><label class="<?php echo e($label); ?>">الرقم القومي</label><input name="national_id" value="<?php echo e(old('national_id', $c->national_id ?? '')); ?>" class="<?php echo e($input); ?>" dir="ltr"></div>
            <div><label class="<?php echo e($label); ?>">الجنسية</label><input name="nationality" value="<?php echo e(old('nationality', $c->nationality ?? 'مصري')); ?>" class="<?php echo e($input); ?>"></div>
            <div><label class="<?php echo e($label); ?>">الهاتف</label><input name="phone" value="<?php echo e(old('phone', $c->phone ?? '')); ?>" class="<?php echo e($input); ?>" dir="ltr"></div>
            <div class="sm:col-span-2"><label class="<?php echo e($label); ?>">العنوان</label><input name="address" value="<?php echo e(old('address', $c->address ?? '')); ?>" class="<?php echo e($input); ?>"></div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">مدة العقد والتارجت</div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="<?php echo e($label); ?>">تاريخ البداية *</label><input type="date" name="start_date" required value="<?php echo e(old('start_date', optional($c?->start_date)->format('Y-m-d') ?? now()->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">تاريخ النهاية</label><input type="date" name="end_date" value="<?php echo e(old('end_date', optional($c?->end_date)->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">تارجت ربع سنوي (قيمة مبيعات)</label><input type="number" step="0.01" min="0" name="quarterly_target_amount" value="<?php echo e(old('quarterly_target_amount', $c->quarterly_target_amount ?? '')); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">تارجت ربع سنوي (عدد صفقات)</label><input type="number" min="1" name="quarterly_target_deals" value="<?php echo e(old('quarterly_target_deals', $c->quarterly_target_deals ?? '')); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">حالة العقد *</label>
                    <select name="status" class="<?php echo e($input); ?>"><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(old('status',$c->status??'active')===$k): echo 'selected'; endif; ?>><?php echo e($t); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                </div>
                <div><label class="<?php echo e($label); ?>">تاريخ التوقيع</label><input type="date" name="signed_at" value="<?php echo e(old('signed_at', optional($c?->signed_at)->format('Y-m-d') ?? now()->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>"></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">توقيع الشركة</div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="<?php echo e($label); ?>">اسم الموقّع عن الشركة</label><input name="company_signatory_name" value="<?php echo e(old('company_signatory_name', $c->company_signatory_name ?? '')); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">الصفة</label><input name="company_signatory_title" value="<?php echo e(old('company_signatory_title', $c->company_signatory_title ?? 'المدير التنفيذي')); ?>" class="<?php echo e($input); ?>"></div>
                <div class="sm:col-span-2"><label class="<?php echo e($label); ?>">ملاحظات</label><textarea name="notes" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('notes', $c->notes ?? '')); ?></textarea></div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\freelance-agents\partials\form.blade.php ENDPATH**/ ?>
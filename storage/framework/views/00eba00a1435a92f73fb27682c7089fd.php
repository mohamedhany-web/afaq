<?php
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mt-6">
    <div class="px-5 sm:px-6 py-4 border-b font-bold font-tajawal" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?>08 0%,<?php echo e($themeColor); ?>03 100%);">
        بيانات العمولة (هيكل الوكيل المستقل)
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="<?php echo e($label); ?>">نوع العملية العقارية</label>
            <select name="transaction_type" class="<?php echo e($input); ?>">
                <option value="">— تلقائي —</option>
                <?php $__currentLoopData = $transactionTypes ?? config('freelance_agents.transaction_types', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($k); ?>" <?php if(old('transaction_type', $sale->transaction_type ?? '')===$k): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">إجمالي عمولة الشركة (جنيه)</label>
            <input type="number" step="0.01" min="0" name="company_commission_amount" value="<?php echo e(old('company_commission_amount', $sale->company_commission_amount ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="المبلغ المحصّل من المطور/البائع">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">وكيل الجلب (Listing)</label>
            <select name="listing_agent_id" class="<?php echo e($input); ?>">
                <option value="">— لا يوجد —</option>
                <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($a->id); ?>" <?php if(old('listing_agent_id', $sale->listing_agent_id ?? '')==$a->id): echo 'selected'; endif; ?>><?php echo e($a->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">تاريخ الإغلاق الفعلي</label>
            <input type="date" name="actual_close_date" value="<?php echo e(old('actual_close_date', optional($sale->actual_close_date)->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
        </div>
        <div>
            <label class="<?php echo e($label); ?>">القيمة الفعلية للصفقة</label>
            <input type="number" step="0.01" min="0" name="actual_value" value="<?php echo e(old('actual_value', $sale->actual_value ?? '')); ?>" class="<?php echo e($input); ?>">
        </div>
        <div class="flex items-center gap-2 pt-6">
            <input type="checkbox" name="commission_collected" value="1" id="commission_collected" <?php if(old('commission_collected', $sale->commission_collected ?? false)): echo 'checked'; endif; ?> class="w-4 h-4 rounded" style="accent-color:<?php echo e($themeColor); ?>;">
            <label for="commission_collected" class="text-sm font-semibold font-tajawal">تم تحصيل عمولة الشركة (جاهزة للصرف)</label>
        </div>
        <div class="sm:col-span-2">
            <label class="<?php echo e($label); ?>">ملاحظات العمولة</label>
            <textarea name="commission_notes" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('commission_notes', $sale->commission_notes ?? '')); ?></textarea>
        </div>
    </div>
    <p class="px-5 pb-4 text-[11px] text-gray-400 font-tajawal">النسب تُحسب من عمولة الشركة وليس قيمة الصفقة — <a href="<?php echo e(route('crm.freelance-agents.scheme')); ?>" class="underline" style="color:<?php echo e($themeColor); ?>">جدول الهيكل</a></p>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\partials\commission-fields.blade.php ENDPATH**/ ?>
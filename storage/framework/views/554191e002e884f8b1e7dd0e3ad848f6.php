<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        إنشاء تقرير يومي جديد
    </div>
    <form action="<?php echo e(route('crm.daily-reports.generate')); ?>" method="POST" class="p-5 sm:p-6 flex flex-col sm:flex-row gap-4 items-end">
        <?php echo csrf_field(); ?>
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">تاريخ التقرير</label>
            <input type="date" name="report_date" value="<?php echo e(old('report_date', today()->toDateString())); ?>" max="<?php echo e(today()->toDateString()); ?>" required
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0"
                   style="focus-ring-color: <?php echo e($themeColor); ?>;">
            <?php $__errorArgs = ['report_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="w-full sm:w-auto px-8 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal hover:shadow-md transition-all"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            إنشاء التقرير
        </button>
    </form>
    <p class="px-5 sm:px-6 pb-5 text-xs text-gray-500 font-tajawal">يُملأ القسم 1–6 تلقائياً من النظام. بعد المراجعة ارفع التقرير من صفحة التفاصيل.</p>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\daily-reports\partials\create-form.blade.php ENDPATH**/ ?>
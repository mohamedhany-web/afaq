

<?php
    $currencySymbol = \App\Helpers\SettingsHelper::getCurrencySymbol();
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal flex items-center justify-between';
    $inputClass = 'w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent font-tajawal';
    $categories = [
        'office_supplies' => 'مستلزمات مكتبية',
        'utilities' => 'مرافق (كهرباء، ماء، إنترنت)',
        'rent' => 'إيجار',
        'salaries' => 'رواتب',
        'marketing' => 'تسويق',
        'travel' => 'سفر',
        'maintenance' => 'صيانة',
        'software' => 'برمجيات',
        'professional_fees' => 'رسوم مهنية',
        'insurance' => 'تأمين',
        'taxes' => 'ضرائب',
        'other' => 'أخرى',
    ];
?>

<?php $__env->startSection('page-title', 'مصروف جديد'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('accounting.partials.context', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'مصروف جديد',
    'subtitle' => 'تسجيل مصروف تشغيلي جديد وإرساله للموافقة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => route('expenses.index'),
    'actionLabel' => 'العودة للمصروفات',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('accounting.partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form action="<?php echo e(route('expenses.store')); ?>" method="POST" class="font-tajawal space-y-6">
    <?php echo csrf_field(); ?>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($headerStyle); ?>">
            <span>معلومات المصروف</span>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="md:col-span-2 lg:col-span-3">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">الوصف <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="3" required
                          class="<?php echo e($inputClass); ?> resize-none <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          placeholder="وصف المصروف..."><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="expense_category" class="block text-sm font-medium text-gray-700 mb-1.5">الفئة <span class="text-red-500">*</span></label>
                <select name="expense_category" id="expense_category" required
                        class="<?php echo e($inputClass); ?> <?php $__errorArgs = ['expense_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">اختر فئة المصروف</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if(old('expense_category') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['expense_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1.5">المورد</label>
                <select name="vendor_id" id="vendor_id" class="<?php echo e($inputClass); ?>">
                    <option value="">اختياري — بدون مورد</option>
                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($vendor->id); ?>" <?php if(old('vendor_id') == $vendor->id): echo 'selected'; endif; ?>><?php echo e($vendor->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">المبلغ <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="number" name="amount" id="amount" value="<?php echo e(old('amount')); ?>" step="0.01" min="0" required
                           class="<?php echo e($inputClass); ?> pl-3 pr-14 tabular-nums <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="0.00">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400"><?php echo e($currencySymbol); ?></span>
                </div>
                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1.5">تاريخ المصروف <span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" id="expense_date" value="<?php echo e(old('expense_date', date('Y-m-d'))); ?>" required
                       class="<?php echo e($inputClass); ?> <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1.5">طريقة الدفع <span class="text-red-500">*</span></label>
                <select name="payment_method" id="payment_method" required
                        class="<?php echo e($inputClass); ?> <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="cash" <?php if(old('payment_method', 'cash') === 'cash'): echo 'selected'; endif; ?>>نقدي</option>
                    <option value="bank_transfer" <?php if(old('payment_method') === 'bank_transfer'): echo 'selected'; endif; ?>>تحويل بنكي</option>
                    <option value="check" <?php if(old('payment_method') === 'check'): echo 'selected'; endif; ?>>شيك</option>
                    <option value="credit_card" <?php if(old('payment_method') === 'credit_card'): echo 'selected'; endif; ?>>بطاقة ائتمان</option>
                </select>
                <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border px-5 py-4 text-sm font-tajawal flex items-start gap-3"
         style="background: <?php echo e($themeColor); ?>08; border-color: <?php echo e($themeColor); ?>25; color: #374151;">
        <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: <?php echo e($themeColor); ?>;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>يُحفظ المصروف بحالة <strong>معلق</strong> حتى تتم الموافقة عليه من الإدارة المالية.</p>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 pb-2">
        <a href="<?php echo e(route('expenses.index')); ?>"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            إضافة المصروف
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\expenses\create.blade.php ENDPATH**/ ?>
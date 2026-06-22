

<?php $__env->startSection('page-title', 'تعديل المصروف'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold mb-2">تعديل المصروف</h1>
            <p class="text-green-100">تعديل بيانات المصروف: <?php echo e($expense->expense_number); ?></p>
        </div>
        <a href="<?php echo e(route('expenses.index')); ?>" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center">
            <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="<?php echo e(route('expenses.update', $expense)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات المصروف</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" id="description" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description', $expense->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="expense_category" class="block text-sm font-medium text-gray-700 mb-2">الفئة <span class="text-red-500">*</span></label>
                        <select name="expense_category" id="expense_category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 <?php $__errorArgs = ['expense_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">اختر فئة المصروف</option>
                            <option value="office_supplies" <?php echo e(old('expense_category', $expense->expense_category) == 'office_supplies' ? 'selected' : ''); ?>>مستلزمات مكتبية</option>
                            <option value="utilities" <?php echo e(old('expense_category', $expense->expense_category) == 'utilities' ? 'selected' : ''); ?>>مرافق (كهرباء، ماء، إنترنت)</option>
                            <option value="rent" <?php echo e(old('expense_category', $expense->expense_category) == 'rent' ? 'selected' : ''); ?>>إيجار</option>
                            <option value="salaries" <?php echo e(old('expense_category', $expense->expense_category) == 'salaries' ? 'selected' : ''); ?>>رواتب</option>
                            <option value="marketing" <?php echo e(old('expense_category', $expense->expense_category) == 'marketing' ? 'selected' : ''); ?>>تسويق</option>
                            <option value="travel" <?php echo e(old('expense_category', $expense->expense_category) == 'travel' ? 'selected' : ''); ?>>سفر</option>
                            <option value="maintenance" <?php echo e(old('expense_category', $expense->expense_category) == 'maintenance' ? 'selected' : ''); ?>>صيانة</option>
                            <option value="software" <?php echo e(old('expense_category', $expense->expense_category) == 'software' ? 'selected' : ''); ?>>برمجيات</option>
                            <option value="professional_fees" <?php echo e(old('expense_category', $expense->expense_category) == 'professional_fees' ? 'selected' : ''); ?>>رسوم مهنية</option>
                            <option value="insurance" <?php echo e(old('expense_category', $expense->expense_category) == 'insurance' ? 'selected' : ''); ?>>تأمين</option>
                            <option value="taxes" <?php echo e(old('expense_category', $expense->expense_category) == 'taxes' ? 'selected' : ''); ?>>ضرائب</option>
                            <option value="other" <?php echo e(old('expense_category', $expense->expense_category) == 'other' ? 'selected' : ''); ?>>أخرى</option>
                        </select>
                        <?php $__errorArgs = ['expense_category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-2">المورد</label>
                        <select name="vendor_id" id="vendor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">اختر المورد</option>
                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendor->id); ?>" <?php echo e(old('vendor_id', $expense->vendor_id) == $vendor->id ? 'selected' : ''); ?>><?php echo e($vendor->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                        <input type="number" name="amount" id="amount" value="<?php echo e(old('amount', $expense->amount)); ?>" step="0.01" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ المصروف</label>
                        <input type="date" name="expense_date" id="expense_date" value="<?php echo e(old('expense_date', $expense->expense_date->format('Y-m-d'))); ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 <?php $__errorArgs = ['expense_date'];
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
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="cash" <?php echo e(old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : ''); ?>>نقدي</option>
                            <option value="bank_transfer" <?php echo e(old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                            <option value="check" <?php echo e(old('payment_method', $expense->payment_method) == 'check' ? 'selected' : ''); ?>>شيك</option>
                            <option value="credit_card" <?php echo e(old('payment_method', $expense->payment_method) == 'credit_card' ? 'selected' : ''); ?>>بطاقة ائتمان</option>
                        </select>
                        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="pending" <?php echo e(old('status', $expense->status) == 'pending' ? 'selected' : ''); ?>>معلق</option>
                            <option value="approved" <?php echo e(old('status', $expense->status) == 'approved' ? 'selected' : ''); ?>>موافق عليه</option>
                            <option value="paid" <?php echo e(old('status', $expense->status) == 'paid' ? 'selected' : ''); ?>>مدفوع</option>
                            <option value="rejected" <?php echo e(old('status', $expense->status) == 'rejected' ? 'selected' : ''); ?>>مرفوض</option>
                        </select>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                <a href="<?php echo e(route('expenses.index')); ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\expenses\edit.blade.php ENDPATH**/ ?>
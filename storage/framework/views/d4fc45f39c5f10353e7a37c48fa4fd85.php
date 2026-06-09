<?php
    $isEdit = isset($client);
    $clientTypeValue = old('client_type', $isEdit
        ? (($client->client_type === 'small_business') ? 'company' : 'individual')
        : 'individual');
?>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        البيانات الأساسية
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="sm:col-span-2 lg:col-span-2">
            <label class="<?php echo e($label); ?>">الاسم الكامل *</label>
            <input name="name" value="<?php echo e(old('name', $client->name ?? '')); ?>" required class="<?php echo e($input); ?>" placeholder="اسم العميل">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">رقم الهاتف *</label>
            <input name="phone" value="<?php echo e(old('phone', $client->phone ?? '')); ?>" required class="<?php echo e($input); ?>" placeholder="01xxxxxxxxx" dir="ltr">
            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">البريد الإلكتروني</label>
            <input name="email" type="email" value="<?php echo e(old('email', $client->email ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="email@example.com" dir="ltr">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">نوع العميل</label>
            <select name="client_type" class="<?php echo e($input); ?>">
                <option value="individual" <?php if($clientTypeValue === 'individual'): echo 'selected'; endif; ?>>فرد</option>
                <option value="company" <?php if($clientTypeValue === 'company'): echo 'selected'; endif; ?>>شركة / منشأة</option>
            </select>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">الحالة *</label>
            <select name="status" class="<?php echo e($input); ?>">
                <?php $__currentLoopData = ['prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(old('status', $client->status ?? 'prospect') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        بيانات التواصل والشركة
    </div>
    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div>
            <label class="<?php echo e($label); ?>">اسم الشركة / المنشأة</label>
            <input name="company" value="<?php echo e(old('company', $client->company_name ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="اختياري — للعملاء من نوع شركة">
            <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">العنوان</label>
            <input name="address" value="<?php echo e(old('address', $client->address ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="المدينة، الحي، الشارع...">
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        ملاحظات
    </div>
    <div class="p-5 sm:p-6">
        <label class="<?php echo e($label); ?>">ملاحظات إضافية</label>
        <textarea name="notes" rows="4" class="<?php echo e($input); ?> resize-none" placeholder="اهتمامات العميل، متطلبات الوحدة، مصدر التواصل..."><?php echo e(old('notes', $client->notes ?? '')); ?></textarea>
        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\form.blade.php ENDPATH**/ ?>
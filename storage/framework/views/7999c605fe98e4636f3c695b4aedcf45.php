<?php
    $client = $client ?? new \App\Models\Client();
    $isEdit = $client->exists;
    $clientTypeValue = old('client_type', $isEdit
        ? \App\Models\Client::normalizeType($client->client_type)
        : 'individual');
    $leadSourceValue = old('lead_source', $isEdit ? $client->lead_source : '');
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
            <input name="phone" id="client_phone" value="<?php echo e(old('phone', $client->phone ?? '')); ?>" required class="<?php echo e($input); ?>" placeholder="01xxxxxxxxx" dir="ltr">
            <?php echo $__env->make('crm.clients.partials.phone-duplicate-check', ['ignoreId' => $isEdit ? $client->id : null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
            <label class="<?php echo e($label); ?>">تصنيف العميل</label>
            <select name="client_type" id="client_type" class="<?php echo e($input); ?>">
                <?php $__currentLoopData = \App\Models\Client::typeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $labelText): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if($clientTypeValue === $value): echo 'selected'; endif; ?>><?php echo e($labelText); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['client_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div id="id_number_wrap">
            <label class="<?php echo e($label); ?>">رقم البطاقة <span id="id_number_required_mark" class="text-red-600 hidden">*</span></label>
            <input name="id_number" id="id_number" value="<?php echo e(old('id_number', $client->id_number ?? '')); ?>" class="<?php echo e($input); ?>" placeholder="إلزامي لعملاء فري لانس" dir="ltr" maxlength="50">
            <?php $__errorArgs = ['id_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($label); ?>">مصدر العميل</label>
            <select name="lead_source" id="lead_source" class="<?php echo e($input); ?>">
                <option value="">— غير محدد —</option>
                <?php $__currentLoopData = \App\Models\Client::leadSourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $labelText): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if($leadSourceValue === $value): echo 'selected'; endif; ?>><?php echo e($labelText); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['lead_source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php echo $__env->make('crm.clients.partials.source-details-fields', compact('client', 'input', 'label', 'marketingCampaigns'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
        وصف العميل
    </div>
    <div class="p-5 sm:p-6">
        <label class="<?php echo e($label); ?>">وصف مختصر عن العميل</label>
        <textarea name="description" rows="3" class="<?php echo e($input); ?> resize-none" placeholder="اهتماماته، نوع الوحدة المطلوبة، ملخص عن احتياجاته..."><?php echo e(old('description', $client->description ?? '')); ?></textarea>
        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const typeSelect = document.getElementById('client_type');
    const idInput = document.getElementById('id_number');
    const mark = document.getElementById('id_number_required_mark');
    if (!typeSelect || !idInput) return;

    function syncIdRequirement() {
        const freelance = typeSelect.value === 'freelance';
        idInput.required = freelance;
        mark?.classList.toggle('hidden', !freelance);
    }

    typeSelect.addEventListener('change', syncIdRequirement);
    syncIdRequirement();
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/form.blade.php ENDPATH**/ ?>
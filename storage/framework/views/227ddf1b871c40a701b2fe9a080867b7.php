<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">الموظف</label>
    <select name="employee_id" required class="w-full border rounded-xl px-3 py-2 text-sm">
        <option value="">اختر الموظف</option>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">عنوان العقد</label>
    <input type="text" name="title" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="مثال: عقد عمل — قسم المبيعات">
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">نوع العقد</label>
    <select name="contract_type" required class="w-full border rounded-xl px-3 py-2 text-sm">
        <?php $__currentLoopData = $contractTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ البداية</label>
        <input type="date" name="start_date" required value="<?php echo e(now()->toDateString()); ?>" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">تاريخ النهاية</label>
        <input type="date" name="end_date" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>
</div>
<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">الراتب</label>
        <input type="number" step="0.01" name="salary" class="w-full border rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">الحالة</label>
        <select name="status" class="w-full border rounded-xl px-3 py-2 text-sm">
            <?php $__currentLoopData = config('hr_contracts.status_labels', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($key); ?>" <?php if($key === 'draft'): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">الشروط</label>
    <textarea name="terms" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">ملاحظات</label>
    <textarea name="notes" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea>
</div>
<div>
    <label class="block text-sm font-bold text-gray-700 mb-1">ملف العقد (PDF / صورة)</label>
    <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm">
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\contracts\partials\form-fields.blade.php ENDPATH**/ ?>
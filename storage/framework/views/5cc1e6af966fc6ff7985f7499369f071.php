<?php $__env->startSection('page-title', 'تعديل مستخدم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تعديل: ' . $user->name,
    'subtitle' => $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
    'actionUrl' => route('users.show', $user),
    'actionLabel' => 'عرض الملف',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form method="POST" action="<?php echo e(route('users.update', $user)); ?>" class="space-y-6 font-tajawal">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold mb-4">بيانات الحساب</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">الاسم *</label>
                <input name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">البريد *</label>
                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="<?php echo e($input); ?>" dir="ltr">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">كلمة مرور جديدة (اختياري)</label>
                <input type="password" name="password" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="<?php echo e($input); ?>">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold mb-1">الدور</h2>
        <p class="text-sm text-gray-500 mb-4">تغيير الدور يُحدّث الصلاحيات ومساحة العمل تلقائياً</p>
        <?php echo $__env->make('users.partials.role-picker', [
            'assignableRoles' => $assignableRoles,
            'workspaceGroups' => $workspaceGroups,
            'roleHints' => $roleHints,
            'selected' => old('role', $currentRole),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php if($user->employee): ?>
    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold mb-4">بيانات الموظف المرتبط</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="<?php echo e($label); ?>">الاسم الأول</label><input name="first_name" value="<?php echo e(old('first_name', $user->employee->first_name)); ?>" class="<?php echo e($input); ?>" required></div>
            <div><label class="<?php echo e($label); ?>">اسم العائلة</label><input name="last_name" value="<?php echo e(old('last_name', $user->employee->last_name)); ?>" class="<?php echo e($input); ?>" required></div>
            <div><label class="<?php echo e($label); ?>">الهاتف</label><input name="phone" value="<?php echo e(old('phone', $user->employee->phone)); ?>" class="<?php echo e($input); ?>" required></div>
            <div>
                <label class="<?php echo e($label); ?>">القسم</label>
                <select name="department_id" class="<?php echo e($input); ?>" required>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dept->id); ?>" <?php if(old('department_id', $user->employee->department_id) == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div><label class="<?php echo e($label); ?>">المنصب</label><input name="position" value="<?php echo e(old('position', $user->employee->position)); ?>" class="<?php echo e($input); ?>" required></div>
            <div><label class="<?php echo e($label); ?>">الراتب</label><input type="number" name="salary" value="<?php echo e(old('salary', $user->employee->salary)); ?>" class="<?php echo e($input); ?>" required></div>
            <div>
                <label class="<?php echo e($label); ?>">نوع التوظيف</label>
                <select name="employment_type" class="<?php echo e($input); ?>" required>
                    <?php $__currentLoopData = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(old('employment_type', $user->employee->employment_type) === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الحالة</label>
                <select name="status" class="<?php echo e($input); ?>" required>
                    <?php $__currentLoopData = ['active'=>'نشط','inactive'=>'غير نشط','terminated'=>'منتهي']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(old('status', $user->employee->status) === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-900">
        هذا المستخدم ليس لديه سجل موظف. يمكنك إنشاء موظف من قسم <a href="<?php echo e(route('employees.create')); ?>" class="font-bold underline">الموظفين</a> وربطه بالمستخدم.
    </div>
    <?php endif; ?>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold" style="background:<?php echo e($themeColor); ?>">حفظ التعديلات</button>
        <a href="<?php echo e(route('users.index')); ?>" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600">إلغاء</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\users\edit.blade.php ENDPATH**/ ?>
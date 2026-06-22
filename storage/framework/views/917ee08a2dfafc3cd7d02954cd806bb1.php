<?php $__env->startSection('page-title', 'مستخدم جديد'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-tajawal focus:ring-2 focus:border-transparent';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'إضافة مستخدم',
    'subtitle' => 'إنشاء حساب دخول واختيار الدور المناسب للنظام',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>',
    'actionUrl' => route('users.index'),
    'actionLabel' => 'العودة للقائمة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($error); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('users.store')); ?>" class="space-y-6 font-tajawal" x-data="{ withEmployee: <?php echo e(old('create_employee') ? 'true' : 'false'); ?> }">
    <?php echo csrf_field(); ?>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold text-gray-900 mb-4">بيانات الحساب</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">الاسم الكامل *</label>
                <input name="name" value="<?php echo e(old('name')); ?>" required class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">البريد الإلكتروني *</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="<?php echo e($input); ?>" dir="ltr">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">كلمة المرور *</label>
                <input type="password" name="password" required class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">تأكيد كلمة المرور *</label>
                <input type="password" name="password_confirmation" required class="<?php echo e($input); ?>">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <h2 class="font-bold text-gray-900 mb-1">الدور في النظام *</h2>
        <p class="text-sm text-gray-500 mb-4">اختر القسم ثم الدور — يُحدَّد تلقائياً مساحة العمل والصلاحيات والقسم التوظيفي</p>
        <?php echo $__env->make('users.partials.role-picker', [
            'assignableRoles' => $assignableRoles,
            'workspaceGroups' => $workspaceGroups,
            'roleHints' => $roleHints,
            'selected' => old('role'),
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="bg-white rounded-2xl border p-5 sm:p-6">
        <label class="flex items-center gap-3 cursor-pointer mb-4">
            <input type="checkbox" name="create_employee" value="1" class="rounded border-gray-300" x-model="withEmployee" <?php if(old('create_employee')): echo 'checked'; endif; ?>>
            <span class="font-bold text-gray-900">إنشاء سجل موظف مرتبط (حضور، إجازات، راتب)</span>
        </label>
        <div x-show="withEmployee" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="<?php echo e($label); ?>">الاسم الأول *</label>
                <input name="first_name" value="<?php echo e(old('first_name')); ?>" class="<?php echo e($input); ?>" :required="withEmployee">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">اسم العائلة *</label>
                <input name="last_name" value="<?php echo e(old('last_name')); ?>" class="<?php echo e($input); ?>" :required="withEmployee">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الهاتف *</label>
                <input name="phone" value="<?php echo e(old('phone')); ?>" class="<?php echo e($input); ?>" dir="ltr" :required="withEmployee">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">القسم</label>
                <select name="department_id" class="<?php echo e($input); ?>">
                    <option value="">تلقائي حسب الدور</option>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dept->id); ?>" <?php if(old('department_id') == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">المنصب</label>
                <input name="position" value="<?php echo e(old('position')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الراتب</label>
                <input type="number" name="salary" value="<?php echo e(old('salary', 0)); ?>" min="0" step="0.01" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">تاريخ التعيين</label>
                <input type="date" name="hire_date" value="<?php echo e(old('hire_date', now()->toDateString())); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">نوع التوظيف</label>
                <select name="employment_type" class="<?php echo e($input); ?>">
                    <?php $__currentLoopData = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(old('employment_type', 'full_time') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold" style="background:<?php echo e($themeColor); ?>">حفظ المستخدم</button>
        <a href="<?php echo e(route('users.index')); ?>" class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold">إلغاء</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\users\create.blade.php ENDPATH**/ ?>
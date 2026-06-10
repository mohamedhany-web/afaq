<?php $__env->startSection('page-title', ($marketingOnly ?? false) ? 'تعديل موظف تسويق' : 'تعديل موظف مبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $fullName = trim($employee->first_name . ' ' . $employee->last_name);
    $isSuperAdminUser = $employee->user?->hasRole('super_admin') ?? false;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => ($marketingOnly ?? false) ? 'تعديل موظف تسويق' : 'تعديل موظف مبيعات',
    'subtitle' => $fullName . ' — ' . ($employee->employee_id ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5">
    <p class="font-bold text-red-800 font-tajawal mb-2">يرجى تصحيح الأخطاء التالية:</p>
    <ul class="list-disc pr-5 text-sm text-red-700 space-y-1 font-tajawal">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('employees.update', $employee)); ?>" method="POST" class="w-full space-y-6">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <?php if($salesOnly ?? false): ?><input type="hidden" name="sales_only" value="1"><?php endif; ?>
    <?php if($marketingOnly ?? false): ?><input type="hidden" name="marketing_only" value="1"><?php endif; ?>

    <?php if($employee->user): ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">حساب النظام</div>
        <div class="p-5 sm:p-6 text-sm font-tajawal text-gray-600">
            <span class="font-semibold text-gray-900"><?php echo e($employee->user->name); ?></span>
            <span class="mx-2">·</span>
            <span dir="ltr"><?php echo e($employee->user->email); ?></span>
            <?php if($isSuperAdminUser): ?>
            <p class="text-xs text-amber-600 mt-2">مستخدم super admin — لا يُغيّر دوره من هنا.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">البيانات الشخصية</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">رقم الموظف *</label>
                <input name="employee_id" value="<?php echo e(old('employee_id', $employee->employee_id)); ?>" required class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الاسم الأول *</label>
                <input name="first_name" value="<?php echo e(old('first_name', $employee->first_name)); ?>" required class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">اسم العائلة *</label>
                <input name="last_name" value="<?php echo e(old('last_name', $employee->last_name)); ?>" required class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الهاتف *</label>
                <input name="phone" value="<?php echo e(old('phone', $employee->phone)); ?>" required class="<?php echo e($input); ?>" dir="ltr">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">البريد الإلكتروني *</label>
                <input type="email" name="email" value="<?php echo e(old('email', $employee->email)); ?>" required class="<?php echo e($input); ?>" dir="ltr">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="<?php echo e($label); ?>">العنوان</label>
                <input name="address" value="<?php echo e(old('address', $employee->address)); ?>" class="<?php echo e($input); ?>">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">الدور والوظيفة — <?php echo e(($marketingOnly ?? false) ? 'قسم التسويق' : 'قسم المبيعات'); ?></div>
        <div class="p-5 sm:p-6 space-y-5">
            <?php if (! ($isSuperAdminUser)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $labelText): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="relative cursor-pointer block">
                    <input type="radio" name="crm_role" value="<?php echo e($val); ?>" class="peer sr-only"
                           <?php if(old('crm_role', $currentRole) === $val): echo 'checked'; endif; ?>>
                    <div class="role-card p-4 rounded-xl border-2 border-gray-200 transition-all text-center font-tajawal">
                        <div class="font-bold text-gray-900"><?php echo e($labelText); ?></div>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php if($marketingOnly ?? false): ?>
                                <?php if($val === 'marketing_manager'): ?> إدارة الحملات والفريق <?php else: ?> تنفيذ المهام وجمع Leads <?php endif; ?>
                            <?php elseif($val === 'manager'): ?> لوحة الفريق + إدارة فرق المبيعات
                            <?php else: ?> CRM — العملاء ومسار المبيعات <?php endif; ?>
                        </div>
                    </div>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['crm_role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label class="<?php echo e($label); ?>">القسم</label>
                    <input type="hidden" name="department_id" value="<?php echo e($salesDepartment->id); ?>">
                    <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm font-semibold font-tajawal"><?php echo e($salesDepartment->name); ?></div>
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">المنصب</label>
                    <input name="position" value="<?php echo e(old('position', $employee->position)); ?>" class="<?php echo e($input); ?>" placeholder="يُملأ تلقائياً حسب الدور">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">حالة الموظف *</label>
                    <select name="status" required class="<?php echo e($input); ?>">
                        <option value="active" <?php if(old('status', $employee->status) === 'active'): echo 'selected'; endif; ?>>نشط</option>
                        <option value="inactive" <?php if(old('status', $employee->status) === 'inactive'): echo 'selected'; endif; ?>>غير نشط</option>
                        <option value="terminated" <?php if(old('status', $employee->status) === 'terminated'): echo 'selected'; endif; ?>>منتهي الخدمة</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">بيانات التوظيف</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">الراتب (ج.م) *</label>
                <input type="number" name="salary" value="<?php echo e(old('salary', $employee->salary)); ?>" required min="0" step="0.01" class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">تاريخ التوظيف</label>
                <input type="date" name="hire_date" value="<?php echo e(old('hire_date', $employee->hire_date?->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">نوع التوظيف</label>
                <select name="employment_type" class="<?php echo e($input); ?>">
                    <?php $__currentLoopData = ['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'intern' => 'متدرب']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(old('employment_type', $employee->employment_type ?? 'full_time') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    <?php echo $__env->make('employees.partials.work-schedule-fields', ['employee' => $employee], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">جهة اتصال الطوارئ</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">الاسم</label>
                <input name="emergency_contact" value="<?php echo e(old('emergency_contact', $employee->emergency_contact)); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الهاتف</label>
                <input name="emergency_phone" value="<?php echo e(old('emergency_phone', $employee->emergency_phone)); ?>" class="<?php echo e($input); ?>" dir="ltr">
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-6">
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employees.show', array_merge(['employee' => $employee], array_filter(['sales_only' => ($salesOnly ?? false) ? 1 : null, 'marketing_only' => ($marketingOnly ?? false) ? 1 : null])))); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">إلغاء</a>
            <?php if($canDelete ?? false): ?>
            <button type="button" onclick="if(confirm('حذف هذا الموظف؟')) document.getElementById('delete-employee-form').submit();"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-red-200 text-red-600 font-semibold text-sm hover:bg-red-50 font-tajawal">حذف الموظف</button>
            <?php endif; ?>
        </div>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md font-tajawal"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">حفظ التعديلات</button>
    </div>
</form>

<?php if($canDelete ?? false): ?>
<form id="delete-employee-form" action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" class="hidden">
    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
    <?php if($salesOnly ?? false): ?><input type="hidden" name="sales_only" value="1"><?php endif; ?>
</form>
<?php endif; ?>

<style>
    input[name="crm_role"]:checked + .role-card {
        border-color: <?php echo e($themeColor); ?>;
        background: <?php echo e($themeColor); ?>14;
        box-shadow: 0 4px 14px <?php echo e($themeColor); ?>25;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/employees/edit.blade.php ENDPATH**/ ?>
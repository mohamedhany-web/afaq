<?php $__env->startSection('page-title', ($marketingOnly ?? false) ? 'إضافة موظف تسويق' : 'إضافة موظف مبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => ($marketingOnly ?? false) ? 'إضافة موظف تسويق' : 'إضافة موظف مبيعات',
    'subtitle' => ($marketingOnly ?? false) ? 'قسم التسويق — مدير تسويق أو موظف تسويق' : 'قسم المبيعات العقارية — اختر الدور: مدير مبيعات أو موظف مبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
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

<form action="<?php echo e(route('employees.store')); ?>" method="POST" class="w-full space-y-6">
    <?php echo csrf_field(); ?>
    <?php if($salesOnly ?? false): ?><input type="hidden" name="sales_only" value="1"><?php endif; ?>
    <?php if($marketingOnly ?? false): ?><input type="hidden" name="marketing_only" value="1"><?php endif; ?>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            حساب الدخول
        </div>
        <div class="p-5 sm:p-6 space-y-5">
            <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors font-tajawal"
                   style="border-color: <?php echo e(old('create_new_user', true) ? $themeColor . '50' : '#e5e7eb'); ?>; background: <?php echo e(old('create_new_user', true) ? $themeColor . '08' : '#fff'); ?>;">
                <input type="checkbox" name="create_new_user" id="create_new_user" value="1" class="mt-1 w-5 h-5 rounded"
                       style="accent-color: <?php echo e($themeColor); ?>;"
                       <?php echo e(old('create_new_user', true) ? 'checked' : ''); ?>

                       onchange="toggleUserSelection()">
                <div>
                    <span class="font-semibold text-gray-900 block">إنشاء حساب مستخدم جديد</span>
                    <span class="text-xs text-gray-500 mt-1 block">يُنشأ حساب بالبريد وكلمة المرور مع صلاحيات الدور المختار</span>
                </div>
            </label>

            <div id="user_selection_container" class="hidden">
                <label class="<?php echo e($label); ?>">ربط بمستخدم موجود</label>
                <select name="user_id" id="user_id" class="<?php echo e($input); ?>">
                    <option value="">اختر مستخدماً</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php if(old('user_id') == $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?> — <?php echo e($user->email); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div id="password_fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="<?php echo e($label); ?>">كلمة المرور *</label>
                    <input type="password" name="password" id="password" class="<?php echo e($input); ?>" placeholder="8 أحرف على الأقل">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="<?php echo e($input); ?>">
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            البيانات الشخصية
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">الاسم الأول *</label>
                <input name="first_name" value="<?php echo e(old('first_name')); ?>" required class="<?php echo e($input); ?>" placeholder="الاسم الأول">
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
                <input name="last_name" value="<?php echo e(old('last_name')); ?>" required class="<?php echo e($input); ?>" placeholder="اسم العائلة">
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
                <label class="<?php echo e($label); ?>">رقم الهاتف *</label>
                <input name="phone" value="<?php echo e(old('phone')); ?>" required class="<?php echo e($input); ?>" placeholder="01xxxxxxxxx" dir="ltr">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="<?php echo e($label); ?>">البريد الإلكتروني *</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="<?php echo e($input); ?>" placeholder="email@example.com" dir="ltr">
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
                <input name="address" value="<?php echo e(old('address')); ?>" class="<?php echo e($input); ?>" placeholder="المدينة، الحي...">
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="<?php echo e($label); ?>">رقم الموظف</label>
                <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm text-gray-600 font-tajawal flex items-center gap-2">
                    <svg class="w-4 h-4" style="color: <?php echo e($themeColor); ?>;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    يُولَّد تلقائياً عند الحفظ
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            الدور والوظيفة — قسم المبيعات
        </div>
        <div class="p-5 sm:p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $labelText): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="relative cursor-pointer block">
                    <input type="radio" name="crm_role" value="<?php echo e($val); ?>" class="peer sr-only"
                           <?php if(old('crm_role', ($marketingOnly ?? false) ? 'marketing_rep' : 'employee') === $val): echo 'checked'; endif; ?> onchange="updateRoleHint()">
                    <div class="role-card p-4 rounded-xl border-2 border-gray-200 transition-all text-center font-tajawal">
                        <div class="font-bold text-gray-900"><?php echo e($labelText); ?></div>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php if($marketingOnly ?? false): ?>
                                <?php if($val === 'marketing_manager'): ?> إدارة الحملات والفريق <?php else: ?> تنفيذ المهام وجمع Leads <?php endif; ?>
                            <?php elseif($val === 'manager'): ?>
                                لوحة الفريق + إدارة فرق المبيعات
                            <?php else: ?>
                                CRM — العملاء ومسار المبيعات
                            <?php endif; ?>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label class="<?php echo e($label); ?>">القسم</label>
                    <input type="hidden" name="department_id" value="<?php echo e($salesDepartment->id); ?>">
                    <div class="px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-sm font-semibold text-gray-800 font-tajawal">
                        <?php echo e($salesDepartment->name); ?>

                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="<?php echo e($label); ?>">المنصب (اختياري)</label>
                    <input name="position" id="position" value="<?php echo e(old('position')); ?>" class="<?php echo e($input); ?>"
                           placeholder="يُملأ تلقائياً حسب الدور">
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            بيانات التوظيف
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">الراتب الشهري (ج.م) *</label>
                <input type="number" name="salary" value="<?php echo e(old('salary')); ?>" required min="0" step="0.01" class="<?php echo e($input); ?>">
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
                <label class="<?php echo e($label); ?>">تاريخ التوظيف *</label>
                <input type="date" name="hire_date" value="<?php echo e(old('hire_date', date('Y-m-d'))); ?>" required class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['hire_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">نوع التوظيف *</label>
                <select name="employment_type" required class="<?php echo e($input); ?>">
                    <option value="full_time" <?php if(old('employment_type', 'full_time') === 'full_time'): echo 'selected'; endif; ?>>دوام كامل</option>
                    <option value="part_time" <?php if(old('employment_type') === 'part_time'): echo 'selected'; endif; ?>>دوام جزئي</option>
                    <option value="contract" <?php if(old('employment_type') === 'contract'): echo 'selected'; endif; ?>>عقد</option>
                    <option value="intern" <?php if(old('employment_type') === 'intern'): echo 'selected'; endif; ?>>متدرب</option>
                </select>
                <?php $__errorArgs = ['employment_type'];
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

    <?php echo $__env->make('employees.partials.work-schedule-fields', ['employee' => new \App\Models\Employee()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            جهة اتصال الطوارئ (اختياري)
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label class="<?php echo e($label); ?>">اسم جهة الاتصال</label>
                <input name="emergency_contact" value="<?php echo e(old('emergency_contact')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">هاتف الطوارئ</label>
                <input name="emergency_phone" value="<?php echo e(old('emergency_phone')); ?>" class="<?php echo e($input); ?>" dir="ltr">
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full pb-6">
        <a href="<?php echo e(route('employees.index', ($salesOnly ?? false) ? ['sales_only' => 1] : [])); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 font-tajawal">
            إلغاء والعودة للقائمة
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all font-tajawal"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            حفظ الموظف
        </button>
    </div>
</form>

<style>
    input[name="crm_role"]:checked + .role-card {
        border-color: <?php echo e($themeColor); ?>;
        background: <?php echo e($themeColor); ?>14;
        box-shadow: 0 4px 14px <?php echo e($themeColor); ?>25;
    }
</style>

<script>
function toggleUserSelection() {
    const createNew = document.getElementById('create_new_user').checked;
    const userBox = document.getElementById('user_selection_container');
    const passBox = document.getElementById('password_fields');
    const userSelect = document.getElementById('user_id');
    const pass = document.getElementById('password');
    const passConf = document.getElementById('password_confirmation');

    if (createNew) {
        userBox.classList.add('hidden');
        passBox.classList.remove('hidden');
        userSelect.required = false;
        userSelect.value = '';
        pass.required = true;
        passConf.required = true;
    } else {
        userBox.classList.remove('hidden');
        passBox.classList.add('hidden');
        userSelect.required = true;
        pass.required = false;
        passConf.required = false;
        pass.value = '';
        passConf.value = '';
    }
}

function updateRoleHint() {
    const role = document.querySelector('input[name="crm_role"]:checked')?.value;
    const pos = document.getElementById('position');
    if (!pos.value && role) {
        pos.placeholder = role === 'manager' ? 'مدير مبيعات' : 'موظف مبيعات';
    }
}

document.addEventListener('DOMContentLoaded', toggleUserSelection);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\employees\create.blade.php ENDPATH**/ ?>
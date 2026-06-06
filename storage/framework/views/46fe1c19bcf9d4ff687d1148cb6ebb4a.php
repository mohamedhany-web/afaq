<?php $__env->startSection('page-title', 'الملف الشخصي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $fieldLabel = 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = 'text-sm font-medium text-gray-900 font-tajawal';

    $roleKey = $user->roles->first()?->name;
    $roleLabel = $roleKey ? \App\Helpers\RoleHelper::getRoleName($roleKey) : 'مستخدم';
    $initial = mb_substr($user->name, 0, 1);
    $employee = $user->employee;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'الملف الشخصي',
    'subtitle' => $user->name . ' — ' . $user->email,
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('dashboard'),
    'actionLabel' => 'لوحة التحكم',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
<div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 w-full">
    <p class="font-bold text-red-800 font-tajawal mb-2">يرجى تصحيح الأخطاء التالية:</p>
    <ul class="list-disc pr-5 text-sm text-red-700 space-y-1 font-tajawal">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data" class="w-full">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 w-full">
        
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                    حسابي
                </div>
                <div class="p-5 sm:p-6 text-center">
                    <div class="relative inline-block mb-4">
                        <?php if($user->profile_picture): ?>
                            <img id="profilePreview"
                                 src="<?php echo e(asset('storage/' . $user->profile_picture)); ?>"
                                 alt="<?php echo e($user->name); ?>"
                                 class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl object-cover shadow-lg border-4 border-white mx-auto"
                                 style="border-color: <?php echo e($themeColor); ?>30;">
                        <?php else: ?>
                            <div id="profilePreviewFallback"
                                 class="h-24 w-24 sm:h-28 sm:w-28 rounded-2xl flex items-center justify-center mx-auto text-3xl font-bold text-white shadow-lg font-tajawal"
                                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                                <?php echo e($initial); ?>

                            </div>
                            <img id="profilePreview" src="" alt="" class="hidden h-24 w-24 sm:h-28 sm:w-28 rounded-2xl object-cover shadow-lg border-4 border-white mx-auto" style="border-color: <?php echo e($themeColor); ?>30;">
                        <?php endif; ?>
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 font-tajawal"><?php echo e($user->name); ?></h2>
                    <p class="text-sm text-gray-500 mt-1 font-tajawal" dir="ltr"><?php echo e($user->email); ?></p>

                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold font-tajawal"
                              style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">
                            <?php echo e($roleLabel); ?>

                        </span>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 font-tajawal">
                            نشط
                        </span>
                    </div>

                    <dl class="mt-6 space-y-3 text-right">
                        <div>
                            <dt class="<?php echo e($fieldLabel); ?>">تاريخ التسجيل</dt>
                            <dd class="<?php echo e($fieldValue); ?>"><?php echo e($user->created_at->format('Y/m/d')); ?></dd>
                        </div>
                        <div>
                            <dt class="<?php echo e($fieldLabel); ?>">آخر تحديث</dt>
                            <dd class="<?php echo e($fieldValue); ?>"><?php echo e($user->updated_at->format('Y/m/d H:i')); ?></dd>
                        </div>
                        <?php if($employee): ?>
                        <div>
                            <dt class="<?php echo e($fieldLabel); ?>">القسم</dt>
                            <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->department->name ?? '—'); ?></dd>
                        </div>
                        <div>
                            <dt class="<?php echo e($fieldLabel); ?>">المنصب</dt>
                            <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->position ?? '—'); ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <div class="px-5 sm:px-6 py-4 border-t border-gray-100 space-y-2">
                    <label for="profile_picture" class="w-full cursor-pointer inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all font-tajawal"
                           style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        تغيير الصورة
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/jpg,image/gif" class="hidden">

                    <?php if($user->profile_picture): ?>
                    <button type="button" onclick="deleteProfilePicture()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-red-700 bg-red-50 border border-red-200 hover:bg-red-100 transition-all font-tajawal">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        حذف الصورة
                    </button>
                    <?php endif; ?>

                    <p class="text-xs text-gray-500 text-center font-tajawal pt-1">JPG أو PNG — حتى 2 ميجابايت</p>

                    <?php if($employee): ?>
                    <a href="<?php echo e(route('employees.show', $employee)); ?>"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-all font-tajawal">
                        عرض ملف الموظف
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
                <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                    المعلومات الشخصية
                </div>
                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="<?php echo e($label); ?>">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                               class="<?php echo e($input); ?> <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               style="--tw-ring-color: <?php echo e($themeColor); ?>;"
                               placeholder="الاسم كما يظهر في النظام">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="<?php echo e($label); ?>">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required dir="ltr"
                               class="<?php echo e($input); ?> <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               style="--tw-ring-color: <?php echo e($themeColor); ?>;"
                               placeholder="name@example.com">
                        <?php $__errorArgs = ['email'];
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
                <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                    كلمة المرور
                </div>
                <div class="p-5 sm:p-6 space-y-4">
                    <p class="text-sm text-gray-600 font-tajawal bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        اترك حقول كلمة المرور فارغة إذا لم ترغب في تغييرها.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <label for="current_password" class="<?php echo e($label); ?>">كلمة المرور الحالية</label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                                   class="<?php echo e($input); ?> <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   style="--tw-ring-color: <?php echo e($themeColor); ?>;"
                                   placeholder="مطلوبة عند تغيير كلمة المرور">
                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="password" class="<?php echo e($label); ?>">كلمة المرور الجديدة</label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                   class="<?php echo e($input); ?> <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   style="--tw-ring-color: <?php echo e($themeColor); ?>;"
                                   placeholder="••••••••">
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
                            <label for="password_confirmation" class="<?php echo e($label); ?>">تأكيد كلمة المرور</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                                   class="<?php echo e($input); ?>"
                                   style="--tw-ring-color: <?php echo e($themeColor); ?>;"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6">
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-gray-700 bg-white border-2 border-gray-200 hover:bg-gray-50 transition-all font-tajawal">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all font-tajawal"
                            style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ التغييرات
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="deleteProfilePictureForm" action="<?php echo e(route('profile.delete-picture')); ?>" method="POST" class="hidden">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteProfilePicture() {
    if (confirm('هل أنت متأكد من حذف الصورة الشخصية؟')) {
        document.getElementById('deleteProfilePictureForm').submit();
    }
}

document.getElementById('profile_picture')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = document.getElementById('profilePreview');
        const fallback = document.getElementById('profilePreviewFallback');
        if (img) {
            img.src = ev.target.result;
            img.classList.remove('hidden');
        }
        if (fallback) {
            fallback.classList.add('hidden');
        }
    };
    reader.readAsDataURL(file);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/profile/index.blade.php ENDPATH**/ ?>
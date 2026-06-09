<?php
    $dev = $developer ?? null;
    $contract = $dev?->activeContract;
    $account = $dev?->accounts->first();
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 w-full">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">بيانات المطور</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">اسم المطور *</label>
                <input name="name" value="<?php echo e(old('name', $dev->name ?? '')); ?>" required class="<?php echo e($input); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الهاتف</label>
                <input name="phone" value="<?php echo e(old('phone', $dev->phone ?? '')); ?>" class="<?php echo e($input); ?>" dir="ltr">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">البريد</label>
                <input type="email" name="email" value="<?php echo e(old('email', $dev->email ?? '')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الموقع الإلكتروني</label>
                <input name="website" value="<?php echo e(old('website', $dev->website ?? '')); ?>" class="<?php echo e($input); ?>" dir="ltr" placeholder="https://">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">المدينة</label>
                <input name="city" value="<?php echo e(old('city', $dev->city ?? '')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">العنوان</label>
                <input name="address" value="<?php echo e(old('address', $dev->address ?? '')); ?>" class="<?php echo e($input); ?>">
            </div>
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">نبذة عن المطور</label>
                <textarea name="description" rows="3" class="<?php echo e($input); ?>"><?php echo e(old('description', $dev->description ?? '')); ?></textarea>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الحالة *</label>
                <select name="status" class="<?php echo e($input); ?>">
                    <?php $__currentLoopData = \App\Models\RealEstateDeveloper::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(old('status', $dev->status ?? 'active') === $k): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="<?php echo e($label); ?>">ملاحظات داخلية (للفريق فقط)</label>
                <textarea name="notes" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('notes', $dev->notes ?? '')); ?></textarea>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">بيانات التعاقد</div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="<?php echo e($label); ?>">مرجع العقد</label>
                    <input name="contract_ref" value="<?php echo e(old('contract_ref', $contract->contract_ref ?? '')); ?>" class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">نسبة العمولة %</label>
                    <input type="number" step="0.01" min="0" max="100" name="commission_percent" value="<?php echo e(old('commission_percent', $contract->commission_percent ?? '')); ?>" class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">مسؤول التواصل</label>
                    <input name="contact_person" value="<?php echo e(old('contact_person', $contract->contact_person ?? '')); ?>" class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">هاتف التواصل</label>
                    <input name="contact_phone" value="<?php echo e(old('contact_phone', $contract->contact_phone ?? '')); ?>" class="<?php echo e($input); ?>" dir="ltr">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">بداية التعاقد</label>
                    <input type="date" name="start_date" value="<?php echo e(old('start_date', optional($contract?->start_date)->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">نهاية التعاقد</label>
                    <input type="date" name="end_date" value="<?php echo e(old('end_date', optional($contract?->end_date)->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">حالة التعاقد</label>
                    <select name="contract_status" class="<?php echo e($input); ?>">
                        <?php $__currentLoopData = \App\Models\DeveloperContract::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php if(old('contract_status', $contract->status ?? 'active') === $k): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="exclusivity" value="1" id="exclusivity" <?php if(old('exclusivity', $contract->exclusivity ?? false)): echo 'checked'; endif; ?> class="w-4 h-4 rounded" style="accent-color:<?php echo e($themeColor); ?>;">
                    <label for="exclusivity" class="text-sm font-semibold font-tajawal text-gray-700">حصرية تسويق</label>
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">انتهاء الحصرية</label>
                    <input type="date" name="exclusivity_until" value="<?php echo e(old('exclusivity_until', optional($contract?->exclusivity_until)->format('Y-m-d'))); ?>" class="<?php echo e($input); ?>">
                </div>
                <div class="sm:col-span-2">
                    <label class="<?php echo e($label); ?>">شروط العرض</label>
                    <textarea name="listing_terms" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('listing_terms', $contract->listing_terms ?? '')); ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="<?php echo e($label); ?>">ملاحظات التعاقد</label>
                    <textarea name="contract_notes" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('contract_notes', $contract->notes ?? '')); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?> flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="<?php echo e($sectionBg); ?>">
                <span>بوابة المطور</span>
                <label class="flex items-center gap-2 text-sm font-semibold font-tajawal cursor-pointer">
                    <input type="checkbox" name="portal_enabled" value="1" <?php if(old('portal_enabled', $dev->portal_enabled ?? false)): echo 'checked'; endif; ?> class="w-4 h-4 rounded" style="accent-color:<?php echo e($themeColor); ?>;">
                    تفعيل البوابة
                </label>
            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 p-4 rounded-xl border-2 border-dashed font-tajawal text-xs text-gray-500" style="border-color:<?php echo e($themeColor); ?>30; background:<?php echo e($themeColor); ?>05;">
                    بعد التفعيل يدخل المطور من
                    <a href="<?php echo e(route('developer.login')); ?>" target="_blank" class="font-bold underline" style="color:<?php echo e($themeColor); ?>"><?php echo e(url('/developer/login')); ?></a>
                    لإدارة مشاريعه ووحداته وسابقة أعماله.
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">اسم المستخدم</label>
                    <input name="portal_account_name" value="<?php echo e(old('portal_account_name', $account->name ?? '')); ?>" class="<?php echo e($input); ?>">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">بريد الدخول</label>
                    <input type="email" name="portal_account_email" value="<?php echo e(old('portal_account_email', $account->email ?? '')); ?>" class="<?php echo e($input); ?>">
                    <?php $__errorArgs = ['portal_account_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">كلمة المرور <?php if($dev): ?><span class="text-gray-400 font-normal">(اتركها فارغة للإبقاء)</span><?php else: ?> * <?php endif; ?></label>
                    <input type="password" name="portal_account_password" class="<?php echo e($input); ?>" autocomplete="new-password">
                    <?php $__errorArgs = ['portal_account_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">تأكيد كلمة المرور</label>
                    <input type="password" name="portal_account_password_confirmation" class="<?php echo e($input); ?>" autocomplete="new-password">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">دور البوابة</label>
                    <select name="portal_account_role" class="<?php echo e($input); ?>">
                        <?php $__currentLoopData = \App\Models\DeveloperAccount::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php if(old('portal_account_role', $account->portal_role ?? 'owner') === $k): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\developers\partials\form.blade.php ENDPATH**/ ?>
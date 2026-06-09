<?php
    $ownershipType = old('ownership_type', $project->ownership_type ?? 'developer_third_party');
    $details = old('ownership_details', $project->ownership_details ?? []);
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = $label ?? 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = $sectionHeader ?? 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full" id="ownership-section">
    <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        نوع ملكية المشروع *
    </div>
    <div class="p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <?php $__currentLoopData = \App\Models\Project::OWNERSHIP_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="ownership-type-option cursor-pointer">
                <input type="radio" name="ownership_type" value="<?php echo e($val); ?>" class="sr-only peer"
                       <?php if($ownershipType === $val): echo 'checked'; endif; ?> data-ownership-type="<?php echo e($val); ?>">
                <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-current transition text-center font-tajawal text-sm font-bold text-gray-600 peer-checked:text-gray-900"
                     style="--tw-ring-color: <?php echo e($themeColor); ?>;" onchange="">
                    <?php echo e($txt); ?>

                </div>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['ownership_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <div class="ownership-pane <?php echo e($ownershipType !== 'owned' ? 'hidden' : ''); ?>" data-pane="owned">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">بيانات المشاريع المملوكة لشركتنا</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="<?php echo e($label); ?>">الجهة الداخلية المالكة</label>
                    <input name="ownership_details[internal_entity]" value="<?php echo e($details['internal_entity'] ?? ''); ?>" class="<?php echo e($input); ?>" placeholder="مثال: شركة أفاق للتطوير"></div>
                <div><label class="<?php echo e($label); ?>">تاريخ الاستحواذ</label>
                    <input type="date" name="ownership_details[acquisition_date]" value="<?php echo e($details['acquisition_date'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">قيمة الاستثمار (ج.م)</label>
                    <input type="number" min="0" step="0.01" name="ownership_details[investment_amount]" value="<?php echo e($details['investment_amount'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div class="sm:col-span-2"><label class="<?php echo e($label); ?>">ملاحظات إدارية</label>
                    <textarea name="ownership_details[management_notes]" rows="2" class="<?php echo e($input); ?>"><?php echo e($details['management_notes'] ?? ''); ?></textarea></div>
            </div>
        </div>

        <div class="ownership-pane <?php echo e($ownershipType !== 'partnership' ? 'hidden' : ''); ?>" data-pane="partnership">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">بيانات مشاريع الشراكة مع جهات أخرى</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="<?php echo e($label); ?>">اسم الشريك *</label>
                    <input name="ownership_details[partner_name]" value="<?php echo e($details['partner_name'] ?? ''); ?>" class="<?php echo e($input); ?>" placeholder="اسم شركة الشريك">
                    <?php $__errorArgs = ['ownership_details.partner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                <div><label class="<?php echo e($label); ?>">هاتف الشريك</label>
                    <input name="ownership_details[partner_phone]" value="<?php echo e($details['partner_phone'] ?? ''); ?>" class="<?php echo e($input); ?>" dir="ltr"></div>
                <div><label class="<?php echo e($label); ?>">مسؤول التواصل</label>
                    <input name="ownership_details[partner_contact]" value="<?php echo e($details['partner_contact'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">مرجع العقد</label>
                    <input name="ownership_details[contract_ref]" value="<?php echo e($details['contract_ref'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">حصتنا %</label>
                    <input type="number" min="0" max="100" name="ownership_details[our_share_percent]" value="<?php echo e($details['our_share_percent'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">حصة الشريك %</label>
                    <input type="number" min="0" max="100" name="ownership_details[partner_share_percent]" value="<?php echo e($details['partner_share_percent'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div><label class="<?php echo e($label); ?>">بداية الشراكة</label>
                    <input type="date" name="ownership_details[partnership_start]" value="<?php echo e($details['partnership_start'] ?? ''); ?>" class="<?php echo e($input); ?>"></div>
                <div class="sm:col-span-2"><label class="<?php echo e($label); ?>">ملاحظات الشراكة</label>
                    <textarea name="ownership_details[partnership_notes]" rows="2" class="<?php echo e($input); ?>"><?php echo e($details['partnership_notes'] ?? ''); ?></textarea></div>
            </div>
        </div>

        <div class="ownership-pane <?php echo e($ownershipType !== 'developer_third_party' ? 'hidden' : ''); ?>" data-pane="developer_third_party">
            <p class="text-xs text-gray-500 mb-3 font-tajawal">اختر مطوراً مسجلاً بتعاقد نشط — بيانات التعاقد تُدار من <a href="<?php echo e(route('admin.developers.index')); ?>" class="font-bold underline" style="color:<?php echo e($themeColor ?? \App\Helpers\SettingsHelper::getThemeColor()); ?>">إدارة المطورين العقاريين</a></p>
            <div class="mb-2">
                <?php echo $__env->make('partials.developer-search-select', [
                    'developerId' => old('real_estate_developer_id', $project->real_estate_developer_id ?? null),
                    'developerName' => old('developer_name', $project->developer_name ?? ''),
                    'inputClass' => $input,
                    'required' => true,
                    'contractedOnly' => true,
                    'allowCreate' => false,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php $__errorArgs = ['real_estate_developer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['developer_name'];
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
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const theme = <?php echo json_encode($themeColor ?? \App\Helpers\SettingsHelper::getThemeColor(), 15, 512) ?>;
    const radios = document.querySelectorAll('#ownership-section input[name="ownership_type"]');
    const panes = document.querySelectorAll('#ownership-section .ownership-pane');

    function syncOwnershipUI() {
        const selected = document.querySelector('#ownership-section input[name="ownership_type"]:checked');
        const type = selected?.value || 'developer_third_party';
        panes.forEach(p => p.classList.toggle('hidden', p.dataset.pane !== type));
        document.querySelectorAll('#ownership-section .ownership-type-option > div').forEach((box, i) => {
            const radio = radios[i];
            if (radio?.checked) {
                box.style.borderColor = theme;
                box.style.background = theme + '10';
            } else {
                box.style.borderColor = '';
                box.style.background = '';
            }
        });
    }

    radios.forEach(r => r.addEventListener('change', syncOwnershipUI));
    syncOwnershipUI();
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/projects/partials/ownership-fields.blade.php ENDPATH**/ ?>
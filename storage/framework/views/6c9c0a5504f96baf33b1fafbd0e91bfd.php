<?php
    $inventorySource = old('inventory_source', $project->inventory_source ?? 'developer');
    $ownershipType = \App\Models\Project::normalizeOwnershipType(old('ownership_type', $project->ownership_type ?? 'developer')) ?? 'developer';
    $developers = $developers ?? collect();
    $input = $input ?? 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = $label ?? 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $sources = \App\Models\Project::inventorySourceLabels();
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full mb-6" id="inventory-source-section">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        الخطوة 1 — نوع المخزون العقاري *
        <p class="text-xs font-normal text-gray-500 mt-1">اختر مصدر الوحدات أولاً — تظهر بعدها الحقول المناسبة</p>
    </div>
    <div class="p-5 sm:p-6">
        <input type="hidden" name="inventory_source" id="inventory_source" value="<?php echo e($inventorySource); ?>">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button"
                    class="inventory-source-card text-right p-4 rounded-2xl border-2 transition font-tajawal hover:shadow-md"
                    data-source="<?php echo e($key); ?>"
                    style="<?php echo e($inventorySource === $key ? 'border-color:' . $themeColor . '; background:' . $themeColor . '10;' : ''); ?>">
                <p class="font-bold text-gray-900"><?php echo e($txt); ?></p>
                <p class="text-xs text-gray-500 mt-1">
                    <?php if($key === 'company'): ?> وحدات مملوكة لأفاق — إدخال يدوي للوحدات
                    <?php elseif($key === 'non_company'): ?> وحدات طرف ثالث — نفس بيانات الوحدة
                    <?php else: ?> مشاريع مطورين — جداول سكني / تجاري / طبي
                    <?php endif; ?>
                </p>
            </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['inventory_source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <div id="pane-developer" class="inventory-pane mt-5 <?php echo e($inventorySource !== 'developer' ? 'hidden' : ''); ?>">
            <label class="<?php echo e($label); ?>">المطور العقاري *</label>
            <select name="real_estate_developer_id" id="developer_id_select" class="<?php echo e($input); ?>">
                <option value="">— اختر المطور —</option>
                <?php $__currentLoopData = $developers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($dev->id); ?>" <?php if((string) old('real_estate_developer_id', $project->real_estate_developer_id ?? '') === (string) $dev->id): echo 'selected'; endif; ?>>
                    <?php echo e($dev->name); ?><?php if($dev->city): ?> — <?php echo e($dev->city); ?><?php endif; ?>
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="hidden" name="developer_name" id="developer_name_hidden" value="<?php echo e(old('developer_name', $project->developer_name ?? '')); ?>">
            <?php if($developers->isEmpty()): ?>
            <p class="text-xs text-amber-700 mt-2 font-tajawal">لا يوجد مطورون بتعاقد نشط — أضفهم من <a href="<?php echo e(route('admin.developers.index')); ?>" class="underline font-bold">إدارة المطورين</a>.</p>
            <?php endif; ?>
            <?php $__errorArgs = ['real_estate_developer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div id="pane-non-company" class="inventory-pane mt-5 <?php echo e($inventorySource !== 'non_company' ? 'hidden' : ''); ?>">
            <label class="<?php echo e($label); ?>">نوع ملكية «وحدات الغير»</label>
            <select id="non_company_ownership" class="<?php echo e($input); ?>">
                <?php $__currentLoopData = config('project_inventory.non_company_ownership', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php if($ownershipType === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <input type="hidden" name="ownership_type" id="ownership_type_hidden" value="<?php echo e($ownershipType); ?>">
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hiddenSource = document.getElementById('inventory_source');
    const hiddenOwnership = document.getElementById('ownership_type_hidden');
    const nonCompanySelect = document.getElementById('non_company_ownership');
    const devSelect = document.getElementById('developer_id_select');
    const devNameHidden = document.getElementById('developer_name_hidden');
    const cards = document.querySelectorAll('.inventory-source-card');
    const panes = document.querySelectorAll('.inventory-pane');
    const detailsWrap = document.getElementById('project-details-after-source');
    const manualSection = document.getElementById('manual-units-section');
    const pricingSection = document.getElementById('classification-pricing-section');
    const ownershipSection = document.getElementById('ownership-section');
    const themeColor = <?php echo json_encode($themeColor, 15, 512) ?>;

    function syncDeveloperName() {
        if (!devSelect || !devNameHidden) return;
        const opt = devSelect.options[devSelect.selectedIndex];
        devNameHidden.value = opt && opt.value ? opt.textContent.split('—')[0].trim() : '';
    }

    function setSource(source) {
        if (!hiddenSource) return;
        hiddenSource.value = source;
        cards.forEach(c => {
            const active = c.dataset.source === source;
            c.style.borderColor = active ? themeColor : '';
            c.style.background = active ? themeColor + '10' : '';
        });
        panes.forEach(p => p.classList.add('hidden'));
        const pane = document.getElementById('pane-' + source);
        if (pane) pane.classList.remove('hidden');

        if (hiddenOwnership) {
            if (source === 'company') hiddenOwnership.value = 'afaq_private';
            else if (source === 'developer') hiddenOwnership.value = 'developer';
            else if (nonCompanySelect) hiddenOwnership.value = nonCompanySelect.value;
        }

        if (detailsWrap) detailsWrap.classList.toggle('hidden', !source);
        if (manualSection) manualSection.classList.toggle('hidden', source === 'developer');
        if (pricingSection) pricingSection.classList.toggle('hidden', source !== 'developer');
        if (ownershipSection) ownershipSection.classList.toggle('hidden', true);
    }

    cards.forEach(c => c.addEventListener('click', () => setSource(c.dataset.source)));
    nonCompanySelect?.addEventListener('change', () => {
        if (hiddenOwnership && hiddenSource?.value === 'non_company') {
            hiddenOwnership.value = nonCompanySelect.value;
        }
    });
    devSelect?.addEventListener('change', syncDeveloperName);

    setSource(hiddenSource?.value || 'developer');
    syncDeveloperName();
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/projects/partials/inventory-source-fields.blade.php ENDPATH**/ ?>
<?php
    $details = old('lead_source_details', isset($client) ? ($client->lead_source_details ?? []) : []);
    if (! is_array($details)) {
        $details = [];
    }
    $detailFields = config('client_lead_sources.detail_fields', []);
    $campaigns = $marketingCampaigns ?? collect();
?>

<div id="lead-source-details-wrap" class="sm:col-span-2 lg:col-span-4 space-y-3">
    <?php $__currentLoopData = $detailFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sourceKey => $fields): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="lead-source-detail-group hidden rounded-xl border border-gray-200 bg-gray-50/80 p-4 grid grid-cols-1 sm:grid-cols-2 gap-4"
         data-source="<?php echo e($sourceKey); ?>">
        <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldKey => $fieldLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="detail-field" data-field="<?php echo e($fieldKey); ?>">
            <label class="<?php echo e($label); ?>"><?php echo e($fieldLabel); ?> <span class="text-red-600">*</span></label>
            <input type="text"
                   name="lead_source_details[<?php echo e($fieldKey); ?>]"
                   value="<?php echo e(old("lead_source_details.{$fieldKey}", $details[$fieldKey] ?? '')); ?>"
                   class="<?php echo e($input); ?> lead-source-detail-input"
                   dir="<?php echo e(str_contains($fieldKey, 'number') ? 'ltr' : 'auto'); ?>"
                   maxlength="<?php echo e(str_contains($fieldKey, 'number') ? 50 : 255); ?>">
            <?php $__errorArgs = ["lead_source_details.{$fieldKey}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($sourceKey === 'marketing' && $campaigns->isNotEmpty()): ?>
        <div class="sm:col-span-2">
            <label class="<?php echo e($label); ?>">حملة تسويقية مسجّلة (اختياري)</label>
            <select name="marketing_campaign_id" class="<?php echo e($input); ?>">
                <option value="">— أو اكتب اسم الحملة أعلاه —</option>
                <?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($campaign->id); ?>" <?php if(old('marketing_campaign_id', $client->marketing_campaign_id ?? '') == $campaign->id): echo 'selected'; endif; ?>>
                    <?php echo e($campaign->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['marketing_campaign_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceSelect = document.querySelector('select[name="lead_source"]');
    const groups = document.querySelectorAll('.lead-source-detail-group');
    if (!sourceSelect || !groups.length) return;

    function syncSourceDetails() {
        const source = sourceSelect.value;
        groups.forEach(group => {
            const show = group.dataset.source === source;
            group.classList.toggle('hidden', !show);
            group.querySelectorAll('.lead-source-detail-input').forEach(inp => {
                inp.required = show;
                inp.disabled = !show;
            });
        });
    }

    sourceSelect.addEventListener('change', syncSourceDetails);
    syncSourceDetails();
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/source-details-fields.blade.php ENDPATH**/ ?>
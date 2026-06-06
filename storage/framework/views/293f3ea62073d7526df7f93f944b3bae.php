<?php $__env->startSection('page-title', $sale->product_service); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $sale->product_service,
    'subtitle' => 'تفاصيل الصفقة — ' . ($sale->client?->name ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    'actionUrl' => route('crm.pipeline.edit', $sale),
    'actionLabel' => 'تعديل',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 space-y-4 text-sm">
        <h3 class="font-bold text-gray-900 font-tajawal border-b pb-2">بيانات الصفقة</h3>
        <p><span class="text-gray-500">العميل:</span> <a href="<?php echo e(route('crm.clients.show', $sale->client)); ?>" class="font-semibold" style="color: <?php echo e($themeColor); ?>;"><?php echo e($sale->client?->name); ?></a></p>
        <p><span class="text-gray-500">المشروع:</span> <span class="text-gray-900"><?php echo e($sale->project?->name ?? '—'); ?></span></p>
        <p><span class="text-gray-500">المرحلة:</span> <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($sale->stage); ?></span></p>
        <p><span class="text-gray-500">القيمة:</span> <strong class="text-gray-900"><?php echo e(\App\Helpers\SettingsHelper::formatMoney($sale->estimated_value)); ?></strong></p>
        <p><span class="text-gray-500">نوع الوحدة:</span> <?php echo e($sale->unit_type ?? '—'); ?></p>
        <p><span class="text-gray-500">معاينة:</span> <?php echo e($sale->viewing_date?->format('Y-m-d') ?? '—'); ?></p>
        <p><span class="text-gray-500">مندوب المبيعات:</span> <?php echo e($sale->salesRep?->name); ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="font-bold mb-4 text-gray-900 font-tajawal">تحديث المرحلة</h3>
        <form action="<?php echo e(route('crm.pipeline.update-stage', $sale)); ?>" method="POST" class="space-y-3" id="deal-stage-form">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            <div class="flex gap-2">
                <select name="stage" id="deal-stage-select" class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal">
                    <?php $stageLabels = ['lead'=>'عميل محتمل','prospect'=>'مهتم','proposal'=>'عرض سعر','negotiation'=>'تفاوض','closed_won'=>'تم البيع','closed_lost'=>'خسارة']; ?>
                    <?php $__currentLoopData = ['lead','prospect','proposal','negotiation','closed_won','closed_lost']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if($sale->stage==$s): echo 'selected'; endif; ?>><?php echo e($stageLabels[$s] ?? $s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="px-5 py-3 rounded-xl text-white font-semibold font-tajawal" style="background: <?php echo e($themeColor); ?>;">تحديث</button>
            </div>
            <div id="deal-lost-fields" class="hidden space-y-2 p-4 rounded-xl bg-red-50 border border-red-100">
                <label class="block text-xs font-bold text-red-700 font-tajawal">سبب الخسارة *</label>
                <select name="lost_reason" class="w-full border-2 border-red-200 rounded-xl px-4 py-2 text-sm font-tajawal">
                    <option value="">— اختر —</option>
                    <?php $__currentLoopData = config('crm_intelligence.lost_reasons'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($sale->lost_reason==$key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <textarea name="lost_reason_notes" rows="2" class="w-full border-2 border-red-200 rounded-xl px-4 py-2 text-sm font-tajawal" placeholder="تفاصيل إضافية"><?php echo e($sale->lost_reason_notes); ?></textarea>
            </div>
        </form>
        <?php if($sale->lost_reason): ?>
            <p class="mt-3 text-sm text-red-600 font-tajawal">سبب الخسارة: <?php echo e(config('crm_intelligence.lost_reasons')[$sale->lost_reason] ?? $sale->lost_reason); ?></p>
        <?php endif; ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sel = document.getElementById('deal-stage-select');
            const box = document.getElementById('deal-lost-fields');
            function toggle() { box?.classList.toggle('hidden', sel?.value !== 'closed_lost'); }
            sel?.addEventListener('change', toggle);
            toggle();
        });
        </script>
        <?php if($sale->notes): ?><p class="mt-4 text-sm text-gray-600 p-4 bg-gray-50 rounded-xl"><?php echo e($sale->notes); ?></p><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/pipeline/show.blade.php ENDPATH**/ ?>
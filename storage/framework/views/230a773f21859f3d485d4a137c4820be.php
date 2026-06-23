<?php
    $filterId = 'client-deals-filter-' . $client->id;
    $listId = 'client-deals-list-' . $client->id;
    $sales = $client->sales->sortByDesc('updated_at');
?>
<div id="client-deals-section" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">صفقات العميل</h3>
            <p class="text-xs text-gray-500 mt-0.5 font-tajawal"><?php echo e($sales->count()); ?> صفقة · <?php echo e($money($sales->sum('estimated_value'))); ?></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <select id="<?php echo e($filterId); ?>" class="border-2 border-gray-200 rounded-xl px-3 py-1.5 text-xs font-semibold font-tajawal text-gray-700 bg-white">
                <option value="">كل المراحل</option>
                <?php $__currentLoopData = $stageLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>" class="text-xs font-semibold font-tajawal px-3 py-1.5 rounded-lg text-white"
               style="background: <?php echo e($themeColor); ?>;">+ صفقة جديدة</a>
        </div>
    </div>

    <div id="<?php echo e($listId); ?>" class="p-5 sm:p-6 space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('crm.pipeline.show', $sale)); ?>" data-deal-stage="<?php echo e($sale->stage); ?>"
           class="client-deal-row block p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/80 transition-all">
            <div class="flex flex-col gap-3">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-gray-900 font-tajawal"><?php echo e($sale->product_service); ?></div>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5 text-xs text-gray-500 font-tajawal">
                            <?php if($sale->project): ?>
                            <span>
                                مشروع:
                                <span class="font-medium" style="color: <?php echo e($themeColor); ?>;"><?php echo e($sale->project->name); ?></span>
                            </span>
                            <?php endif; ?>
                            <?php if($sale->salesRep): ?>
                            <span class="text-gray-300 hidden sm:inline">·</span>
                            <span>مندوب: <?php echo e($sale->salesRep->name); ?></span>
                            <?php endif; ?>
                            <?php if($sale->updated_at): ?>
                            <span class="text-gray-300 hidden sm:inline">·</span>
                            <span>آخر تحديث: <?php echo e($sale->updated_at->format('Y/m/d')); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal bg-gray-100 text-gray-700">
                            <?php echo e($stageLabels[$sale->stage] ?? $sale->stage); ?>

                        </span>
                        <span class="font-bold text-sm font-tajawal whitespace-nowrap" style="color: <?php echo e($themeColor); ?>;"><?php echo e($money($sale->estimated_value)); ?></span>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-10">
            <p class="text-gray-400 font-tajawal mb-4">لا توجد صفقات لهذا العميل بعد</p>
            <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>" class="inline-flex items-center px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
               style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                إنشاء أول صفقة
            </a>
        </div>
        <?php endif; ?>
        <p id="<?php echo e($listId); ?>-empty" class="hidden text-center text-sm text-gray-400 font-tajawal py-6">لا توجد صفقات في هذه المرحلة.</p>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filter = document.getElementById('<?php echo e($filterId); ?>');
    const rows = document.querySelectorAll('#<?php echo e($listId); ?> .client-deal-row');
    const emptyMsg = document.getElementById('<?php echo e($listId); ?>-empty');
    if (!filter || !rows.length) return;

    filter.addEventListener('change', function () {
        const stage = filter.value;
        let visible = 0;
        rows.forEach(function (row) {
            const show = !stage || row.dataset.dealStage === stage;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        if (emptyMsg) {
            emptyMsg.classList.toggle('hidden', visible > 0 || !stage);
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/deals-list.blade.php ENDPATH**/ ?>
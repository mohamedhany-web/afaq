<div id="client-deals-kanban" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">
        <h3 class="font-bold text-gray-900 font-tajawal">صفقات العميل — اسحب بين المراحل</h3>
        <p class="text-xs text-gray-500 mt-1 font-tajawal">كل عمود = مرحلة الصفقة. اسحب البطاقة لتحديث الحالة.</p>
    </div>

    <div class="p-4 sm:p-5">
        <div class="mb-4">
            <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">مراحل نشطة</p>
            <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
                <?php $__currentLoopData = $activeStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('crm.pipeline.partials.client-deal-column', [
                    'stage' => $stage,
                    'deals' => $dealColumns[$stage] ?? collect(),
                    'total' => $dealStageTotals[$stage] ?? ['count' => 0, 'value' => 0],
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">النتيجة</p>
            <div class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
                <?php $__currentLoopData = $closedStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('crm.pipeline.partials.client-deal-column', [
                    'stage' => $stage,
                    'deals' => $dealColumns[$stage] ?? collect(),
                    'total' => $dealStageTotals[$stage] ?? ['count' => 0, 'value' => 0],
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php if($dealsCount === 0): ?>
        <div class="text-center py-8 mt-2">
            <p class="text-gray-400 font-tajawal mb-3">لا توجد صفقات بعد</p>
            <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>"
               class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
               style="background: <?php echo e($themeColor); ?>;">إنشاء أول صفقة</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\partials\client-deals-kanban.blade.php ENDPATH**/ ?>
<?php
    use App\Services\CrmScopeService;
    $activeStages = CrmScopeService::activeLeadStages();
    $closedStages = CrmScopeService::closedLeadStages();
    $currentStage = $client->lead_stage ?? CrmScopeService::LEAD_STAGE_NEW;
    $stageColors = CrmScopeService::clientLeadStageColors();
    $stageLabels = $stageLabels ?? CrmScopeService::leadStageLabels();
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">مسار العميل — اسحب لتحويل المرحلة</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">المرحلة الحالية: <strong><?php echo e($stageLabels[$currentStage] ?? $currentStage); ?></strong></p>
        </div>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">Drag & Drop</span>
    </div>
    <div class="p-4 sm:p-5 overflow-x-auto">
        <div class="flex gap-3 min-w-max pb-2">
            <?php $__currentLoopData = array_merge($activeStages, $closedStages); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $color = $stageColors[$stage] ?? ['bg' => $themeColor, 'light' => '#f3f4f6']; ?>
                <div class="w-56 shrink-0 rounded-xl border border-gray-200 overflow-hidden bg-gray-50/50">
                    <div class="px-3 py-2 border-b border-gray-100 text-center" style="background: <?php echo e($color['light']); ?>;">
                        <span class="text-xs font-bold font-tajawal" style="color: <?php echo e($color['bg']); ?>;"><?php echo e($stageLabels[$stage] ?? $stage); ?></span>
                    </div>
                    <div class="journey-kanban-zone kanban-drop-zone p-2 min-h-[100px]" data-journey-stage="<?php echo e($stage); ?>">
                        <?php if($currentStage === $stage): ?>
                        <div class="kanban-card bg-white rounded-lg p-3 border-2 shadow-sm cursor-grab active:cursor-grabbing font-tajawal"
                             style="border-color: <?php echo e($color['bg']); ?>;"
                             data-client-id="<?php echo e($client->id); ?>">
                            <p class="font-bold text-sm text-gray-900">
                                <a href="<?php echo e($client->profileUrl()); ?>" class="hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($client->name); ?></a>
                            </p>
                            <p class="text-xs text-gray-500 mt-1" dir="ltr"><?php echo e($client->phone); ?></p>
                            <?php if($stage === 'new' && $client->created_at): ?>
                            <p class="text-[10px] text-blue-600 mt-1 font-tajawal">أُضيف <?php echo e($client->created_at->format('Y/m/d H:i')); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="kanban-empty h-16 rounded-lg border border-dashed border-gray-200 flex items-center justify-center">
                            <span class="text-[10px] text-gray-400">أفلت هنا</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php echo $__env->make('crm.partials.kanban-scripts', [
    'updateUrl' => route('crm.clients.update-lead-stage', ['client' => '__ID__']),
    'payloadKey' => 'lead_stage',
    'itemKey' => 'clientId',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\journey-kanban.blade.php ENDPATH**/ ?>
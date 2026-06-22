<?php
    $accent = $accentColor ?? '#6366f1';
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $deals = $client->sales ?? collect();
    $dealsValue = $deals->whereNotIn('stage', ['closed_lost'])->sum('estimated_value');
    $dealStageColors = [
        'lead' => '#6366f1', 'prospect' => '#3b82f6', 'proposal' => '#0ea5e9',
        'negotiation' => '#f59e0b', 'closed_won' => '#16a34a', 'closed_lost' => '#ef4444',
    ];
?>
<div class="kanban-card group bg-white rounded-lg border border-gray-200 shadow-sm hover:border-gray-300 transition-all cursor-grab active:cursor-grabbing font-tajawal"
     data-client-id="<?php echo e($client->id); ?>">
    
    <div class="p-2 border-b border-gray-50">
        <div class="flex items-start gap-1">
            <span class="shrink-0 mt-0.5 text-gray-300 group-hover:text-gray-400 pointer-events-none" aria-hidden="true">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/><circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/></svg>
            </span>
            <div class="flex-1 min-w-0">
                <a href="<?php echo e($client->profileUrl()); ?>" class="font-bold text-[12px] text-gray-900 hover:underline block truncate" draggable="false"><?php echo e($client->name); ?></a>
                <p class="text-[10px] text-gray-500 truncate" dir="ltr"><?php echo e($client->phone); ?></p>
                <?php if(($client->lead_stage ?? '') === 'new' && $client->created_at): ?>
                <p class="text-[9px] text-blue-600 mt-0.5 font-tajawal"><?php echo e($client->created_at->format('Y/m/d H:i')); ?></p>
                <?php endif; ?>
                <div class="flex flex-wrap items-center gap-1 mt-1">
                    <?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if($deals->count()): ?>
                    <span class="text-[9px] px-1.5 py-px rounded bg-gray-100 text-gray-600 font-semibold"><?php echo e($deals->count()); ?> صفقة</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if($dealsValue > 0): ?>
        <p class="text-[10px] font-bold mt-1.5 tabular-nums" style="color: <?php echo e($accent); ?>;">قيمة الصفقات: <?php echo e($money($dealsValue)); ?></p>
        <?php endif; ?>
    </div>

    
    <details class="client-deals-panel border-b border-gray-50" open>
        <summary class="px-2 py-1.5 text-[10px] font-bold text-gray-600 cursor-pointer hover:bg-gray-50 select-none list-none flex items-center justify-between">
            <span>الصفقات والحالات</span>
            <span class="text-gray-400"><?php echo e($deals->count()); ?></span>
        </summary>
        <div class="px-2 pb-2 space-y-1 max-h-36 overflow-y-auto" onclick="event.stopPropagation()">
            <?php $__empty_1 = true; $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-md border border-gray-100 bg-gray-50/80 p-1.5 text-[10px]">
                <div class="flex items-start justify-between gap-1">
                    <a href="<?php echo e(route('crm.pipeline.show', $sale)); ?>" class="font-semibold text-gray-800 truncate flex-1 hover:underline"><?php echo e($sale->product_service); ?></a>
                    <span class="shrink-0 font-bold tabular-nums" style="color: <?php echo e($accent); ?>;"><?php echo e($money($sale->estimated_value)); ?></span>
                </div>
                <?php if($sale->project): ?>
                <p class="text-gray-500 truncate mt-0.5"><?php echo e($sale->project->name); ?></p>
                <?php endif; ?>
                <div class="flex items-center gap-1 mt-1">
                    <select class="deal-stage-select flex-1 min-w-0 rounded border border-gray-200 bg-white px-1 py-0.5 text-[9px] font-semibold"
                            data-sale-id="<?php echo e($sale->id); ?>"
                            data-update-url="<?php echo e(route('crm.pipeline.update-stage', $sale)); ?>"
                            style="color: <?php echo e($dealStageColors[$sale->stage] ?? '#374151'); ?>;">
                        <?php $__currentLoopData = $stageLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($sale->stage === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span class="shrink-0 text-[9px] text-gray-400 tabular-nums"><?php echo e($sale->probability_percentage); ?>%</span>
                </div>
                <?php if($sale->viewing_date): ?>
                <p class="text-[9px] text-amber-700 mt-0.5">اجتماع: <?php echo e(\Carbon\Carbon::parse($sale->viewing_date)->format('Y/m/d')); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-[10px] text-gray-400 text-center py-2">لا توجد صفقات</p>
            <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>"
               class="block text-center text-[10px] font-bold py-1 rounded-md hover:opacity-90"
               style="color: <?php echo e($accent); ?>; background: <?php echo e($accent); ?>12;">+ إضافة صفقة</a>
            <?php endif; ?>
            <?php if($deals->count()): ?>
            <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>"
               class="block text-center text-[9px] font-semibold text-gray-500 hover:text-gray-700 pt-0.5">+ صفقة أخرى</a>
            <?php endif; ?>
        </div>
    </details>

    
    <details class="client-log-panel" onclick="event.stopPropagation()">
        <summary class="px-2 py-1.5 text-[10px] font-bold cursor-pointer hover:bg-gray-50 select-none list-none"
                 style="color: <?php echo e($accent); ?>;">تسجيل متابعة / بيانات</summary>
        <form class="client-interaction-form px-2 pb-2 space-y-1.5"
              data-url="<?php echo e(route('crm.clients.log-interaction', $client)); ?>"
              onclick="event.stopPropagation()">
            <?php echo csrf_field(); ?>
            <select name="interaction_type" class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal" required>
                <?php $__currentLoopData = $interactionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php if($deals->count()): ?>
            <select name="sale_id" class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal interaction-sale-select hidden">
                <option value="">— ربط بصفقة (اختياري) —</option>
                <?php $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($sale->id); ?>"><?php echo e(\Illuminate\Support\Str::limit($sale->product_service, 30)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php endif; ?>
            <input type="date" name="viewing_date" class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal interaction-viewing-date hidden">
            <textarea name="notes" rows="2" required placeholder="اكتب تفاصيل المكالمة، الاجتماع، أو الملاحظة..."
                      class="w-full rounded-md border border-gray-200 px-2 py-1 text-[10px] font-tajawal resize-none"></textarea>
            <button type="submit" class="w-full py-1.5 rounded-md text-[10px] font-bold text-white transition hover:opacity-90 disabled:opacity-50"
                    style="background: <?php echo e($accent); ?>;">حفظ</button>
            <p class="interaction-msg text-[9px] text-center hidden"></p>
        </form>
    </details>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\partials\client-card.blade.php ENDPATH**/ ?>
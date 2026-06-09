<?php
    $deptColors = [
        'marketing' => '#8b5cf6',
        'sales' => '#3b82f6',
        'collection' => '#0ea5e9',
        'customer_service' => '#10b981',
        'maintenance' => '#f59e0b',
        'admin' => '#6b7280',
    ];
    $eventIcons = [
        'lead_created' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        'interaction' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'deal_won' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'deal_lost' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'default' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6 w-full">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <div>
            <h3 class="font-bold text-gray-900 font-tajawal">خط زمني موحّد للعميل</h3>
            <p class="text-xs text-gray-500 mt-1 font-tajawal">كل التفاعلات عبر الأقسام — من أول Lead حتى ما بعد البيع</p>
        </div>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal bg-gray-100 text-gray-600"><?php echo e($timeline->count()); ?> حدث</span>
    </div>
    <div class="p-5 sm:p-6">
        <?php $__empty_1 = true; $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isModel = $event instanceof \App\Models\ClientTimelineEvent;
                $dept = $isModel ? $event->department : ($event['department'] ?? 'sales');
                $type = $isModel ? $event->event_type : ($event['event_type'] ?? 'default');
                $title = $isModel ? $event->title : ($event['title'] ?? '—');
                $desc = $isModel ? $event->description : ($event['description'] ?? null);
                $user = $isModel ? $event->user : ($event['user'] ?? null);
                $occurred = $isModel ? $event->occurred_at : ($event['occurred_at'] ?? now());
                $meta = $isModel ? ($event->meta ?? []) : ($event['meta'] ?? []);
                $deptColor = $deptColors[$dept] ?? $themeColor;
                $deptLabel = config('crm_intelligence.departments')[$dept] ?? $dept;
                $iconPath = $eventIcons[$type] ?? $eventIcons['default'];
            ?>
            <div class="flex gap-4 pb-6 last:pb-0 relative">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background: <?php echo e($deptColor); ?>15;">
                        <svg class="w-5 h-5" style="color: <?php echo e($deptColor); ?>;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($iconPath); ?>"/>
                        </svg>
                    </div>
                    <?php if(!$loop->last): ?>
                        <div class="w-0.5 flex-1 mt-2 bg-gray-200"></div>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0 pb-2">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="font-bold text-sm text-gray-900 font-tajawal"><?php echo e($title); ?></span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold" style="background: <?php echo e($deptColor); ?>12; color: <?php echo e($deptColor); ?>;"><?php echo e($deptLabel); ?></span>
                    </div>
                    <?php if($desc): ?>
                        <p class="text-sm text-gray-600 font-tajawal whitespace-pre-line"><?php echo e(Str::limit($desc, 200)); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($meta['lost_reason_label'])): ?>
                        <p class="text-xs text-red-600 mt-1 font-tajawal">سبب الخسارة: <?php echo e($meta['lost_reason_label']); ?></p>
                    <?php endif; ?>
                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-400 font-tajawal">
                        <span><?php echo e($occurred instanceof \Carbon\Carbon ? $occurred->format('Y/m/d H:i') : \Carbon\Carbon::parse($occurred)->format('Y/m/d H:i')); ?></span>
                        <?php if($user): ?>
                            <span><?php echo e($user->name ?? '—'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-gray-400 py-8 font-tajawal">لا توجد أحداث مسجّلة بعد — ستظهر تلقائياً مع التفاعلات</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\unified-timeline.blade.php ENDPATH**/ ?>
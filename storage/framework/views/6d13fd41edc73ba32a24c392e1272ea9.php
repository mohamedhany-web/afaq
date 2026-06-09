<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $priorityColors = config('crm_tasks.priority_colors', []);
    $pColor = $priorityColors[$task->priority] ?? $themeColor;
    $isOverdue = $task->isOverdue();
?>
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden <?php echo e($isOverdue ? 'ring-2 ring-red-200' : ''); ?>">
    <div class="h-1" style="background:<?php echo e($pColor); ?>"></div>
    <div class="p-4">
        <div class="flex items-start justify-between gap-2 mb-2">
            <a href="<?php echo e(route('crm.tasks.show', $task)); ?>" class="font-bold text-gray-900 font-tajawal hover:underline leading-snug"><?php echo e($task->title); ?></a>
            <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full font-bold text-white" style="background:<?php echo e($pColor); ?>"><?php echo e($task->priorityLabel()); ?></span>
        </div>
        <div class="flex flex-wrap gap-1.5 text-[10px] mb-3">
            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-tajawal"><?php echo e($task->categoryLabel()); ?></span>
            <span class="px-2 py-0.5 rounded-full font-tajawal <?php echo e($isOverdue ? 'bg-red-100 text-red-700' : 'bg-blue-50 text-blue-700'); ?>"><?php echo e($task->statusLabel()); ?></span>
            <?php if($task->auto_generated): ?>
            <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-tajawal">تلقائية</span>
            <?php endif; ?>
        </div>
        <div class="text-xs text-gray-500 space-y-1 font-tajawal">
            <p>المكلف: <strong class="text-gray-700"><?php echo e($task->assignee?->name); ?></strong></p>
            <p>الموعد: <strong class="tabular-nums <?php echo e($isOverdue ? 'text-red-600' : ''); ?>"><?php echo e($task->due_at->format('Y/m/d H:i')); ?></strong></p>
            <?php if($task->client): ?>
            <p>العميل: <a href="<?php echo e(route('crm.clients.show', $task->client)); ?>" class="font-semibold" style="color:<?php echo e($themeColor); ?>"><?php echo e($task->client->name); ?></a></p>
            <?php endif; ?>
        </div>
        <?php if($task->performance_score && $task->status === 'completed'): ?>
        <p class="mt-2 text-xs font-bold text-green-700 font-tajawal">الأداء: <?php echo e($task->performance_score); ?>%</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\tasks\partials\task-card.blade.php ENDPATH**/ ?>
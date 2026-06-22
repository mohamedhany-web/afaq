
<?php $__env->startSection('page-title', 'مسار العملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'مسار العملاء',
    'subtitle' => 'تتبّع العملاء حسب مرحلة الرحلة — العملاء الجدد يبدأون في خانة New Lead / جديد',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'New Lead / جديد', 'value' => $stats['new_queue'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />', 'href' => route('crm.pipeline.index', ['lead_stage' => 'new']) . '#pipeline-clients-kanban', 'linkLabel' => 'عرض الخانة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'جدد اليوم', 'value' => $stats['new_today'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'href' => route('crm.pipeline.index', ['lead_stage' => 'new', 'created_from' => today()->toDateString(), 'created_to' => today()->toDateString()]) . '#pipeline-clients-kanban', 'linkLabel' => 'عرض جدد اليوم'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غير موزّعين (جدد)', 'value' => $stats['unassigned_new'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />', 'href' => route('crm.leads.distribution'), 'linkLabel' => 'توزيع العملاء'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => $stats['total'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />', 'href' => route('crm.pipeline.index') . '#pipeline-clients-kanban', 'linkLabel' => 'عرض الكل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مراحل نشطة', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => route('crm.pipeline.index') . '#pipeline-clients-kanban', 'linkLabel' => 'عرض المسار'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('crm.pipeline.partials.view-switcher', ['current' => 'kanban'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.partials.filter-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div id="pipeline-clients-kanban" class="mb-8">
    <div class="flex items-center gap-3 mb-3 flex-wrap">
        <h2 class="text-base font-bold text-gray-900 font-tajawal">مراحل الرحلة — نشطة</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">
            <?php echo e(number_format($stats['new_queue'])); ?> في خانة New Lead / جديد
        </span>
    </div>
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
        <?php $__currentLoopData = $activeStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!request('lead_stage') || request('lead_stage') === $stage): ?>
            <?php echo $__env->make('crm.pipeline.partials.client-column', [
                'stage' => $stage,
                'columns' => $columns,
                'stageLabels' => $stageLabels,
                'stageTotals' => $stageTotals,
                'stageColors' => $stageColors,
                'themeColor' => $themeColor,
                'interactionTypes' => $interactionTypes,
                'dealStageLabels' => $dealStageLabels,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<div>
    <div class="flex items-center gap-3 mb-3 flex-wrap">
        <h2 class="text-base font-bold text-gray-900 font-tajawal">النتيجة</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 font-medium font-tajawal"><?php echo e(number_format($stats['won'])); ?> تم البيع</span>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 font-medium font-tajawal"><?php echo e(number_format($stats['lost'])); ?> خسارة</span>
    </div>
    <?php if($showClosed): ?>
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
        <?php $__currentLoopData = $closedStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('crm.pipeline.partials.client-column', [
                'stage' => $stage,
                'columns' => $columns,
                'stageLabels' => $stageLabels,
                'stageTotals' => $stageTotals,
                'stageColors' => $stageColors,
                'themeColor' => $themeColor,
                'interactionTypes' => $interactionTypes,
                'dealStageLabels' => $dealStageLabels,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <a href="<?php echo e(route('crm.pipeline.index', array_merge(request()->query(), ['show_closed' => 1]))); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 font-tajawal">
        عرض المراحل المغلقة (<?php echo e(number_format($stats['won'] + $stats['lost'])); ?>)
    </a>
    <?php endif; ?>
</div>

<?php echo $__env->make('crm.partials.lost-reason-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('crm.partials.pipeline-client-scripts', [
    'updateUrl' => route('crm.clients.update-lead-stage', ['client' => '__ID__']),
    'loadMoreUrl' => route('crm.pipeline.column-clients', ['stage' => '__STAGE__']),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\index-clients-kanban.blade.php ENDPATH**/ ?>
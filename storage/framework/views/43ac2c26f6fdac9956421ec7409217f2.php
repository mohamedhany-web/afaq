
<?php $__env->startSection('page-title', 'مسار الصفقات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $stageColors = [
        'lead' => ['bg' => '#6366f1', 'light' => '#eef2ff'],
        'prospect' => ['bg' => '#3b82f6', 'light' => '#eff6ff'],
        'proposal' => ['bg' => '#0ea5e9', 'light' => '#f0f9ff'],
        'negotiation' => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
        'closed_won' => ['bg' => '#16a34a', 'light' => '#f0fdf4'],
        'closed_lost' => ['bg' => '#ef4444', 'light' => '#fef2f2'],
    ];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'مسار الصفقات',
    'subtitle' => 'عرض Kanban حسب مرحلة الصفقة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    'actionUrl' => route('crm.pipeline.create'),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الصفقات', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صفقات نشطة', 'value' => $stats['active'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تم البيع', 'value' => $stats['won'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قيمة المسار', 'value' => $money($stats['pipeline_value']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إيرادات مكتملة', 'value' => $money($stats['won_value']), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('crm.pipeline.partials.view-switcher', ['current' => 'deals'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <input type="hidden" name="view" value="deals">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم العميل، المشروع، أو وصف الصفقة..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-44">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">مرحلة الصفقة</label>
            <select name="stage" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل المراحل النشطة</option>
                <?php $__currentLoopData = $stageLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>" <?php if(request('stage') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
            <?php if(request()->hasAny(['search', 'stage', 'show_closed'])): ?>
            <a href="<?php echo e(route('crm.pipeline.index', ['view' => 'deals'])); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="mb-8">
    <div class="flex items-center gap-3 mb-3">
        <h2 class="text-base font-bold text-gray-900 font-tajawal">مراحل البيع النشطة</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full font-medium" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e(number_format($stats['active'])); ?> صفقة</span>
    </div>
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
        <?php $__currentLoopData = $activeStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!request('stage') || request('stage') === $stage): ?>
            <?php echo $__env->make('crm.pipeline.partials.column', compact('stage', 'columns', 'stageLabels', 'stageTotals', 'stageColors', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<div>
    <div class="flex items-center gap-3 mb-3 flex-wrap">
        <h2 class="text-base font-bold text-gray-900 font-tajawal">نتيجة الصفقات</h2>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 font-medium"><?php echo e(number_format($stats['won'])); ?> ربح</span>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 font-medium"><?php echo e(number_format($stats['lost'])); ?> خسارة</span>
    </div>
    <?php if($showClosed): ?>
    <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
        <?php $__currentLoopData = $closedStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('crm.pipeline.partials.column', compact('stage', 'columns', 'stageLabels', 'stageTotals', 'stageColors', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <a href="<?php echo e(route('crm.pipeline.index', array_merge(request()->query(), ['view' => 'deals', 'show_closed' => 1]))); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 font-tajawal">
        عرض الصفقات المغلقة (<?php echo e(number_format($stats['won'] + $stats['lost'])); ?>)
    </a>
    <?php endif; ?>
</div>
<?php echo $__env->make('crm.partials.lost-reason-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('crm.partials.pipeline-kanban-scripts', [
    'updateUrl' => route('crm.pipeline.update-stage', ['sale' => '__ID__']),
    'loadMoreUrl' => route('crm.pipeline.column-deals', ['stage' => '__STAGE__']),
    'payloadKey' => 'stage',
    'itemKey' => 'dealId',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\index-deals.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'لوحتي — التسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $k = $kpis; ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'لوحة موظف التسويق',
    'subtitle' => now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />',
    'actionUrl' => route('marketing.reports.index'),
    'actionLabel' => 'تقريري اليومي',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(!empty($reportPending) && count($reportPending)): ?>
<div class="mb-6 p-4 rounded-2xl border-2 border-red-200 bg-red-50 font-tajawal">
    <p class="font-bold text-red-900 text-sm">التقرير اليومي إلزامي — لم يُرفع بعد</p>
    <a href="<?php echo e(route('marketing.reports.index')); ?>" class="inline-block mt-2 px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:#7c3aed">رفع الآن</a>
</div>
<?php endif; ?>

<?php if($activePlan): ?>
<div class="mb-6 p-4 rounded-2xl border border-gray-200 bg-white shadow font-tajawal">
    <p class="text-xs font-bold text-gray-500 mb-1">خطة الشهر</p>
    <p class="font-bold text-gray-900"><?php echo e($activePlan->title); ?></p>
    <p class="text-xs text-gray-600 mt-1 mb-3"><?php echo e($activePlan->periodLabel()); ?> — تقدم الفريق <?php echo e($activePlan->activities_count ? round(($activePlan->completed_activities_count / $activePlan->activities_count) * 100) : 0); ?>%</p>
    <a href="<?php echo e(route('marketing.plans.show', $activePlan)); ?>" class="inline-flex px-4 py-2 rounded-xl text-white text-xs font-bold" style="background:<?php echo e($themeColor); ?>">مهامي في الخطة</a>
</div>
<?php endif; ?>

<div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهام اليوم', 'value' => $k['activities_today'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'Leads اليوم', 'value' => $k['leads_today'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $k['activities_overdue'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'دورية نشطة', 'value' => $k['recurring_active'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">مهامي القادمة</div>
    <div class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $upcomingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="px-5 py-4 font-tajawal flex justify-between gap-3">
            <div>
                <p class="font-semibold"><?php echo e($activity->title); ?></p>
                <p class="text-xs text-gray-500"><?php echo e($activity->due_at?->locale('ar')->translatedFormat('d M — H:i')); ?></p>
            </div>
            <a href="<?php echo e(route('marketing.activities.index')); ?>" class="text-xs font-bold" style="color: <?php echo e($themeColor); ?>;">عرض</a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="p-5 text-sm text-gray-500">لا مهام قادمة.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\dashboard-rep.blade.php ENDPATH**/ ?>
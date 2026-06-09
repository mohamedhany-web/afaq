
<?php $__env->startSection('page-title', 'لوحة التسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $k = $kpis; ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $isManager ? 'لوحة مدير التسويق' : 'لوحة التسويق',
    'subtitle' => $role . ' — ' . now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />',
    'actionUrl' => route('marketing.reports.index'),
    'actionLabel' => 'التقارير الدورية',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(!empty($reportPending) && count($reportPending)): ?>
<div class="mb-6 p-5 rounded-2xl border-2 border-red-200 bg-red-50 font-tajawal">
    <p class="font-bold text-red-900 mb-2">تقارير إلزامية مطلوبة</p>
    <p class="text-sm text-red-800 mb-3">يجب رفع التقارير التالية قبل نهاية اليوم/الأسبوع/الشهر.</p>
    <a href="<?php echo e(route('marketing.reports.index')); ?>" class="inline-flex px-5 py-2 rounded-xl text-white text-sm font-bold" style="background:#7c3aed">رفع التقارير الآن</a>
</div>
<?php endif; ?>

<?php if($isManager && !empty($teamDailyStatus)): ?>
<?php $missingTeam = collect($teamDailyStatus)->where('submitted', false)->count(); ?>
<?php if($missingTeam > 0): ?>
<div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm font-tajawal">
    <strong><?php echo e($missingTeam); ?></strong> من فريق التسويق لم يرفعوا تقرير اليوم بعد.
    <a href="<?php echo e(route('marketing.reports.index', ['period' => 'daily'])); ?>" class="text-purple-700 font-bold mr-2">متابعة</a>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if($activePlan): ?>
<div class="mb-6 p-5 rounded-2xl border border-gray-200 bg-white shadow-lg font-tajawal">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-bold text-gray-500 mb-1">خطة الشهر النشطة</p>
            <p class="font-bold text-gray-900"><?php echo e($activePlan->title); ?></p>
            <p class="text-sm text-gray-600 mt-1"><?php echo e($activePlan->periodLabel()); ?> — <?php echo e($activePlan->completed_activities_count); ?>/<?php echo e($activePlan->activities_count); ?> مهمة مكتملة</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('marketing.plans.show', $activePlan)); ?>" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background:<?php echo e($themeColor); ?>">عرض الخطة</a>
            <a href="<?php echo e(route('marketing.activities.index', ['view' => 'month', 'marketing_plan_id' => $activePlan->id])); ?>" class="px-4 py-2 rounded-xl border text-sm font-semibold">جدول الشهر</a>
        </div>
    </div>
</div>
<?php elseif($isManager): ?>
<div class="mb-6 p-4 rounded-xl bg-purple-50 border border-purple-200 text-sm font-tajawal">
    لا توجد خطة تسويق نشطة لهذا الشهر.
    <a href="<?php echo e(route('marketing.plans.create')); ?>" class="font-bold text-purple-700 mr-2">إنشاء خطة</a>
</div>
<?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حملات نشطة', 'value' => $k['active_campaigns'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'Leads الشهر', 'value' => $k['leads_month'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهام اليوم', 'value' => $k['activities_today'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهام متأخرة', 'value' => $k['activities_overdue'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">أحدث الحملات</div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $recentCampaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('marketing.campaigns.show', $campaign)); ?>" class="block px-5 py-4 hover:bg-gray-50 font-tajawal">
                <div class="flex justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900"><?php echo e($campaign->name); ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($campaign->channelLabel()); ?> · <?php echo e($campaign->statusLabel()); ?></p>
                    </div>
                    <span class="text-sm font-bold" style="color: <?php echo e($themeColor); ?>;"><?php echo e($campaign->leads_count); ?> lead</span>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="p-5 text-sm text-gray-500 font-tajawal">لا توجد حملات بعد.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, transparent 100%);">مهام قادمة</div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $upcomingActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-5 py-4 font-tajawal">
                <p class="font-semibold text-gray-900"><?php echo e($activity->title); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($activity->due_at?->locale('ar')->translatedFormat('d M — H:i')); ?> · <?php echo e($activity->assignee?->name); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="p-5 text-sm text-gray-500 font-tajawal">لا مهام قادمة.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($overdueActivities->isNotEmpty()): ?>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 font-tajawal">
    <p class="font-bold text-amber-900 mb-2">مهام متأخرة (<?php echo e($overdueActivities->count()); ?>)</p>
    <ul class="space-y-1 text-sm text-amber-800">
        <?php $__currentLoopData = $overdueActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($activity->title); ?> — <?php echo e($activity->due_at?->diffForHumans()); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\dashboard.blade.php ENDPATH**/ ?>
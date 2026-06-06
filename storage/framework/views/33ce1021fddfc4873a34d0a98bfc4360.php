
<?php $__env->startSection('page-title', 'لوحة مدير المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $k = $kpis;
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $stageLabel = fn ($s) => \App\Services\SalesManagerDashboardService::FUNNEL_LABELS[$s] ?? $s;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'لوحة مدير المبيعات',
    'subtitle' => ($role ?? 'مدير المبيعات') . ' — ' . now()->locale('ar')->translatedFormat('l، d F Y') . ' · ' . $teams->count() . ' فريق',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
    'actionUrl' => route('crm.clients.index'),
    'actionLabel' => 'توزيع العملاء',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="mb-2">
    <h2 class="text-sm font-bold text-gray-500 font-tajawal px-1">مؤشرات تنفيذية</h2>
</div>
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-3 items-stretch">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إيرادات الفريق', 'value' => $money($k['team_revenue']), 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إيرادات الشهر', 'value' => $money($k['monthly_revenue']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء نشطون', 'value' => number_format($k['total_leads']), 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء مؤهلون', 'value' => number_format($k['qualified_leads']), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'فرص نشطة', 'value' => number_format($k['active_opportunities']), 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6 items-stretch">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معدل التحويل', 'value' => $k['conversion_rate'] . '%', 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صفقات الشهر', 'value' => $k['closed_deals_month'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متوسط الصفقة', 'value' => $money($k['avg_deal_value']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'تحقيق الهدف',
        'value' => $k['target_achievement'] . '%',
        'accent' => 'theme',
        'footer' => '<span class="text-gray-500">هدف الشهر: </span><span class="font-semibold text-gray-800">' . $money($k['team_target']) . '</span>',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>


<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    <div class="xl:col-span-8 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2" style="<?php echo e($headerStyle); ?>">
            <div>
                <h3 class="font-bold text-lg text-gray-900 font-tajawal">اتجاه الإيرادات والتوقعات</h3>
                <p class="text-xs text-gray-500 font-tajawal mt-0.5">خط فعلي + توقعات الأشهر القادمة</p>
            </div>
            <div class="flex gap-4 text-xs font-tajawal">
                <span class="text-gray-500">مسار متوقع: <strong class="text-gray-900"><?php echo e($money($forecasting['pipeline_value'])); ?></strong></span>
                <span class="text-gray-500">مرجّح الاحتمال: <strong style="color:<?php echo e($themeColor); ?>"><?php echo e($money($forecasting['weighted_forecast'])); ?></strong></span>
            </div>
        </div>
        <div class="p-4 sm:p-6 h-64 sm:h-72"><canvas id="revenueForecastChart"></canvas></div>
    </div>
    <div class="xl:col-span-4 grid grid-cols-1 gap-3">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-tajawal mb-1">قيمة المسار المتوقع</p>
            <p class="text-2xl font-bold text-gray-900 font-tajawal tabular-nums"><?php echo e($money($forecasting['pipeline_value'])); ?></p>
            <p class="text-xs text-gray-400 mt-2 font-tajawal">مجموع الفرص غير المغلقة — قرار تخصيص الموارد</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-tajawal mb-1">توقع مرجّح بالاحتمال</p>
            <p class="text-2xl font-bold font-tajawal tabular-nums" style="color:<?php echo e($themeColor); ?>"><?php echo e($money($forecasting['weighted_forecast'])); ?></p>
            <p class="text-xs text-gray-400 mt-2 font-tajawal">قيمة × نسبة الإغلاق — أولوية المتابعة</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-tajawal mb-1">متوسط إغلاق آخر 3 أشهر</p>
            <?php $avg3 = collect($forecasting['trend'])->take(-3)->avg('value'); ?>
            <p class="text-2xl font-bold text-gray-900 font-tajawal tabular-nums"><?php echo e($money($avg3)); ?></p>
        </div>
    </div>
</div>


<div class="mb-2 mt-2">
    <h2 class="text-sm font-bold text-gray-500 font-tajawal px-1">أداء الفريق</h2>
</div>
<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    <div class="xl:col-span-7 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200 flex justify-between items-center" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">أداء فريقي</h3>
            <?php $primaryTeam = $teams->first(); ?>
            <a href="<?php echo e($primaryTeam ? route('crm.teams.show', $primaryTeam) : route('crm.teams.create')); ?>" class="text-xs font-semibold font-tajawal" style="color:<?php echo e($themeColor); ?>"><?php echo e($primaryTeam ? 'فريقي' : 'إنشاء فريقي'); ?></a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm font-tajawal">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">الفريق</th>
                        <th class="text-right px-4 py-3">إيرادات الشهر</th>
                        <th class="text-right px-4 py-3">صفقات</th>
                        <th class="text-right px-4 py-3">تحويل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $teamPerformance['teams']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 tabular-nums text-gray-400"><?php echo e($i + 1); ?></td>
                        <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($team['name']); ?></td>
                        <td class="px-4 py-3 tabular-nums"><?php echo e($money($team['revenue'])); ?></td>
                        <td class="px-4 py-3 tabular-nums"><?php echo e($team['closed']); ?></td>
                        <td class="px-4 py-3 tabular-nums"><?php echo e($team['conversion']); ?>%</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">لا توجد فرق مُدارة</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="xl:col-span-5 grid grid-cols-1 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
                <h3 class="font-bold text-base text-gray-900 font-tajawal">إيرادات حسب الفريق</h3>
            </div>
            <div class="p-4 h-44"><canvas id="teamRevenueChart"></canvas></div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
                <h3 class="font-bold text-base text-gray-900 font-tajawal">صفقات مغلقة حسب الفريق</h3>
            </div>
            <div class="p-4 h-44"><canvas id="teamClosedChart"></canvas></div>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
        <h3 class="font-bold text-lg text-gray-900 font-tajawal">لوحة الشرف — أفضل 10 مندوبين (الشهر)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-gray-600 text-xs">
                <tr>
                    <th class="text-right px-4 py-3">الترتيب</th>
                    <th class="text-right px-4 py-3">المندوب</th>
                    <th class="text-right px-4 py-3">الإيرادات</th>
                    <th class="text-right px-4 py-3">صفقات</th>
                    <th class="text-right px-4 py-3">فرص جديدة</th>
                    <th class="text-right px-4 py-3">متابعات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__currentLoopData = $teamPerformance['leaderboard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <?php if($i < 3): ?>
                        <span class="inline-flex w-7 h-7 items-center justify-center rounded-full text-xs font-bold text-white" style="background:<?php echo e($themeColor); ?>"><?php echo e($i + 1); ?></span>
                        <?php else: ?>
                        <span class="text-gray-400 tabular-nums"><?php echo e($i + 1); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-semibold text-gray-900"><?php echo e($rep['name']); ?></span>
                        <span class="block text-xs text-gray-400"><?php echo e($rep['title']); ?></span>
                    </td>
                    <td class="px-4 py-3 font-semibold tabular-nums" style="color:<?php echo e($themeColor); ?>"><?php echo e($money($rep['revenue'])); ?></td>
                    <td class="px-4 py-3 tabular-nums"><?php echo e($rep['deals_closed']); ?></td>
                    <td class="px-4 py-3 tabular-nums"><?php echo e($rep['opportunities_created']); ?></td>
                    <td class="px-4 py-3 tabular-nums"><?php echo e($rep['follow_ups']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>


<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden xl:col-span-2">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">تتبع الأداء الفردي — الشهر الحالي</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm font-tajawal min-w-[720px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-right px-3 py-2">المندوب</th>
                        <th class="text-center px-2 py-2">اتصالات</th>
                        <th class="text-center px-2 py-2">اجتماعات</th>
                        <th class="text-center px-2 py-2">معاينات</th>
                        <th class="text-center px-2 py-2">متابعات</th>
                        <th class="text-center px-2 py-2">فرص</th>
                        <th class="text-center px-2 py-2">إغلاق</th>
                        <th class="text-center px-2 py-2">إيراد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $individualMetrics['reps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-semibold text-gray-900"><?php echo e($rep['name']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums"><?php echo e($rep['calls']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums"><?php echo e($rep['meetings']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums"><?php echo e($rep['property_visits']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums"><?php echo e($rep['follow_ups']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums"><?php echo e($rep['opportunities_created']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums"><?php echo e($rep['deals_closed']); ?></td>
                        <td class="text-center px-2 py-2 tabular-nums font-semibold"><?php echo e($money($rep['revenue'])); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="bg-gray-50 font-bold">
                        <td class="px-3 py-2">إجمالي الفريق</td>
                        <td class="text-center px-2 py-2"><?php echo e($individualMetrics['totals']['calls']); ?></td>
                        <td class="text-center px-2 py-2"><?php echo e($individualMetrics['totals']['meetings']); ?></td>
                        <td class="text-center px-2 py-2"><?php echo e($individualMetrics['totals']['property_visits']); ?></td>
                        <td class="text-center px-2 py-2"><?php echo e($individualMetrics['totals']['follow_ups']); ?></td>
                        <td class="text-center px-2 py-2"><?php echo e($individualMetrics['totals']['opportunities_created']); ?></td>
                        <td class="text-center px-2 py-2"><?php echo e($individualMetrics['totals']['deals_closed']); ?></td>
                        <td class="text-center px-2 py-2"><?php echo e($money($individualMetrics['totals']['revenue'])); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">قمع المبيعات</h3>
        </div>
        <div class="p-4 sm:p-6 space-y-3">
            <?php $__currentLoopData = $funnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <div class="flex justify-between text-xs sm:text-sm mb-1 font-tajawal">
                    <span class="font-semibold text-gray-800"><?php echo e($step['label']); ?></span>
                    <span class="text-gray-500 tabular-nums"><?php echo e($step['count']); ?></span>
                </div>
                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500" style="width: <?php echo e(max($step['percent'], 4)); ?>%; background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>cc 100%);"></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-3 sm:py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">معدل التحويل حسب الفريق</h3>
        </div>
        <div class="p-4 sm:p-6 h-64"><canvas id="teamConversionChart"></canvas></div>
    </div>
</div>


<div class="mb-2">
    <h2 class="text-sm font-bold text-gray-500 font-tajawal px-1">مركز توزيع العملاء</h2>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غير مُعيَّنين', 'value' => $leadDistribution['unassigned_count'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نسبة التعيين', 'value' => $leadDistribution['assigned_pct'] . '%', 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متوسط الاستجابة', 'value' => $leadDistribution['response_hours'] . ' س', 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متابعات متأخرة', 'value' => $leadDistribution['overdue_count'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    <div class="xl:col-span-5 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 flex justify-between" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-base text-gray-900 font-tajawal">عملاء غير مُعيَّنين</h3>
            <a href="<?php echo e(route('crm.clients.index', ['filter' => 'unassigned'])); ?>" class="text-xs font-semibold" style="color:<?php echo e($themeColor); ?>">عرض الكل</a>
        </div>
        <ul class="divide-y divide-gray-100 p-2">
            <?php $__empty_1 = true; $__currentLoopData = $leadDistribution['unassigned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="px-3 py-2.5 flex justify-between items-center text-sm font-tajawal hover:bg-gray-50 rounded-lg">
                <div>
                    <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="font-semibold text-gray-900 hover:underline"><?php echo e($client->name); ?></a>
                    <span class="block text-xs text-gray-400"><?php echo e($client->phone ?? '—'); ?></span>
                </div>
                <span class="text-xs text-gray-400"><?php echo e($client->created_at->diffForHumans()); ?></span>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="px-4 py-6 text-center text-gray-400 text-sm">لا يوجد — جيد للتوزيع السريع</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="xl:col-span-4 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-base text-gray-900 font-tajawal">عملاء لكل مندوب</h3>
        </div>
        <div class="p-4 h-56"><canvas id="leadsPerRepChart"></canvas></div>
    </div>
    <div class="xl:col-span-3 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-base text-gray-900 font-tajawal">متابعات متأخرة</h3>
        </div>
        <ul class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $leadDistribution['overdue_follow_ups']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="px-4 py-2.5 text-xs font-tajawal">
                <a href="<?php echo e(route('crm.pipeline.show', $sale)); ?>" class="font-semibold text-gray-900"><?php echo e($sale->client?->name ?? '—'); ?></a>
                <span class="block text-gray-400"><?php echo e($sale->salesRep?->name); ?> · <?php echo e($stageLabel($sale->stage)); ?></span>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="px-4 py-6 text-center text-gray-400">لا متأخرات</li>
            <?php endif; ?>
        </ul>
    </div>
</div>


<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    <div class="xl:col-span-4 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">نشاط الفريق</h3>
        </div>
        <ul class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            <?php $__currentLoopData = $activityFeed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="px-4 py-3 text-sm font-tajawal">
                <p class="font-semibold text-gray-800 leading-snug"><?php echo e($item['title']); ?></p>
                <p class="text-xs text-gray-400 mt-0.5"><?php echo e($item['meta']); ?> · <?php echo e($item['time']->diffForHumans()); ?></p>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div class="xl:col-span-4 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">تنبيهات الأداء</h3>
        </div>
        <div class="p-4 space-y-4 max-h-96 overflow-y-auto">
            <?php if($alerts['underperforming']->isNotEmpty()): ?>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                <p class="text-xs font-bold text-amber-800 mb-2 font-tajawal">مندوبون دون المتوسط</p>
                <ul class="text-xs text-amber-900 space-y-1">
                    <?php $__currentLoopData = $alerts['underperforming']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($r['name']); ?> — <?php echo e($money($r['revenue'])); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <?php if($alerts['missed_follow_ups'] > 0): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-3">
                <p class="text-sm font-bold text-red-800 font-tajawal"><?php echo e($alerts['missed_follow_ups']); ?> متابعة فائتة</p>
                <p class="text-xs text-red-700 mt-1">صفقات بلا نشاط منذ 3+ أيام</p>
            </div>
            <?php endif; ?>
            <?php if($alerts['stagnant']->isNotEmpty()): ?>
            <div>
                <p class="text-xs font-bold text-gray-600 mb-2 font-tajawal">فرص راكدة (7+ أيام)</p>
                <?php $__currentLoopData = $alerts['stagnant']->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('crm.pipeline.show', $s)); ?>" class="block text-xs py-1.5 text-gray-800 hover:underline font-tajawal"><?php echo e($s->client?->name); ?> — <?php echo e($money($s->estimated_value)); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
            <?php if($alerts['at_risk']->isNotEmpty()): ?>
            <div>
                <p class="text-xs font-bold text-gray-600 mb-2 font-tajawal">صفقات معرّضة للخسارة</p>
                <?php $__currentLoopData = $alerts['at_risk']->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('crm.pipeline.show', $s)); ?>" class="block text-xs py-1.5 font-tajawal" style="color:<?php echo e($themeColor); ?>"><?php echo e($s->client?->name); ?> (<?php echo e($s->probability_percentage ?? 0); ?>%)</a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
            <?php if($alerts['underperforming']->isEmpty() && $alerts['missed_follow_ups'] === 0 && $alerts['stagnant']->isEmpty() && $alerts['at_risk']->isEmpty()): ?>
            <p class="text-sm text-gray-400 text-center py-8 font-tajawal">لا تنبيهات حرجة — الفريق مستقر</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="xl:col-span-4 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">رؤى ذكية</h3>
        </div>
        <div class="p-4 space-y-4 text-sm font-tajawal">
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2">أفضل مصادر العملاء</p>
                <?php $__empty_1 = true; $__currentLoopData = $aiInsights['best_sources']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex justify-between py-1 text-xs">
                    <span><?php echo e($src['source']); ?></span>
                    <span class="font-semibold tabular-nums"><?php echo e($src['count']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-xs text-gray-400">لا بيانات كافية</p>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2">احتمال إغلاق مرتفع</p>
                <?php $__currentLoopData = $aiInsights['high_probability']->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('crm.pipeline.show', $deal)); ?>" class="block text-xs py-1 hover:underline">
                    <?php echo e($deal->client?->name); ?> — <?php echo e($deal->probability_percentage); ?>%
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="rounded-xl p-3" style="background:<?php echo e($themeColor); ?>08;border:1px solid <?php echo e($themeColor); ?>22">
                <p class="text-xs font-bold mb-2" style="color:<?php echo e($themeColor); ?>">إجراءات موصى بها</p>
                <ul class="text-xs text-gray-700 space-y-1.5 list-disc list-inside">
                    <?php $__currentLoopData = $aiInsights['actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($action); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <p class="text-xs text-gray-500">إنتاجية الفريق: <strong class="text-gray-800"><?php echo e($aiInsights['productive_reps']); ?>/<?php echo e($aiInsights['total_reps']); ?></strong> مندوبين نشطين (<?php echo e($aiInsights['productivity_pct']); ?>%)</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const theme = <?php echo json_encode($themeColor, 15, 512) ?>;
    const chartFont = { family: 'Tajawal', size: 11 };
    const charts = <?php echo json_encode($charts, 15, 512) ?>;
    const forecasting = <?php echo json_encode($forecasting, 15, 512) ?>;

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { font: chartFont } } },
        scales: {
            x: { ticks: { font: chartFont }, grid: { display: false } },
            y: { ticks: { font: chartFont }, grid: { color: 'rgba(0,0,0,0.06)' } },
        },
    };

    const rfEl = document.getElementById('revenueForecastChart');
    if (rfEl) {
        const actual = charts.revenue_trend || [];
        const forecast = charts.forecast || [];
        const labels = actual.map(r => r.label).concat(forecast.map(r => r.label));
        new Chart(rfEl, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'فعلي',
                        data: actual.map(r => r.value).concat(forecast.map(() => null)),
                        borderColor: theme,
                        backgroundColor: theme + '22',
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'توقع',
                        data: actual.map(() => null).concat(forecast.map(r => r.value)),
                        borderColor: '#d97706',
                        borderDash: [6, 4],
                        tension: 0.35,
                    },
                ],
            },
            options: baseOpts,
        });
    }

    function barChart(id, data, label) {
        const el = document.getElementById(id);
        if (!el || !data.length) return;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{ label, data: data.map(d => d.value), backgroundColor: theme }],
            },
            options: { ...baseOpts, plugins: { legend: { display: false } } },
        });
    }

    barChart('teamRevenueChart', charts.team_revenue, 'إيرادات');
    barChart('teamClosedChart', charts.team_closed, 'صفقات');

    const convEl = document.getElementById('teamConversionChart');
    const teams = <?php echo json_encode($teamPerformance['teams'], 15, 512) ?>;
    if (convEl && teams.length) {
        new Chart(convEl, {
            type: 'bar',
            data: {
                labels: teams.map(t => t.name),
                datasets: [{ data: teams.map(t => t.conversion), backgroundColor: theme + 'cc' }],
            },
            options: { ...baseOpts, plugins: { legend: { display: false } }, indexAxis: 'y' },
        });
    }

    barChart('leadsPerRepChart', charts.leads_per_rep.map(r => ({ label: r.name, value: r.count })), 'عملاء');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/dashboard-manager.blade.php ENDPATH**/ ?>
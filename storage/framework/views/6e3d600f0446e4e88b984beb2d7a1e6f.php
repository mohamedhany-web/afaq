
<?php $__env->startSection('page-title', 'لوحتي — مندوب المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $k = $kpis;
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'لوحة مندوب المبيعات',
    'subtitle' => ($user->name ?? '') . ' — ' . now()->locale('ar')->translatedFormat('l، d F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="rounded-2xl shadow-lg border overflow-hidden mb-6" style="border-color:<?php echo e($themeColor); ?>33">
    <div class="px-5 sm:px-6 py-4 sm:py-5" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>12 0%, <?php echo e($themeColor); ?>05 100%);">
        <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
            <div class="flex-1">
                <p class="text-xs font-bold uppercase tracking-wide mb-1 font-tajawal" style="color:<?php echo e($themeColor); ?>">الخطوة التالية</p>
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 font-tajawal leading-snug"><?php echo e($assistant['next_action']); ?></h2>
                <p class="text-sm text-gray-600 mt-3 font-tajawal bg-white/70 rounded-xl p-3 border border-gray-100">
                    <span class="font-semibold text-gray-800">رسالة مقترحة:</span> <?php echo e($assistant['follow_up_message']); ?>

                </p>
            </div>
            <div class="flex flex-wrap gap-3 lg:flex-col lg:items-end shrink-0">
                <div class="bg-white rounded-xl px-4 py-3 shadow border border-gray-100 text-center min-w-[120px]">
                    <p class="text-xs text-gray-500 font-tajawal">درجة الإغلاق</p>
                    <p class="text-3xl font-bold tabular-nums font-tajawal" style="color:<?php echo e($themeColor); ?>"><?php echo e($assistant['closing_score']); ?>%</p>
                </div>
                <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md font-tajawal"
                   style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                    فتح المسار
                </a>
            </div>
        </div>
    </div>
</div>


<div class="mb-2">
    <h2 class="text-sm font-bold text-gray-500 font-tajawal px-1">مؤشراتي اليوم</h2>
</div>
<?php
    $kpiRow1 = [
        ['label' => 'عملائي', 'value' => number_format($k['assigned_leads']), 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />', 'href' => auth()->user()->clientsHubUrl(), 'linkLabel' => 'مسار المبيعات'],
        ['label' => 'جدد اليوم', 'value' => $k['new_leads_today'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />', 'href' => auth()->user()->clientsHubUrl(), 'linkLabel' => 'مسار المبيعات'],
        ['label' => 'متابعات اليوم', 'value' => $k['follow_ups_due_today'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.follow-ups.index'), 'linkLabel' => 'عرض المتابعات'],
        ['label' => 'فرص نشطة', 'value' => $k['active_opportunities'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض الصفقات'],
    ];
    $kpiRow2 = [
        ['label' => 'إغلاق الشهر', 'value' => $k['closed_deals_month'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الصفقات'],
        ['label' => 'إيرادي', 'value' => $money($k['personal_revenue']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8" />', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الإيرادات'],
        ['label' => 'التحويل', 'value' => $k['conversion_rate'] . '%', 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />', 'href' => route('crm.intelligence.index'), 'linkLabel' => 'عرض التحليلات'],
        ['label' => 'الهدف', 'value' => $k['target_achievement'] . '%', 'accent' => 'theme', 'footer' => '<span class="text-gray-500">هدف: </span>' . $money($k['monthly_target']), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />', 'href' => route('crm.compensation.dashboard'), 'linkLabel' => 'عرض الهدف'],
    ];
?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-4 sm:mb-5 items-stretch">
    <?php $__currentLoopData = $kpiRow1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('crm.partials.stat-card', $card, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6 sm:mb-8 items-stretch">
    <?php $__currentLoopData = $kpiRow2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('crm.partials.stat-card', $card, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    
    <div class="xl:col-span-7 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 flex justify-between items-center" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">مهامي</h3>
            <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="text-xs font-semibold font-tajawal" style="color:<?php echo e($themeColor); ?>">المسار</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-0 sm:gap-4 p-4 sm:p-5">
            <?php $__currentLoopData = [
                ['title' => 'مهام اليوم', 'items' => $tasks['today'], 'empty' => 'لا مهام محددة اليوم'],
                ['title' => 'اجتماعات قادمة', 'items' => $tasks['meetings'], 'empty' => 'لا اجتماعات'],
                ['title' => 'معاينات مجدولة', 'items' => $tasks['visits'], 'empty' => 'لا معاينات'],
                ['title' => 'متابعات معلّقة', 'items' => $tasks['follow_ups'], 'empty' => 'ممتاز — لا تأخير'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 min-h-[140px]">
                <h4 class="text-sm font-bold text-gray-700 mb-3 font-tajawal"><?php echo e($block['title']); ?></h4>
                <?php $__empty_1 = true; $__currentLoopData = $block['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($item['url']); ?>" class="block p-2.5 mb-2 rounded-lg bg-white hover:bg-gray-50 border border-gray-100 text-sm font-tajawal shadow-sm">
                    <span class="font-semibold text-gray-900"><?php echo e($item['title']); ?></span>
                    <span class="block text-gray-400"><?php echo e($item['meta']); ?> · <?php echo e($item['date']); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-gray-400 font-tajawal"><?php echo e($block['empty']); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($tasks['contracts']->isNotEmpty()): ?>
        <div class="px-4 pb-4 border-t border-gray-100 pt-3">
            <h4 class="text-xs font-bold text-gray-500 mb-2 font-tajawal">متابعة عقود</h4>
            <?php $__currentLoopData = $tasks['contracts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($c['url']); ?>" class="flex justify-between text-xs py-1.5 font-tajawal hover:underline">
                <span><?php echo e($c['title']); ?> — <?php echo e($c['client']); ?></span>
                <span class="text-gray-400"><?php echo e($c['date']); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="xl:col-span-5 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">تقدّم الأداء</h3>
        </div>
        <div class="p-5 sm:p-6 space-y-5">
            <div>
                <div class="flex justify-between text-sm font-tajawal mb-2">
                    <span class="text-gray-600">هدف الشهر</span>
                    <span class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($progress['target_achievement']); ?>%</span>
                </div>
                <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full" style="width:<?php echo e(min($progress['target_achievement'], 100)); ?>%;background:<?php echo e($themeColor); ?>"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1 font-tajawal"><?php echo e($money($progress['personal_revenue'])); ?> من <?php echo e($money($progress['monthly_target'])); ?></p>
            </div>
            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100">
                <span class="text-sm font-semibold text-gray-800 font-tajawal">إنتاجية اليوم</span>
                <span class="text-3xl font-bold tabular-nums font-tajawal" style="color:<?php echo e($themeColor); ?>"><?php echo e($progress['productivity_score']); ?>%</span>
            </div>
            <div class="h-44 sm:h-48"><canvas id="revenueProgressChart"></canvas></div>
            <div class="h-36 sm:h-40"><canvas id="conversionTrendChart"></canvas></div>
        </div>
    </div>
</div>


<div class="mb-3">
    <h2 class="text-sm font-bold text-gray-500 font-tajawal px-1">عملائي حسب الحرارة</h2>
</div>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5 mb-6">
    <?php $__currentLoopData = [
        'urgent' => ['عاجل', '#ef4444'],
        'new' => ['جدد', '#2563eb'],
        'hot' => ['ساخن', '#d97706'],
        'warm' => ['دافئ', '#3b82f6'],
        'cold' => ['بارد', '#9333ea'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 text-center hover:shadow-xl transition-shadow min-h-[100px] flex flex-col justify-center">
        <p class="text-3xl sm:text-4xl font-bold tabular-nums font-tajawal mb-2" style="color:<?php echo e($color); ?>"><?php echo e($leads['counts'][$key]); ?></p>
        <p class="text-sm text-gray-600 font-tajawal font-semibold"><?php echo e($label); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['key' => 'urgent', 'title' => 'يتطلب إجراء فوري', 'border' => '#ef4444'],
        ['key' => 'hot', 'title' => 'عملاء ساخنون', 'border' => $themeColor],
        ['key' => 'new', 'title' => 'عملاء جدد', 'border' => '#2563eb'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b-2 font-tajawal font-bold text-sm text-gray-800" style="border-color:<?php echo e($col['border']); ?>"><?php echo e($col['title']); ?></div>
        <ul class="divide-y divide-gray-50 max-h-48 overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $leads[$col['key']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li>
                <a href="<?php echo e($lead['url']); ?>" class="flex justify-between px-4 py-2.5 text-sm font-tajawal hover:bg-gray-50">
                    <span class="font-semibold text-gray-900"><?php echo e($lead['name']); ?></span>
                    <span class="text-xs text-gray-400"><?php echo e($lead['phone'] ?? '—'); ?></span>
                </a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="px-4 py-6 text-xs text-gray-400 text-center">—</li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6 mb-6">
    
    <div class="xl:col-span-5 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg text-gray-900 font-tajawal">مساري — مراحل الصفقة</h3>
        </div>
        <div class="p-4 space-y-2.5">
            <?php $__currentLoopData = $pipeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($step['url']); ?>" class="block group">
                <div class="flex justify-between text-xs mb-1 font-tajawal">
                    <span class="font-semibold text-gray-800 group-hover:underline"><?php echo e($step['label']); ?></span>
                    <span class="text-gray-500 tabular-nums"><?php echo e($step['count']); ?></span>
                </div>
                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all" style="width:<?php echo e(max($step['percent'], 4)); ?>%;background:<?php echo e($themeColor); ?>"></div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="xl:col-span-3 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-base text-gray-900 font-tajawal">نشاطي اليوم</h3>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <?php $__currentLoopData = [
                ['label' => 'اتصالات', 'val' => $dailyActivity['calls']],
                ['label' => 'واتساب', 'val' => $dailyActivity['whatsapp']],
                ['label' => 'بريد', 'val' => $dailyActivity['emails']],
                ['label' => 'اجتماعات', 'val' => $dailyActivity['meetings']],
                ['label' => 'معاينات', 'val' => $dailyActivity['tours']],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="text-center p-4 sm:p-5 rounded-xl bg-gray-50 border border-gray-100 min-h-[88px] flex flex-col justify-center">
                <p class="text-2xl sm:text-3xl font-bold tabular-nums font-tajawal mb-1" style="color:<?php echo e($themeColor); ?>"><?php echo e($act['val']); ?></p>
                <p class="text-sm text-gray-600 font-tajawal"><?php echo e($act['label']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="xl:col-span-4 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-base text-gray-900 font-tajawal">أولوية الإغلاق</h3>
        </div>
        <ul class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $assistant['high_priority']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li>
                <a href="<?php echo e(route('crm.pipeline.show', $deal)); ?>" class="block px-4 py-3 hover:bg-gray-50 font-tajawal text-sm">
                    <span class="font-semibold text-gray-900"><?php echo e($deal->client?->name ?? '—'); ?></span>
                    <span class="flex justify-between text-xs text-gray-500 mt-0.5">
                        <span><?php echo e($money($deal->estimated_value)); ?></span>
                        <span class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($deal->probability_percentage ?? 0); ?>%</span>
                    </span>
                </a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="px-4 py-8 text-center text-gray-400 text-sm">أضف فرصاً في المسار</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php if(auth()->user()?->can('view-all-projects') || auth()->user()?->can('view-own-projects')): ?>

<div class="mb-2"><h2 class="text-sm font-bold text-gray-500 font-tajawal px-1">عقارات للعرض</h2></div>
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['title' => 'موصى بها لعملائي', 'items' => $properties['recommended']],
        ['title' => 'شاهدت مؤخراً', 'items' => $properties['recently_viewed']],
        ['title' => 'الأكثر طلباً', 'items' => $properties['frequent']],
        ['title' => 'وحدات متاحة', 'items' => $properties['available']],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 text-sm font-bold text-gray-800 font-tajawal" style="<?php echo e($headerStyle); ?>"><?php echo e($section['title']); ?></div>
        <ul class="p-3 space-y-2 max-h-52 overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li>
                <a href="<?php echo e(route('crm.projects.show', $p)); ?>" class="block p-2 rounded-lg hover:bg-gray-50 text-xs font-tajawal border border-gray-50">
                    <span class="font-semibold text-gray-900"><?php echo e($p->name); ?></span>
                    <span class="block text-gray-500"><?php echo e($p->city ?? $p->location ?? '—'); ?>

                        <?php if($p->available_units): ?> · <?php echo e($p->available_units); ?> وحدة <?php endif; ?>
                        <?php if($p->price_from): ?> · من <?php echo e($money($p->price_from)); ?> <?php endif; ?>
                    </span>
                </a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="text-xs text-gray-400 text-center py-4">لا مشاريع بعد</li>
            <?php endif; ?>
        </ul>
        <div class="px-3 pb-3">
            <a href="<?php echo e(route('crm.projects.index')); ?>" class="text-xs font-semibold font-tajawal" style="color:<?php echo e($themeColor); ?>">كل المشاريع</a>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const theme = <?php echo json_encode($themeColor, 15, 512) ?>;
    const chartFont = { family: 'Tajawal', size: 10 };
    const charts = <?php echo json_encode($charts, 15, 512) ?>;
    const baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { font: chartFont }, grid: { display: false } },
            y: { ticks: { font: chartFont }, grid: { color: 'rgba(0,0,0,0.06)' } },
        },
    };
    const revEl = document.getElementById('revenueProgressChart');
    if (revEl && charts.revenue_trend?.length) {
        new Chart(revEl, {
            type: 'bar',
            data: {
                labels: charts.revenue_trend.map(r => r.label),
                datasets: [{ data: charts.revenue_trend.map(r => r.value), backgroundColor: theme }],
            },
            options: baseOpts,
        });
    }
    const convEl = document.getElementById('conversionTrendChart');
    if (convEl && charts.conversion_trend?.length) {
        new Chart(convEl, {
            type: 'line',
            data: {
                labels: charts.conversion_trend.map(r => r.label),
                datasets: [{ data: charts.conversion_trend.map(r => r.value), borderColor: theme, tension: 0.35 }],
            },
            options: { ...baseOpts, scales: { x: { display: true }, y: { max: 100 } } },
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\dashboard-rep.blade.php ENDPATH**/ ?>
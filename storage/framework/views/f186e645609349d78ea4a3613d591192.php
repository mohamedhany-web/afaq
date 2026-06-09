
<?php $__env->startSection('page-title', 'تحليلات أداء المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تحليلات أداء المبيعات',
    'subtitle' => 'معدلات التحويل · أسباب خسارة الصفقات · أداء الفريق · توقعات الإيرادات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form method="GET" class="mb-6 flex flex-wrap gap-3 items-end bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">من</label>
        <input type="date" name="from" value="<?php echo e($filters['from']); ?>" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">إلى</label>
        <input type="date" name="to" value="<?php echo e($filters['to']); ?>" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-sm">
    </div>
    <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal" style="background: <?php echo e($themeColor); ?>;">تطبيق</button>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معدل التحويل', 'value' => $funnel['conversion']['lead_to_won'] . '%', 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معدل الإغلاق', 'value' => $funnel['conversion']['deal_close_rate'] . '%', 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صفقات خاسرة', 'value' => $funnel['lost_breakdown']['total_lost'], 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'توقع الشهر القادم', 'value' => $money($forecast['forecast'][0]['revenue_forecast'] ?? 0), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">مسار العملاء (Lead Funnel)</div>
        <div class="p-5 space-y-3">
            <?php $__currentLoopData = $funnel['client_funnel']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex justify-between text-sm font-tajawal mb-1">
                        <span class="text-gray-700"><?php echo e($step['label']); ?></span>
                        <span class="font-bold" style="color: <?php echo e($themeColor); ?>;"><?php echo e($step['count']); ?></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: <?php echo e($step['percent']); ?>%; background: <?php echo e($themeColor); ?>;"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">توزيع أسباب الخسارة</div>
        <div class="p-5 space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $funnel['lost_breakdown']['reasons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div>
                    <div class="flex justify-between text-sm font-tajawal mb-1">
                        <span class="text-gray-700"><?php echo e($reason['label']); ?></span>
                        <span class="font-bold text-red-600"><?php echo e($reason['count']); ?> <span class="text-gray-400 font-normal">(<?php echo e($reason['share']); ?>%)</span></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-red-400" style="width: <?php echo e($reason['percent']); ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-400 text-sm text-center py-6 font-tajawal">لا توجد خسائر مسجّلة بالسبب في هذه الفترة</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">ذكاء إدارة المبيعات — لكل فريق ومدير</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="text-right px-4 py-3">الفريق</th>
                    <th class="text-right px-4 py-3">المدير</th>
                    <th class="text-right px-4 py-3">سرعة الرد (ساعة)</th>
                    <th class="text-right px-4 py-3">متابعة %</th>
                    <th class="text-right px-4 py-3">معاينات</th>
                    <th class="text-right px-4 py-3">إغلاق %</th>
                    <th class="text-right px-4 py-3">قيمة المسار</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $management['teams']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-semibold text-gray-900"><?php echo e($team['team_name']); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($team['manager_name']); ?></td>
                        <td class="px-4 py-3"><?php echo e($team['avg_response_hours']); ?></td>
                        <td class="px-4 py-3"><?php echo e($team['follow_up_rate']); ?>%</td>
                        <td class="px-4 py-3"><?php echo e($team['viewings_month']); ?></td>
                        <td class="px-4 py-3"><?php echo e($team['close_rate']); ?>%</td>
                        <td class="px-4 py-3 font-bold" style="color: <?php echo e($themeColor); ?>;"><?php echo e($money($team['pipeline_value'])); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">لا توجد فرق مبيعات</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">محرك التنبؤ — ماذا سيحدث</div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500 font-tajawal">قيمة المسار الحالي</p>
                    <p class="text-lg font-bold font-tajawal" style="color: <?php echo e($themeColor); ?>;"><?php echo e($money($forecast['pipeline_value'])); ?></p>
                </div>
                <div class="p-4 rounded-xl bg-gray-50">
                    <p class="text-xs text-gray-500 font-tajawal">توقع مرجّح (احتمالية)</p>
                    <p class="text-lg font-bold font-tajawal text-green-600"><?php echo e($money($forecast['weighted_forecast'])); ?></p>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">توقعات الأشهر القادمة</p>
                <?php $__currentLoopData = $forecast['forecast']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between py-2 border-b border-gray-50 text-sm font-tajawal">
                        <span><?php echo e($month['label']); ?></span>
                        <span class="font-bold"><?php echo e($money($month['revenue_forecast'])); ?> · <?php echo e($month['deals_forecast']); ?> صفقة</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">تحصيلات متوقعة (30 يوم)</p>
                <p class="text-lg font-bold text-blue-600 font-tajawal"><?php echo e($money($forecast['upcoming_collections'])); ?></p>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">مشاريع وصفقات معرّضة للخطر</div>
        <div class="p-5 space-y-3 max-h-96 overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $forecast['at_risk_deals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('crm.pipeline.show', $deal['id'])); ?>" class="block p-3 rounded-xl border border-amber-100 bg-amber-50/50 hover:bg-amber-50 transition">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-gray-900 text-sm font-tajawal"><?php echo e($deal['client'] ?? '—'); ?></span>
                        <span class="text-sm font-bold text-amber-700 font-tajawal"><?php echo e($money($deal['value'])); ?></span>
                    </div>
                    <p class="text-xs text-amber-700 mt-1 font-tajawal"><?php echo e($deal['reason']); ?></p>
                    <?php if($deal['project']): ?>
                        <p class="text-xs text-gray-500 mt-0.5 font-tajawal"><?php echo e($deal['project']); ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-400 text-sm text-center py-6 font-tajawal">لا توجد صفقات معرّضة للخطر حالياً</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <span class="font-bold font-tajawal text-gray-900">ما بعد البيع — شكاوى وصيانة وتسليم</span>
        <div class="flex gap-2 text-xs font-tajawal">
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800"><?php echo e($postSales['open']); ?> مفتوح</span>
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800"><?php echo e($postSales['resolved_month']); ?> حُلّ هذا الشهر</span>
        </div>
    </div>
    <div class="p-5">
        <?php $__empty_1 = true; $__currentLoopData = $postSales['recent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <a href="<?php echo e(route('crm.clients.show', $case->client_id)); ?>" class="font-semibold text-sm font-tajawal hover:underline" style="color: <?php echo e($themeColor); ?>;"><?php echo e($case->client?->name); ?></a>
                    <p class="text-sm text-gray-700 font-tajawal"><?php echo e($case->title); ?></p>
                    <p class="text-xs text-gray-400 font-tajawal"><?php echo e($case->typeLabel()); ?> · <?php echo e($case->statusLabel()); ?></p>
                </div>
                <span class="text-xs text-gray-400 font-tajawal"><?php echo e($case->created_at->format('Y/m/d')); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-400 text-sm text-center py-6 font-tajawal">لا توجد حالات ما بعد البيع — جاهز لتسجيل الشكاوى والصيانة</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\intelligence\index.blade.php ENDPATH**/ ?>
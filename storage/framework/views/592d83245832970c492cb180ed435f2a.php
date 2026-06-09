
<?php $__env->startSection('page-title', $member->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $member->name,
    'subtitle' => 'ملف مندوب المبيعات — نشاط اليوم والصفقات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الصفقات', 'value' => $stats['total_deals'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صفقات نشطة', 'value' => $stats['active_deals'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تم البيع', 'value' => $stats['won_deals'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قيمة المسار', 'value' => $money($stats['pipeline_value']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تحديثات اليوم', 'value' => $stats['today_updates'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 w-full">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            نشاط اليوم — صفقات محدّثة
        </div>
        <div class="p-5 space-y-3 max-h-[400px] overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $todayDeals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('crm.pipeline.show', $deal)); ?>" class="block p-3 rounded-xl border border-gray-100 hover:bg-gray-50">
                    <p class="font-semibold text-sm font-tajawal"><?php echo e($deal->client?->name); ?> — <?php echo e($deal->product_service); ?></p>
                    <p class="text-xs text-gray-500 mt-1 font-tajawal"><?php echo e($stageLabels[$deal->stage] ?? $deal->stage); ?> · <?php echo e($money($deal->estimated_value)); ?> · <?php echo e($deal->updated_at->format('H:i')); ?></p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-400 text-sm font-tajawal text-center py-8">لا تحديثات اليوم</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            عملاء المندوب
        </div>
        <div class="p-5 space-y-3 max-h-[400px] overflow-y-auto">
            <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="block p-3 rounded-xl border border-gray-100 hover:bg-gray-50">
                    <p class="font-semibold text-sm font-tajawal"><?php echo e($client->name); ?></p>
                    <p class="text-xs text-gray-500 mt-1 font-tajawal" dir="ltr"><?php echo e($client->phone); ?> · <?php echo e($client->sales_count); ?> صفقة</p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-400 text-sm font-tajawal text-center py-8">لا عملاء</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden w-full">
    <div class="px-5 py-4 border-b border-gray-200 font-bold font-tajawal text-gray-900">كل الصفقات</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr class="text-gray-600">
                <th class="text-right p-4 font-tajawal">العميل</th>
                <th class="text-right p-4 font-tajawal">الوصف</th>
                <th class="text-right p-4 font-tajawal">المرحلة</th>
                <th class="text-right p-4 font-tajawal">القيمة</th>
                <th class="text-right p-4 font-tajawal">آخر تحديث</th>
            </tr></thead>
            <tbody>
            <?php $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t border-gray-100 hover:bg-gray-50">
                    <td class="p-4"><a href="<?php echo e(route('crm.clients.show', $deal->client)); ?>" class="font-medium font-tajawal" style="color: <?php echo e($themeColor); ?>;"><?php echo e($deal->client?->name); ?></a></td>
                    <td class="p-4 font-tajawal text-gray-700"><?php echo e($deal->product_service); ?></td>
                    <td class="p-4"><span class="px-2 py-1 rounded-lg text-xs bg-gray-100 font-tajawal"><?php echo e($stageLabels[$deal->stage] ?? $deal->stage); ?></span></td>
                    <td class="p-4 font-semibold font-tajawal"><?php echo e($money($deal->estimated_value)); ?></td>
                    <td class="p-4 text-gray-500 text-xs font-tajawal"><?php echo e($deal->updated_at->diffForHumans()); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="<?php echo e(route('crm.dashboard')); ?>" class="inline-flex px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm font-tajawal hover:bg-gray-50">← العودة للوحة الفريق</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\team-members\show.blade.php ENDPATH**/ ?>
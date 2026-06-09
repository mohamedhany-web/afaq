
<?php $__env->startSection('page-title', 'مسار المبيعات — جدول'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'جدول العملاء والصفقات',
    'subtitle' => 'عرض تفصيلي لجميع العملاء مع صفقاتهم',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.pipeline.partials.view-switcher', ['current' => 'list'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 mb-4">
    <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <input type="hidden" name="view" value="list">
        <div class="flex-1">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                style="background: <?php echo e($themeColor); ?>;">بحث</button>
    </form>
</div>

<div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>06 0%, transparent 100%);">
            <div>
                <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="font-bold text-gray-900 font-tajawal hover:underline"><?php echo e($client->name); ?></a>
                <p class="text-sm text-gray-500 font-tajawal mt-0.5" dir="ltr"><?php echo e($client->phone); ?></p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <span class="text-xs px-2 py-0.5 rounded-lg font-semibold bg-gray-100 text-gray-700 font-tajawal">
                        <?php echo e($stageLabels[$client->lead_stage] ?? $client->lead_stage); ?>

                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white font-tajawal"
                   style="background: <?php echo e($themeColor); ?>;">+ صفقة</a>
                <a href="<?php echo e(route('crm.clients.show', $client)); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 font-tajawal hover:bg-gray-50">الملف الكامل</a>
            </div>
        </div>
        <?php if($client->sales->count()): ?>
        <div class="p-4 overflow-x-auto">
            <table class="w-full text-sm font-tajawal">
                <thead>
                    <tr class="text-xs text-gray-500 border-b">
                        <th class="text-right py-2">الصفقة</th>
                        <th class="text-right py-2">المشروع</th>
                        <th class="text-right py-2">المرحلة</th>
                        <th class="text-right py-2">القيمة</th>
                        <th class="text-right py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $client->sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-2 font-medium text-gray-900"><?php echo e($sale->product_service); ?></td>
                        <td class="py-2 text-gray-600"><?php echo e($sale->project?->name ?? '—'); ?></td>
                        <td class="py-2"><span class="text-xs px-2 py-0.5 rounded bg-gray-100"><?php echo e($stageLabels[$sale->stage] ?? $sale->stage); ?></span></td>
                        <td class="py-2 font-bold" style="color: <?php echo e($themeColor); ?>;"><?php echo e($money($sale->estimated_value)); ?></td>
                        <td class="py-2"><a href="<?php echo e(route('crm.pipeline.show', $sale)); ?>" class="text-xs font-semibold" style="color: <?php echo e($themeColor); ?>;">تفاصيل</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="p-4 text-sm text-gray-400 font-tajawal text-center">لا توجد صفقات — <a href="<?php echo e(route('crm.pipeline.create', ['client_id' => $client->id])); ?>" style="color: <?php echo e($themeColor); ?>;">أنشئ صفقة</a></p>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-16 text-gray-400 font-tajawal">لا يوجد عملاء مطابقون للفلاتر</div>
    <?php endif; ?>
</div>

<div class="mt-6"><?php echo e($clients->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\pipeline\list-clients.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'راتبي ومؤشرات الأداء'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $kpiBreakdown = $run->breakdown['kpi'] ?? [];
    $level = $kpiBreakdown['level']['label'] ?? '—';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'راتبي ومؤشرات الأداء',
    'subtitle' => $period->starts_at->locale('ar')->translatedFormat('F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الراتب الأساسي', 'value' => $money($run->base_salary), 'compact' => true, 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'العمولة', 'value' => $money($run->commission_total), 'compact' => true, 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'درجة KPI', 'value' => round($run->kpi_score ?? 0, 1) . '%', 'compact' => true, 'accent' => 'purple', 'footer' => '<span class="text-gray-600">المستوى: ' . e($level) . '</span>'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'المكافآت', 'value' => $money($run->bonus_total), 'compact' => true, 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الخصومات', 'value' => $money($run->deduction_total), 'compact' => true, 'accent' => 'red'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صافي الراتب المتوقع', 'value' => $money($run->net_pay), 'compact' => true, 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg font-tajawal">تفاصيل مؤشرات الأداء</h3>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full text-sm font-tajawal">
                <thead><tr class="text-gray-500 border-b"><th class="text-right py-2">المؤشر</th><th class="text-center py-2">الهدف</th><th class="text-center py-2">الفعلي</th><th class="text-center py-2">التحقق</th><th class="text-center py-2">الوزن</th></tr></thead>
                <tbody>
                <?php $__currentLoopData = $kpiBreakdown['items'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b border-gray-100">
                        <td class="py-2"><?php echo e($item['name']); ?></td>
                        <td class="text-center"><?php echo e($item['target']); ?></td>
                        <td class="text-center"><?php echo e($item['actual']); ?></td>
                        <td class="text-center font-semibold"><?php echo e(round($item['achievement'] ?? 0, 1)); ?>%</td>
                        <td class="text-center"><?php echo e($item['weight']); ?>%</td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h3 class="font-bold text-lg font-tajawal">اتجاه الأرباح الشهرية</h3>
        </div>
        <div class="p-4 sm:p-6 h-64"><canvas id="earningsTrend"></canvas></div>
    </div>
</div>

<div class="flex gap-3">
    <a href="<?php echo e(route('crm.compensation.payroll.show', $run)); ?>" class="inline-flex items-center px-4 py-2 rounded-xl text-white text-sm font-tajawal" style="background: <?php echo e($themeColor); ?>">كشف الراتب التفصيلي</a>
    <form method="POST" action="<?php echo e(route('crm.compensation.payroll.recalculate')); ?>"><?php echo csrf_field(); ?><button type="submit" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-tajawal">تحديث الحساب</button></form>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('earningsTrend');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($history->map(fn ($h) => $h->period?->month . '/' . $h->period?->year)->values(), 15, 512) ?>,
            datasets: [{ label: 'صافي الراتب', data: <?php echo json_encode($history->pluck('net_pay')->values(), 15, 512) ?>, borderColor: '<?php echo e($themeColor); ?>', tension: 0.3, fill: false }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\compensation\rep\dashboard.blade.php ENDPATH**/ ?>
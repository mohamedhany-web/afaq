
<?php $__env->startSection('page-title', 'انضباط الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $statusColors = ['green' => 'bg-green-100 text-green-800', 'blue' => 'bg-blue-100 text-blue-800', 'amber' => 'bg-amber-100 text-amber-800', 'red' => 'bg-red-100 text-red-800'];
?>

<?php echo $__env->make('crm.partials.page-header', array_filter([
    'title' => $mode === 'manager' ? 'انضباط الفريق والالتزام' : 'التزامي بالنظام',
    'subtitle' => 'التقارير · الحضور · الإجازات · المهام · العقوبات التلقائية',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'actionUrl' => auth()->user()?->hasRole(['super_admin', 'admin']) ? route('admin.auto-penalties.index') : null,
    'actionLabel' => auth()->user()?->hasRole(['super_admin', 'admin']) ? 'قواعد العقوبات' : null,
]), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($mode === 'manager'): ?>
<?php echo $__env->make('crm.partials.filter-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end font-tajawal">
        <div><label class="text-xs font-bold text-gray-500 mb-1 block">من</label><input type="date" name="from" value="<?php echo e($start->toDateString()); ?>" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"></div>
        <div><label class="text-xs font-bold text-gray-500 mb-1 block">إلى</label><input type="date" name="to" value="<?php echo e($end->toDateString()); ?>" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"></div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
    </form>
</div>
<?php endif; ?>

<?php if($mode === 'manager'): ?>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حجم الفريق', 'value' => $stats['team_size'], 'accent' => 'theme', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'ملتزمون', 'value' => $stats['excellent'], 'accent' => 'green', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'يحتاج متابعة', 'value' => $stats['critical'], 'accent' => 'amber', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عقوبات الشهر', 'value' => $money($stats['penalties_month']), 'accent' => 'red', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">ملخص الفريق</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px] font-tajawal">
            <thead class="bg-gray-50 border-b"><tr class="text-gray-600">
                <th class="text-right p-4">الموظف</th>
                <th class="text-center p-4">التقييم</th>
                <th class="text-center p-4">تقارير</th>
                <th class="text-center p-4">حضور</th>
                <th class="text-center p-4">إجازات</th>
                <th class="text-center p-4">مهام متأخرة</th>
                <th class="text-center p-4">عقوبات</th>
                <th class="text-center p-4"></th>
            </tr></thead>
            <tbody class="divide-y">
                <?php $__currentLoopData = $overview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50/80">
                    <td class="p-4 font-semibold"><?php echo e($row['name']); ?></td>
                    <td class="p-4 text-center">
                        <span class="text-xs px-2 py-1 rounded-full font-bold <?php echo e($statusColors[$row['status']['color']] ?? 'bg-gray-100'); ?>"><?php echo e($row['overall_score']); ?>% — <?php echo e($row['status']['label']); ?></span>
                    </td>
                    <td class="p-4 text-center tabular-nums"><?php echo e($row['reports']['percent']); ?>%</td>
                    <td class="p-4 text-center tabular-nums"><?php echo e($row['attendance_compliance']); ?>%</td>
                    <td class="p-4 text-center tabular-nums"><?php echo e($row['period']['leave_days']); ?></td>
                    <td class="p-4 text-center tabular-nums"><?php echo e($row['overdue_tasks'] + $row['overdue_follow_ups']); ?></td>
                    <td class="p-4 text-center tabular-nums"><?php echo e($money($row['penalties_total'])); ?></td>
                    <td class="p-4 text-center"><a href="<?php echo e(route('crm.employee-compliance.show', $row['user'])); ?>" class="text-xs font-bold" style="color:<?php echo e($themeColor); ?>">تفاصيل</a></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<?php $s = $self; ?>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التقييم الإجمالي', 'value' => $s['overall_score'].'%', 'accent' => $s['status']['color'] === 'green' ? 'green' : 'amber', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التقارير', 'value' => $s['reports']['submitted'].' / '.$s['reports']['expected'], 'accent' => 'blue', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الحضور', 'value' => $s['attendance_compliance'].'%', 'accent' => 'purple', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام إجازة', 'value' => $s['period']['leave_days'], 'accent' => 'theme', 'compact' => true, 'href' => route('crm.employee-compliance.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border p-5 font-tajawal text-sm">
        <h3 class="font-bold mb-3">ملاحظات الالتزام</h3>
        <?php $__empty_1 = true; $__currentLoopData = $s['flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="py-2 border-b text-amber-800">• <?php echo e($flag); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-gray-500">لا ملاحظات — أداء جيد</p>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-2xl border p-5 font-tajawal text-sm">
        <h3 class="font-bold mb-3">إجازات قادمة</h3>
        <?php $__empty_1 = true; $__currentLoopData = $leaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="py-2 border-b"><?php echo e($leave->leave_type_name); ?>: <?php echo e($leave->start_date->format('Y/m/d')); ?> — <?php echo e($leave->end_date->format('Y/m/d')); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-gray-500">لا إجازات معتمدة قادمة</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="mt-6 p-4 rounded-2xl border text-xs text-gray-500 font-tajawal" style="border-color:<?php echo e($themeColor); ?>25;background:<?php echo e($themeColor); ?>05;">
    <strong>كيف يعمل النظام:</strong> أيام الإجازة المعتمدة لا تُحسب ضمنك في التقارير ولا تُطبّق عليك عقوبات التأخر. العقوبات التلقائية تُسجّل في كشف الرواتب وتُطبّق كل ساعة عبر النظام.
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\employee-compliance\index.blade.php ENDPATH**/ ?>
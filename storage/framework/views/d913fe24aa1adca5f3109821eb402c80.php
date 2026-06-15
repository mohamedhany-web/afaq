
<?php $__env->startSection('page-title', 'أداء الفريق'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'متابعة فرق العمل',
    'subtitle' => 'المبيعات — الالتزام — الإنتاجية — التقارير',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'موظفون نشطون', 'value' => $activeEmployees, 'accent' => 'theme', 'href' => route('operations.team.index') . '#page-data', 'linkLabel' => 'عرض الفريق'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غياب بانتظار المراجعة', 'value' => $pendingAbsence, 'accent' => 'amber', 'href' => route('operations.attendance-reviews.index'), 'linkLabel' => 'عرض المراجعات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مديرو مبيعات', 'value' => $managers->count(), 'accent' => 'blue', 'href' => route('operations.team.index') . '#page-data', 'linkLabel' => 'عرض المديرين'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
    <?php if($teamKpis): ?> <?php echo $__env->make('operations.partials.kpi-group', ['group' => $teamKpis, 'link' => route('operations.team.index') . '#page-data'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
    <?php if($revenueKpis): ?> <?php echo $__env->make('operations.partials.kpi-group', ['group' => $revenueKpis, 'link' => route('operations.crm.index')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
    <?php if($reportingKpis): ?> <?php echo $__env->make('operations.partials.kpi-group', ['group' => $reportingKpis, 'link' => route('operations.reports.index')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
</div>

<div class="bg-white rounded-2xl border overflow-hidden font-tajawal" id="page-data">
    <div class="px-5 py-4 border-b font-bold">مندوبو المبيعات — الالتزام</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="p-3 text-right">الموظف</th>
                <th class="p-3 text-right">الالتزام العام</th>
                <th class="p-3 text-right">الحضور</th>
                <th class="p-3 text-right">تنبيهات</th>
            </tr></thead>
            <tbody>
            <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t">
                <td class="p-3 font-semibold"><?php echo e($row['user']->name); ?></td>
                <td class="p-3"><span class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e(number_format($row['compliance'], 1)); ?>%</span></td>
                <td class="p-3"><?php echo e(number_format($row['attendance'], 1)); ?>%</td>
                <td class="p-3 text-xs text-amber-700"><?php echo e(implode(' · ', $row['flags']) ?: '—'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/operations/team/index.blade.php ENDPATH**/ ?>
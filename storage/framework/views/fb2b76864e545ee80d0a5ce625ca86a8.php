
<?php $__env->startSection('page-title', 'تقرير الحضور الشهري'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تقرير الحضور الشهري',
    'subtitle' => 'ملخص حضور وغياب وإجازات وأذونات الموظفين — ' . $month->translatedFormat('F Y'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>',
    'actionUrl' => route('hr.reports.monthly.print', $filters),
    'actionLabel' => 'طباعة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl border p-5 mb-6 font-tajawal">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الشهر</label>
            <input type="month" name="month" value="<?php echo e($filters['month']); ?>" class="w-full border rounded-xl px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">القسم</label>
            <select name="department_id" class="w-full border rounded-xl px-3 py-2 text-sm">
                <option value="">جميع الأقسام</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($dept->id); ?>" <?php if($filters['department_id'] == $dept->id): echo 'selected'; endif; ?>><?php echo e($dept->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الموظف</label>
            <select name="employee_id" class="w-full border rounded-xl px-3 py-2 text-sm">
                <option value="">جميع الموظفين</option>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($row['employee']->id); ?>" <?php if($filters['employee_id'] == $row['employee']->id): echo 'selected'; endif; ?>><?php echo e($row['employee']->first_name); ?> <?php echo e($row['employee']->last_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">عرض التقرير</button>
    </form>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عدد الموظفين', 'value' => $summary['employees_count'], 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام حضور', 'value' => $summary['total_present'], 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام غياب', 'value' => $summary['total_absent'], 'accent' => 'red'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معدل الحضور', 'value' => $summary['avg_attendance_rate'] . '%', 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام تأخير', 'value' => $summary['total_late'], 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام إجازة', 'value' => $summary['total_leave_days'], 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أذونات معتمدة', 'value' => $summary['total_permits'], 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الساعات', 'value' => number_format($summary['total_hours'], 0), 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal" id="page-data">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="p-3 text-right">الموظف</th>
                    <th class="p-3 text-right">القسم</th>
                    <th class="p-3 text-right">أيام متوقعة</th>
                    <th class="p-3 text-right">حضور</th>
                    <th class="p-3 text-right">تأخير</th>
                    <th class="p-3 text-right">غياب</th>
                    <th class="p-3 text-right">إجازات</th>
                    <th class="p-3 text-right">أذونات</th>
                    <th class="p-3 text-right">ساعات</th>
                    <th class="p-3 text-right">معدل الحضور</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="p-3 font-semibold"><?php echo e($row['employee']->first_name); ?> <?php echo e($row['employee']->last_name); ?></td>
                    <td class="p-3 text-gray-600"><?php echo e($row['employee']->department?->name ?? '—'); ?></td>
                    <td class="p-3"><?php echo e($row['expected_days']); ?></td>
                    <td class="p-3 text-green-700 font-semibold"><?php echo e($row['present_days']); ?></td>
                    <td class="p-3 text-amber-700"><?php echo e($row['late_days']); ?></td>
                    <td class="p-3 text-red-700 font-semibold"><?php echo e($row['absent_days']); ?></td>
                    <td class="p-3"><?php echo e($row['leave_days']); ?></td>
                    <td class="p-3"><?php echo e($row['permit_count']); ?></td>
                    <td class="p-3"><?php echo e($row['total_hours']); ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-lg text-xs font-bold <?php echo e($row['attendance_rate'] >= 90 ? 'bg-green-100 text-green-800' : ($row['attendance_rate'] >= 75 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800')); ?>">
                            <?php echo e($row['attendance_rate']); ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="10" class="p-8 text-center text-gray-500">لا توجد بيانات لهذا الشهر.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\hr\reports\monthly.blade.php ENDPATH**/ ?>
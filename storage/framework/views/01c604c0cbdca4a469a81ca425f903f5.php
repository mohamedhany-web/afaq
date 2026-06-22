
<?php $__env->startSection('page-title', $employee->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $e = $evaluation;
    $money = fn ($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تفاصيل الالتزام: ' . $employee->name,
    'subtitle' => $e['status']['label'] . ' — ' . $e['overall_score'] . '%',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => route('crm.employee-compliance.index'),
    'actionLabel' => 'العودة للقائمة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أيام عمل متوقعة', 'value' => $e['period']['expected_work_days'], 'accent' => 'theme', 'compact' => true, 'href' => route('crm.employee-compliance.index'), 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تقارير مرفوعة', 'value' => $e['reports']['submitted'].'/'.$e['reports']['expected'], 'accent' => 'blue', 'compact' => true, 'href' => route('crm.employee-compliance.index'), 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حضور %', 'value' => $e['attendance_compliance'].'%', 'accent' => 'green', 'compact' => true, 'href' => route('crm.employee-compliance.index'), 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عقوبات', 'value' => $money($e['penalties_total']), 'accent' => 'red', 'compact' => true, 'href' => route('crm.employee-compliance.index'), 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">تفاصيل الحضور</div>
        <dl class="p-5 space-y-2 text-sm font-tajawal">
            <div class="flex justify-between"><dt class="text-gray-500">حضور</dt><dd class="font-bold"><?php echo e($e['attendance']['present']); ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">تأخر</dt><dd class="font-bold text-amber-700"><?php echo e($e['attendance']['late']); ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">غياب</dt><dd class="font-bold text-red-700"><?php echo e($e['attendance']['absent']); ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">ساعات ناقصة</dt><dd class="font-bold"><?php echo e($e['attendance']['short_hours']); ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">إجازات معتمدة</dt><dd class="font-bold"><?php echo e($e['attendance']['on_leave']); ?> يوم</dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="<?php echo e($sectionBg); ?>">CRM والمهام</div>
        <dl class="p-5 space-y-2 text-sm font-tajawal">
            <div class="flex justify-between"><dt class="text-gray-500">التزام التقارير</dt><dd class="font-bold"><?php echo e($e['reports']['percent']); ?>%</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">مهام متأخرة</dt><dd class="font-bold"><?php echo e($e['overdue_tasks']); ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">متابعات فائتة</dt><dd class="font-bold"><?php echo e($e['overdue_follow_ups']); ?></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">خصومات معتمدة</dt><dd class="font-bold"><?php echo e($money($e['deductions_total'])); ?></dd></div>
        </dl>
        <?php if(count($e['flags'])): ?>
        <div class="px-5 pb-5">
            <?php $__currentLoopData = $e['flags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="inline-block text-xs bg-amber-50 text-amber-800 px-2 py-1 rounded-lg mr-1 mb-1"><?php echo e($flag); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\employee-compliance\show.blade.php ENDPATH**/ ?>
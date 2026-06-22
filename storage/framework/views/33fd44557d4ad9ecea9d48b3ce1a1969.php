
<?php $__env->startSection('page-title', 'تقرير عمليات'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تقرير ' . $report->periodLabel(),
    'subtitle' => $report->periodRangeLabel() . ' — ' . ($report->author?->name ?? ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'actionUrl' => route('operations.reports.index', ['period' => $report->period_type]),
    'actionLabel' => 'القائمة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6 font-tajawal">
    <?php $__currentLoopData = $metricLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-xs text-gray-500"><?php echo e($label); ?></p>
        <p class="text-xl font-bold text-gray-900"><?php echo e(number_format($report->metric($key))); ?></p>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<form method="POST" action="<?php echo e($canEdit ? route('operations.reports.update', $report) : '#'); ?>" class="space-y-4 font-tajawal">
    <?php echo csrf_field(); ?>
    <?php if($canEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
    <?php
        $fields = [
            'operations_summary' => 'ملخص العمليات',
            'projects_progress' => 'تقدم المشاريع',
            'team_coordination' => 'تنسيق الفرق',
            'obstacles' => 'العقبات',
            'support_required' => 'الدعم المطلوب من الإدارة',
            'next_period_plan' => 'خطة الفترة القادمة',
            'notes' => 'ملاحظات',
        ];
    ?>
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white rounded-2xl border p-5">
        <label class="block text-sm font-bold text-gray-700 mb-2"><?php echo e($label); ?></label>
        <textarea name="<?php echo e($name); ?>" rows="<?php echo e($name === 'notes' ? 3 : 4); ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm" <?php if(!$canEdit): echo 'disabled'; endif; ?> placeholder="<?php echo e($name === 'notes' ? 'ملاحظات عامة على التقرير أو الفريق...' : ''); ?>"><?php echo e(old($name, $report->$name)); ?></textarea>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($canEdit): ?>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="px-6 py-3 rounded-xl text-white font-bold text-sm" style="background: <?php echo e($themeColor); ?>;">حفظ المسودة</button>
        <button type="submit" formaction="<?php echo e(route('operations.reports.submit', $report)); ?>" formmethod="POST"
                onclick="return confirm('رفع التقرير للإدارة؟ لن يمكن التعديل بعد الرفع.');"
                class="px-6 py-3 rounded-xl text-white font-bold text-sm bg-green-600">رفع للإدارة</button>
    </div>
    <?php endif; ?>
</form>

<?php if($canEdit): ?>
<div class="flex flex-wrap gap-3 mt-4 font-tajawal">
    <form method="POST" action="<?php echo e(route('operations.reports.refresh', $report)); ?>"><?php echo csrf_field(); ?><button type="submit" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-bold">تحديث الأرقام</button></form>
</div>
<?php endif; ?>

<?php if($report->admin_notes): ?>
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-5 font-tajawal">
    <p class="font-bold text-blue-900 mb-2">ملاحظات الإدارة</p>
    <p class="text-sm text-blue-800 whitespace-pre-wrap"><?php echo e($report->admin_notes); ?></p>
</div>
<?php endif; ?>

<?php if($canAnnotate): ?>
<form method="POST" action="<?php echo e(route('operations.reports.annotate', $report)); ?>" class="mt-6 bg-white border rounded-2xl p-5 font-tajawal">
    <?php echo csrf_field(); ?>
    <label class="block text-sm font-bold text-gray-700 mb-2">إضافة ملاحظات الإدارة</label>
    <textarea name="admin_notes" rows="3" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm" required><?php echo e(old('admin_notes', $report->admin_notes)); ?></textarea>
    <button type="submit" class="mt-3 px-6 py-2.5 rounded-xl text-white text-sm font-bold" style="background: <?php echo e($themeColor); ?>;">حفظ ملاحظات الإدارة</button>
</form>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\operations\reports\show.blade.php ENDPATH**/ ?>
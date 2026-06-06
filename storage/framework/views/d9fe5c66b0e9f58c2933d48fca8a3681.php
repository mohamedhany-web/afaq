
<?php $__env->startSection('page-title', 'تقرير تسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $m = $report->metrics ?? [];
    $v = fn ($s, $k) => data_get($m, "{$s}.{$k}", 0);
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'تقرير ' . $report->periodLabel(),
    'subtitle' => ($report->author?->name ?? '—') . ' — ' . $report->periodRangeLabel(),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="mb-4 flex flex-wrap gap-2 items-center">
    <?php if($report->isSubmitted()): ?>
    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 font-tajawal">مرفوع <?php echo e($report->submitted_at?->format('Y-m-d H:i')); ?></span>
    <?php else: ?>
    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 font-tajawal">مسودة — أكمل وارفع</span>
    <?php endif; ?>
    <a href="<?php echo e(route('marketing.reports.index', ['period' => $report->period_type])); ?>" class="text-sm text-gray-600 font-tajawal mr-auto">← العودة</a>
    <?php if($canEdit): ?>
    <form action="<?php echo e(route('marketing.reports.refresh', $report)); ?>" method="POST" class="inline"><?php echo csrf_field(); ?>
        <button type="submit" class="px-4 py-2 rounded-xl border text-sm font-tajawal">تحديث من النظام</button>
    </form>
    <?php endif; ?>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهام مكتملة', 'value' => $v('activities', 'completed'), 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'Leads جديدة', 'value' => $v('leads', 'created'), 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهام مجدولة', 'value' => $v('activities', 'assigned'), 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حملات نشطة', 'value' => $v('campaigns', 'active_involved'), 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if(!empty($m['team'])): ?>
<div class="mb-6 p-5 rounded-2xl bg-purple-50 border border-purple-100 font-tajawal">
    <p class="font-bold text-purple-900 mb-2">ملخص الفريق (من النظام)</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
        <div>أعضاء: <strong><?php echo e($m['team']['members_count'] ?? 0); ?></strong></div>
        <div>Leads الفريق: <strong><?php echo e($m['team']['leads_created'] ?? 0); ?></strong></div>
        <div>تقارير اليوم: <strong><?php echo e($m['team']['daily_reports_submitted_today'] ?? 0); ?></strong></div>
        <div>ناقص اليوم: <strong class="text-red-700"><?php echo e($m['team']['daily_reports_missing_today'] ?? 0); ?></strong></div>
    </div>
</div>
<?php endif; ?>

<?php if($canEdit): ?>
<div class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal">
    <form id="report-fields">
        <div><label class="<?php echo e($label); ?>">ملخص الأنشطة *</label><textarea name="activities_summary" rows="4" required class="<?php echo e($input); ?>"><?php echo e(old('activities_summary', $report->activities_summary)); ?></textarea></div>
        <div><label class="<?php echo e($label); ?>">تقدم الحملات</label><textarea name="campaigns_progress" rows="3" class="<?php echo e($input); ?>"><?php echo e(old('campaigns_progress', $report->campaigns_progress)); ?></textarea></div>
        <?php if(in_array($report->period_type, ['weekly', 'monthly'], true)): ?>
        <div><label class="<?php echo e($label); ?>">ملخص أداء الفريق *</label><textarea name="team_summary" rows="4" required class="<?php echo e($input); ?>"><?php echo e(old('team_summary', $report->team_summary)); ?></textarea></div>
        <?php endif; ?>
        <div><label class="<?php echo e($label); ?>">معوقات</label><textarea name="obstacles" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('obstacles', $report->obstacles)); ?></textarea></div>
        <div><label class="<?php echo e($label); ?>">دعم مطلوب</label><textarea name="support_required" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('support_required', $report->support_required)); ?></textarea></div>
        <div><label class="<?php echo e($label); ?>">خطة الفترة القادمة</label><textarea name="next_period_plan" rows="2" class="<?php echo e($input); ?>"><?php echo e(old('next_period_plan', $report->next_period_plan)); ?></textarea></div>
    </form>
    <div class="flex flex-wrap gap-3 pt-2">
        <form action="<?php echo e(route('marketing.reports.update', $report)); ?>" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <button type="submit" onclick="copyReportFields(event)" class="px-6 py-3 rounded-xl border font-semibold text-sm">حفظ مسودة</button>
        </form>
        <form action="<?php echo e(route('marketing.reports.submit', $report)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" onclick="copyReportFields(event)" class="px-8 py-3 rounded-xl text-white font-semibold text-sm" style="background:#7c3aed">رفع التقرير</button>
        </form>
    </div>
</div>
<script>
function copyReportFields(e) {
    const src = document.getElementById('report-fields');
    const form = e.target.closest('form');
    src.querySelectorAll('textarea').forEach(el => {
        let hidden = form.querySelector(`[name="${el.name}"]`);
        if (!hidden) { hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = el.name; form.appendChild(hidden); }
        hidden.value = el.value;
    });
}
</script>
<?php else: ?>
<div class="bg-white rounded-2xl shadow-lg border p-5 sm:p-6 space-y-4 font-tajawal text-sm">
    <div><strong>ملخص الأنشطة:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo e($report->activities_summary ?? '—'); ?></p></div>
    <div><strong>تقدم الحملات:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo e($report->campaigns_progress ?? '—'); ?></p></div>
    <?php if($report->team_summary): ?><div><strong>ملخص الفريق:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo e($report->team_summary); ?></p></div><?php endif; ?>
    <div><strong>معوقات:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo e($report->obstacles ?? '—'); ?></p></div>
    <div><strong>خطة قادمة:</strong><p class="mt-1 text-gray-700 whitespace-pre-wrap"><?php echo e($report->next_period_plan ?? '—'); ?></p></div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/period-reports/show.blade.php ENDPATH**/ ?>
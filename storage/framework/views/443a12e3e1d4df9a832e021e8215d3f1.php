
<?php $__env->startSection('page-title', 'لوحة العمليات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $kpiLinks = [
        'lead_management' => route('operations.leads.index'),
        'crm_management' => route('operations.crm.index'),
        'sales_operations' => route('operations.crm.index'),
        'revenue_impact' => route('operations.crm.index'),
        'inventory_operations' => route('operations.inventory.index'),
        'team_performance' => route('operations.team.index'),
        'reporting_management' => route('operations.reports.index'),
    ];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'لوحة تحكم العمليات',
    'subtitle' => 'مركز التشغيل — توزيع العملاء · المخزون · الفريق · الحضور',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
    'actionUrl' => route('operations.reports.index'),
    'actionLabel' => 'تقاريري',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('operations.partials.crm-pulse', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'عملاء بانتظار التوزيع', 'value' => $stats['unassigned_leads'], 'accent' => 'amber', 'href' => route('operations.leads.index', ['filter' => 'unassigned']) . '#page-data', 'linkLabel' => 'توزيع العملاء'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'غياب بانتظار المراجعة', 'value' => $stats['pending_absence_reviews'], 'accent' => 'red', 'href' => route('operations.attendance-reviews.index', ['status' => 'pending']) . '#page-data', 'linkLabel' => 'مراجعة الغياب'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'انصراف بانتظار الموافقة', 'value' => $stats['pending_checkout_reviews'], 'accent' => 'purple', 'href' => route('operations.checkout-reviews.index') . '#page-data', 'linkLabel' => 'موافقات الانصراف'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مشاريع نشطة', 'value' => $stats['active_projects'], 'accent' => 'theme', 'href' => route('operations.inventory.index') . '#page-data', 'linkLabel' => 'المخزون العقاري'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مطورون نشطون', 'value' => $stats['developers'], 'accent' => 'blue', 'href' => route('admin.developers.index'), 'linkLabel' => 'عرض المطورين'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تقاريري — مسودات', 'value' => $stats['pending_reports'], 'accent' => 'amber', 'href' => route('operations.reports.index', ['status' => 'draft']) . '#page-data', 'linkLabel' => 'عرض المسودات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تقاريري — مرفوعة', 'value' => $stats['submitted_reports'], 'accent' => 'green', 'href' => route('operations.reports.index', ['status' => 'submitted']) . '#page-data', 'linkLabel' => 'عرض المرفوعة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if($resolver->isAdmin()): ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تقارير الفريق — مسودات', 'value' => $stats['team_reports_pending'] ?? 0, 'accent' => 'amber', 'href' => route('operations.reports.index', ['status' => 'draft']) . '#page-data', 'linkLabel' => 'متابعة الفريق'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6 font-tajawal">
    <div class="bg-white rounded-2xl border p-5">
        <p class="text-xs text-gray-500 mb-1">مؤشرات التعويضات — <?php echo e($period->label); ?></p>
        <p class="text-3xl font-extrabold" style="color:<?php echo e($themeColor); ?>"><?php echo e(number_format($kpi['total_score'] ?? 0, 1)); ?>%</p>
        <p class="text-sm text-gray-600 mt-2">الدرجة الإجمالية للفترة الحالية</p>
        <a href="<?php echo e(route('crm.compensation.dashboard')); ?>" class="inline-flex items-center gap-1 text-xs font-bold mt-3 hover:underline" style="color:<?php echo e($themeColor); ?>">تفاصيل التعويضات ←</a>
    </div>
    <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
        <?php $__currentLoopData = [
            ['route' => 'operations.leads.index', 'label' => 'توزيع العملاء', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route' => 'operations.follow-ups.index', 'label' => 'جدول المتابعات', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['route' => 'operations.crm.index', 'label' => 'متابعة CRM', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
            ['route' => 'operations.inventory.index', 'label' => 'المخزون', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['route' => 'operations.team.index', 'label' => 'أداء الفريق', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['route' => 'operations.attendance-reviews.index', 'label' => 'مراجعة الغياب', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['route' => 'operations.checkout-reviews.index', 'label' => 'موافقات الانصراف', 'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route($action['route'])); ?>" class="flex flex-col items-center gap-2 p-4 rounded-2xl border bg-white hover:shadow-md transition-shadow text-center group">
            <div class="p-3 rounded-xl text-white" style="background:<?php echo e($themeColor); ?>">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($action['icon']); ?>"/></svg>
            </div>
            <span class="text-xs font-bold text-gray-800"><?php echo e($action['label']); ?></span>
            <span class="text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity" style="color:<?php echo e($themeColor); ?>">فتح القسم ←</span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 font-tajawal" id="page-data">
    <?php $__currentLoopData = $kpiGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('operations.partials.kpi-group', [
        'group' => $group,
        'link' => $kpiLinks[$group['key'] ?? ''] ?? null,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/operations/dashboard.blade.php ENDPATH**/ ?>
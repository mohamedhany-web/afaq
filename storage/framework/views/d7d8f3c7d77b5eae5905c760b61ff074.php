<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $user = auth()->user();
    $links = [];

    $links[] = [
        'label' => 'تقارير المبيعات اليومية',
        'desc' => 'تقارير المندوبين اليومية والمراجعة',
        'url' => route('crm.daily-reports.index'),
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    ];

    $links[] = [
        'label' => 'تحليلات الأداء',
        'desc' => 'مؤشرات التحويل والأداء التفصيلي',
        'url' => route('crm.intelligence.index'),
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    ];

    if ($user?->can('view-reports')) {
        $links[] = [
            'label' => 'تقارير النظام',
            'desc' => 'عملاء، صفقات، مشاريع — مع Excel',
            'url' => route('admin.system-reports.index'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>',
        ];
        $links[] = [
            'label' => 'تقرير المبيعات المالي',
            'desc' => 'ملخص الصفقات حسب الفترة',
            'url' => route('reports.sales'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>',
        ];
    }

    if ($user?->hasRole(['super_admin', 'admin', 'sales_manager'])) {
        $links[] = [
            'label' => 'تعويضات وعمولات',
            'desc' => 'كشوف الرواتب والعمولات',
            'url' => route('crm.compensation.dashboard'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ];
    }

    if ($user?->canAccessOperations()) {
        $links[] = [
            'label' => 'تقارير العمليات',
            'desc' => 'أداء الفرق والمتابعات',
            'url' => route('operations.reports.index'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
        ];
    }
?>

<div class="mb-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden font-tajawal">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <div>
            <h3 class="font-bold text-gray-900">مركز التقارير</h3>
            <p class="text-xs text-gray-500 mt-0.5">وصول سريع لكل تقارير المبيعات والتحليلات</p>
        </div>
        <a href="<?php echo e(route('crm.pipeline.index', ['view' => 'deals'])); ?>" class="text-xs font-semibold shrink-0 hover:underline" style="color: <?php echo e($themeColor); ?>">مسار المبيعات</a>
    </div>
    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($link['url']); ?>" class="flex items-start gap-3 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/80 transition-all group">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background: <?php echo e($themeColor); ?>12;">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: <?php echo e($themeColor); ?>"><?php echo $link['icon']; ?></svg>
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-bold text-gray-900 group-hover:underline"><?php echo e($link['label']); ?></span>
                <span class="block text-xs text-gray-500 mt-0.5"><?php echo e($link['desc']); ?></span>
            </span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\partials\reports-hub.blade.php ENDPATH**/ ?>
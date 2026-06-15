
<?php $__env->startSection('page-title', 'متابعة CRM'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'متابعة CRM',
    'subtitle' => 'جودة البيانات — مراحل البيع — العملاء المتعثرون',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
    'actionUrl' => auth()->user()?->can('create', \App\Models\Client::class) ? route('crm.clients.create') : null,
    'actionLabel' => 'عميل جديد',
    'secondaryUrl' => auth()->user()->clientsHubUrl(),
    'secondaryLabel' => 'كل العملاء',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي العملاء', 'value' => $stats['total_clients'], 'accent' => 'theme', 'href' => auth()->user()->clientsHubUrl(), 'linkLabel' => 'عرض العملاء'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'صفقات نشطة', 'value' => $stats['active_deals'], 'accent' => 'blue', 'href' => route('crm.pipeline.index', ['view' => 'deals']), 'linkLabel' => 'عرض الصفقات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إغلاقات الشهر', 'value' => $stats['won_month'], 'accent' => 'green', 'href' => route('crm.pipeline.index', ['view' => 'deals', 'stage' => 'closed_won']), 'linkLabel' => 'عرض الإغلاقات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
    <?php if($crmKpis): ?> <?php echo $__env->make('operations.partials.kpi-group', ['group' => $crmKpis, 'link' => route('operations.crm.index') . '#page-data'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
    <?php if($salesKpis): ?> <?php echo $__env->make('operations.partials.kpi-group', ['group' => $salesKpis, 'link' => route('crm.pipeline.index')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 font-tajawal" id="page-data">
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold">الـ Pipeline</div>
        <div class="p-4 space-y-2">
            <?php $stageLabels = ['lead'=>'عميل جديد','prospect'=>'تم التواصل','proposal'=>'معاينة','negotiation'=>'تفاوض','closed_won'=>'مغلق — ربح','closed_lost'=>'مغلق — خسارة']; ?>
            <?php $__currentLoopData = $pipeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('crm.pipeline.index', ['view' => 'deals', 'stage' => $stage])); ?>" class="flex justify-between items-center p-2 rounded-lg bg-gray-50 text-sm hover:bg-gray-100 transition-colors">
                <span><?php echo e($stageLabels[$stage] ?? $stage); ?></span>
                <span class="font-bold"><?php echo e($row->cnt); ?> <span class="text-gray-400 font-normal">(<?php echo e(number_format($row->val)); ?>)</span></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="px-5 py-4 border-b font-bold text-amber-800">عملاء متعثرون (+5 أيام)</div>
        <ul class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $staleClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="p-4 text-sm">
                <a href="<?php echo e($client->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($client->name); ?></a>
                <p class="text-xs text-gray-500"><?php echo e($client->assignedEmployee ? trim($client->assignedEmployee->first_name.' '.$client->assignedEmployee->last_name) : 'غير معيّن'); ?> — <?php echo e($client->updated_at->diffForHumans()); ?></p>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="p-6 text-center text-gray-500 text-sm">لا يوجد</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden" id="missed-reminders">
        <div class="px-5 py-4 border-b font-bold text-red-700">متابعات فائتة</div>
        <ul class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $overdueFollowUps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="p-4 text-sm flex justify-between gap-3">
                <div>
                    <?php if($fu->client): ?>
                    <a href="<?php echo e($fu->client->profileUrl()); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($fu->client->name); ?></a>
                    <?php else: ?>
                    <p class="font-semibold">—</p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-500"><?php echo e($fu->user?->name); ?> — <?php echo e($fu->scheduled_at?->format('Y-m-d H:i')); ?></p>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="p-6 text-center text-gray-500 text-sm">لا توجد متابعات فائتة</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/operations/crm/index.blade.php ENDPATH**/ ?>
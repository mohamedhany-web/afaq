<?php $__env->startSection('page-title', $client->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $money = fn($v) => \App\Helpers\SettingsHelper::formatMoney($v);
    $stageLabels = \App\Services\CrmScopeService::leadStageLabels();
    $dealsCount = $client->sales->count();
    $dealsValue = $client->sales->sum('estimated_value');
    $canDelete = auth()->user()?->can('delete', $client);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $client->name,
    'subtitle' => 'ملف العميل — ' . $client->typeLabel(),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'secondaryUrl' => auth()->user()?->can('update', $client) ? route('crm.clients.edit', $client) : null,
    'secondaryLabel' => 'تعديل البيانات',
    'secondaryIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
    'actionUrl' => route('crm.pipeline.create', ['client_id' => $client->id]),
    'actionLabel' => 'صفقة جديدة',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.clients.partials.sales-rep-card', compact('client', 'themeColor', 'assignableReps'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الصفقات', 'value' => $dealsCount, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />', 'href' => route('crm.pipeline.client', $client), 'linkLabel' => 'عرض الصفقات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قيمة الصفقات', 'value' => $money($dealsValue), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />', 'href' => route('crm.pipeline.client', $client), 'linkLabel' => 'عرض الصفقات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الحالة', 'value' => match($client->status) { 'prospect' => 'محتمل', 'active' => 'نشط', 'inactive' => 'غير نشط', 'suspended' => 'موقوف', default => $client->status }, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />', 'href' => '#client-details', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تاريخ التسجيل', 'value' => $client->created_at->format('Y/m/d · H:i'), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />', 'href' => '#client-details', 'linkLabel' => 'عرض التفاصيل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($relatedProjects->isNotEmpty()): ?>
<div class="mb-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        المشاريع المرتبطة
    </div>
    <div class="p-5 sm:p-6 flex flex-wrap gap-2">
        <?php $__currentLoopData = $relatedProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('crm.projects.show', $project)); ?>" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 hover:border-gray-300 transition-all"
           style="background: <?php echo e($themeColor); ?>08; color: <?php echo e($themeColor); ?>;">
            <?php echo e($project->name); ?>

            <?php if($project->city): ?><span class="text-xs text-gray-500 mr-2">· <?php echo e($project->city); ?></span><?php endif; ?>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<?php echo $__env->make('crm.clients.partials.portal-hub', ['client' => $client, 'portalHub' => $portalHub ?? [], 'themeColor' => $themeColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.clients.partials.journey-kanban', compact('client', 'stageLabels', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('crm.clients.partials.unified-timeline', compact('client', 'timeline', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-6 w-full">
    
    <div id="client-details" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
             style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            بيانات التواصل
        </div>
        <div class="p-5 sm:p-6 space-y-4">
            <?php echo $__env->make('crm.clients.partials.registration-meta', compact('client'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الهاتف</dt>
                <dd class="font-medium text-gray-900 font-tajawal" dir="ltr"><?php echo e($client->phone); ?></dd>
            </div>
            <?php if($client->id_number): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">رقم البطاقة</dt>
                <dd class="font-medium text-gray-900 font-tajawal" dir="ltr"><?php echo e($client->id_number); ?></dd>
            </div>
            <?php endif; ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">البريد الإلكتروني</dt>
                <dd class="text-gray-900 font-tajawal" dir="ltr"><?php echo e($client->email ?? '—'); ?></dd>
            </div>
            <?php if($client->company_name): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الشركة</dt>
                <dd class="text-gray-900 font-tajawal"><?php echo e($client->company_name); ?></dd>
            </div>
            <?php endif; ?>
            <?php if($client->address): ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">العنوان</dt>
                <dd class="text-gray-900 font-tajawal"><?php echo e($client->address); ?></dd>
            </div>
            <?php endif; ?>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">تصنيف العميل</dt>
                <dd><?php echo $__env->make('crm.clients.partials.type-badge', ['type' => $client->client_type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">مصدر العميل</dt>
                <dd>
                    <?php echo $__env->make('crm.clients.partials.source-badge', ['source' => $client->lead_source], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('crm.clients.partials.source-details-display', compact('client'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">الحالة</dt>
                <dd><?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd>
            </div>
            <?php if($client->description): ?>
            <div class="sm:col-span-2">
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">وصف العميل</dt>
                <dd class="text-gray-700 text-sm font-tajawal whitespace-pre-line"><?php echo e($client->description); ?></dd>
            </div>
            <?php endif; ?>
            <?php if($client->notes): ?>
            <div class="sm:col-span-2">
                <dt class="text-xs font-bold text-gray-500 mb-1 font-tajawal">ملاحظات</dt>
                <dd class="text-gray-700 text-sm font-tajawal whitespace-pre-line"><?php echo e($client->notes); ?></dd>
            </div>
            <?php endif; ?>
            </div>
        </div>
        <?php if(!empty($pendingChange)): ?>
        <div class="mx-5 sm:mx-6 mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm font-tajawal">
            يوجد طلب <strong><?php echo e($pendingChange->actionLabel()); ?></strong> بانتظار موافقة الإدارة.
            <a href="<?php echo e(route('crm.clients.approvals.show', $pendingChange)); ?>" class="font-bold mr-1" style="color:<?php echo e($themeColor); ?>">عرض الطلب</a>
        </div>
        <?php endif; ?>
        <div class="px-5 sm:px-6 py-4 border-t border-gray-100 flex flex-wrap gap-2">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $client)): ?>
            <?php if(empty($pendingChange)): ?>
            <a href="<?php echo e(route('crm.clients.edit', $client)); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal text-white"
               style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);"><?php echo e(($requiresApproval ?? false) ? 'طلب تعديل' : 'تعديل البيانات'); ?></a>
            <?php endif; ?>
            <?php endif; ?>
            <a href="<?php echo e(route('crm.pipeline.client', $client)); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 font-medium"
               style="border-color: <?php echo e($themeColor); ?>40; color: <?php echo e($themeColor); ?>;">فتح في المسار</a>
            <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">قائمة المسار</a>
            <a href="<?php echo e(route('crm.clients.index')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">كل العملاء</a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $client)): ?>
            <?php if(empty($pendingChange)): ?>
            <div class="w-full sm:w-auto sm:min-w-[220px]">
                <?php if($requiresApproval ?? false): ?>
                    <?php echo $__env->make('crm.partials.delete-request-form', [
                        'action' => route('crm.clients.destroy', $client),
                        'label' => 'طلب حذف العميل',
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('crm.partials.delete-request-form', [
                        'action' => route('crm.clients.destroy', $client),
                        'label' => 'حذف العميل',
                        'confirmMessage' => 'هل أنت متأكد من حذف هذا العميل؟',
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewActivityLog', $client)): ?>
    <?php echo $__env->make('crm.clients.partials.activity-log', ['activityLogs' => $activityLogs ?? collect(), 'themeColor' => $themeColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <?php echo $__env->make('crm.clients.partials.staff-notes', compact('client', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('crm.clients.partials.deals-list', compact('client', 'stageLabels', 'themeColor', 'money'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php echo $__env->make('crm.partials.lost-reason-modal', ['lostReasons' => $lostReasons ?? config('crm_intelligence.lost_reasons')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\show.blade.php ENDPATH**/ ?>
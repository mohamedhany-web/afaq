<?php $__env->startSection('page-title', $project->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $fieldLabel = 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = 'text-sm font-medium text-gray-900 font-tajawal';
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $project->name,
    'subtitle' => ($project->city ?? '') . ($project->location ? ' — ' . $project->location : ''),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />',
    'actionUrl' => route('crm.pipeline.create', ['project_id' => $project->id]),
    'actionLabel' => 'صفقة على المشروع',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 w-full">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الوحدات', 'value' => $project->total_units ?? 0, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متاح', 'value' => $project->available_units ?? 0, 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الصفقات', 'value' => $project->sales_count, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'قيمة الصفقات', 'value' => \App\Helpers\SettingsHelper::formatMoney($stats['sales_value']), 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($project->hasMapLocation() || $project->mapPins->isNotEmpty()): ?>
<div class="mb-4 flex flex-wrap gap-2 justify-end">
    <button type="button" onclick="window.open(<?php echo json_encode(route('public.project.locate.viewer', ['project' => $project, 'mode' => 'satellite'])) ?>, 'map_viewer', 'width=1100,height=760')"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold font-tajawal text-white"
            style="background: <?php echo e($themeColor); ?>;">
        🛰️ نافذة العرض الجوي
    </button>
    <a href="<?php echo e(route('public.project.locate', $project)); ?>" target="_blank" rel="noopener noreferrer"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold font-tajawal border-2 border-gray-200 text-gray-700 hover:bg-gray-50">
        صفحة مشاركة الموقع
    </a>
</div>
<?php endif; ?>
<?php echo $__env->make('projects.partials.map-display', compact('project', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('crm.projects.partials.building-units', compact('project', 'themeColor', 'buildingSummary'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="mb-6">
    <?php echo $__env->make('projects.partials.ownership-summary', compact('project', 'themeColor'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 w-full">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">تفاصيل المشروع</div>
        <div class="p-5 sm:p-6 space-y-4">
            <div><dt class="<?php echo e($fieldLabel); ?>">حالة العرض</dt><dd class="mt-1"><?php echo $__env->make('projects.partials.listing-badge', ['status' => $project->listing_status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd></div>
            <div><dt class="<?php echo e($fieldLabel); ?>">المدينة</dt><dd class="<?php echo e($fieldValue); ?>"><?php echo e($project->city ?? '—'); ?></dd></div>
            <div><dt class="<?php echo e($fieldLabel); ?>">الموقع</dt><dd class="<?php echo e($fieldValue); ?>"><?php echo e($project->location ?? '—'); ?></dd></div>
            <div><dt class="<?php echo e($fieldLabel); ?>">نوع العقار</dt><dd class="<?php echo e($fieldValue); ?>"><?php echo e($project->property_type_name); ?></dd></div>
            <div><dt class="<?php echo e($fieldLabel); ?>">نوع التطوير</dt><dd class="<?php echo e($fieldValue); ?>"><?php echo e($project->development_type_name); ?></dd></div>
            <div><dt class="<?php echo e($fieldLabel); ?>">نوع الملكية</dt><dd class="mt-1"><?php echo $__env->make('projects.partials.ownership-badge', ['type' => $project->ownership_type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd></div>
            <?php if($project->ownership_type === 'developer_third_party'): ?>
            <div><dt class="<?php echo e($fieldLabel); ?>">المطور</dt><dd class="<?php echo e($fieldValue); ?>"><?php echo e($project->displayDeveloperName()); ?></dd></div>
            <?php endif; ?>
            <div><dt class="<?php echo e($fieldLabel); ?>">السعر</dt>
                <dd class="font-bold font-tajawal" style="color: <?php echo e($themeColor); ?>;">
                    <?php echo e(\App\Helpers\SettingsHelper::formatMoney($project->price_from)); ?>

                    <?php if($project->price_to): ?> — <?php echo e(\App\Helpers\SettingsHelper::formatMoney($project->price_to)); ?><?php endif; ?>
                </dd>
            </div>
            <?php if($project->description): ?>
            <div><dt class="<?php echo e($fieldLabel); ?>">الوصف</dt><dd class="text-sm text-gray-700 font-tajawal whitespace-pre-line"><?php echo e($project->description); ?></dd></div>
            <?php endif; ?>
        </div>
        <?php if(session('success')): ?>
        <div class="mx-5 sm:mx-6 mt-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
        <div class="mx-5 sm:mx-6 mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if(!empty($pendingChange)): ?>
        <div class="mx-5 sm:mx-6 mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm font-tajawal">
            يوجد طلب <strong><?php echo e($pendingChange->actionLabel()); ?></strong> بانتظار موافقة الإدارة.
            <a href="<?php echo e(route('crm.projects.approvals.show', $pendingChange)); ?>" class="font-bold mr-1" style="color:<?php echo e($themeColor); ?>">عرض الطلب</a>
        </div>
        <?php endif; ?>
        <div class="px-5 sm:px-6 py-4 border-t border-gray-100 flex flex-col gap-2">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-projects')): ?>
            <?php if(empty($pendingChange)): ?>
            <a href="<?php echo e(route('crm.projects.edit', $project)); ?>" class="inline-flex justify-center w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-white font-tajawal"
               style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);"><?php echo e(($requiresApproval ?? false) ? 'طلب تعديل' : 'تعديل المشروع'); ?></a>
            <?php endif; ?>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $project)): ?>
            <?php if(empty($pendingChange)): ?>
            <form action="<?php echo e(route('crm.projects.destroy', $project)); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(($requiresApproval ?? false) ? 'إرسال طلب حذف للإدارة العليا؟' : 'حذف هذا المشروع؟ لا يمكن التراجع.'); ?>')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100 font-tajawal"><?php echo e(($requiresApproval ?? false) ? 'طلب حذف المشروع' : 'حذف المشروع'); ?></button>
            </form>
            <?php endif; ?>
            <?php elseif(!$project->isDeletable()): ?>
            <p class="text-xs text-center text-gray-400 font-tajawal py-1">لا يمكن الحذف — المشروع مرتبط بصفقات أو يحتوي وحدات مباعة</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">آخر الصفقات</div>
        <div class="p-5 sm:p-6">
            <?php $__empty_1 = true; $__currentLoopData = $project->sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('crm.pipeline.show', $sale)); ?>" class="block p-4 mb-3 last:mb-0 rounded-xl border border-gray-100 hover:bg-gray-50 font-tajawal">
                <div class="font-semibold text-gray-900"><?php echo e($sale->product_service); ?></div>
                <?php if($sale->client): ?><div class="text-xs text-gray-500 mt-1"><?php echo e($sale->client->name); ?></div><?php endif; ?>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-gray-400 py-6 font-tajawal">لا توجد صفقات بعد — ابدأ بصفقة جديدة على هذا المشروع.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/projects/show.blade.php ENDPATH**/ ?>
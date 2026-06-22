
<?php $__env->startSection('page-title', 'مراجعة طلب مشروع'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $payload = $request->payload ?? [];
    $projectData = $payload['project'] ?? [];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $request->summary,
    'subtitle' => $request->actionLabel() . ' — ' . $request->statusLabel(),
    'actionUrl' => route('crm.projects.approvals.index'),
    'actionLabel' => 'كل الطلبات',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-tajawal">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 space-y-4">
        <h3 class="font-bold text-gray-900">تفاصيل الطلب</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-xs text-gray-500">مقدم الطلب</dt><dd class="font-semibold"><?php echo e($request->requester?->name); ?></dd></div>
            <div><dt class="text-xs text-gray-500">التاريخ</dt><dd><?php echo e($request->created_at->format('Y-m-d H:i')); ?></dd></div>
            <?php if($request->project): ?>
            <div><dt class="text-xs text-gray-500">المشروع الحالي</dt><dd><a href="<?php echo e(route('crm.projects.show', $request->project)); ?>" class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($request->project->name); ?></a></dd></div>
            <?php endif; ?>
        </dl>

        <?php if($request->action !== 'delete' && !empty($projectData)): ?>
        <div class="border-t pt-4 space-y-2 text-sm">
            <p><span class="text-gray-500">الاسم:</span> <strong><?php echo e($projectData['name'] ?? '—'); ?></strong></p>
            <p><span class="text-gray-500">المدينة:</span> <?php echo e($projectData['city'] ?? '—'); ?></p>
            <p><span class="text-gray-500">نوع العقار:</span> <?php echo e(\App\Models\Project::formatPropertyTypesLabel($projectData['property_types'] ?? $projectData['property_type'] ?? null)); ?></p>
            <p><span class="text-gray-500">حالة العرض:</span> <?php echo e(\App\Models\Project::LISTING_STATUSES[$projectData['listing_status'] ?? ''] ?? '—'); ?></p>
            <p><span class="text-gray-500">الوحدات:</span> <?php echo e($projectData['total_units'] ?? 0); ?> (متاح: <?php echo e($projectData['available_units'] ?? 0); ?>)</p>
            <?php if(!empty($projectData['description'])): ?>
            <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($projectData['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php elseif($request->action === 'delete'): ?>
        <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl p-4 space-y-2">
            <p>طلب حذف المشروع: <strong><?php echo e($payload['project_name'] ?? $request->project?->name); ?></strong></p>
            <?php if($request->request_reason || !empty($payload['delete_reason'])): ?>
            <p class="text-red-800"><span class="font-bold">سبب الحذف:</span> <?php echo e($request->request_reason ?? $payload['delete_reason']); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if($request->review_notes): ?>
        <div class="border-t pt-4 text-sm text-gray-600">
            <p class="text-xs text-gray-500 mb-1">ملاحظات المراجعة</p>
            <?php echo e($request->review_notes); ?>

        </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6">
        <?php if($canApprove): ?>
        <h3 class="font-bold text-gray-900 mb-4">قرار الإدارة العليا</h3>
        <form action="<?php echo e(route('crm.projects.approvals.approve', $request)); ?>" method="POST" class="mb-4">
            <?php echo csrf_field(); ?>
            <textarea name="review_notes" rows="2" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm mb-3" placeholder="ملاحظات (اختياري)"></textarea>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-bold">موافقة وتنفيذ</button>
        </form>
        <form action="<?php echo e(route('crm.projects.approvals.reject', $request)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <textarea name="review_notes" rows="2" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm mb-3" placeholder="سبب الرفض (اختياري)"></textarea>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm font-bold">رفض الطلب</button>
        </form>
        <?php else: ?>
        <p class="text-sm text-gray-600">الحالة: <strong><?php echo e($request->statusLabel()); ?></strong></p>
        <?php if($request->reviewer): ?>
        <p class="text-xs text-gray-500 mt-2">بواسطة <?php echo e($request->reviewer->name); ?> — <?php echo e($request->reviewed_at?->format('Y-m-d H:i')); ?></p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\projects\approvals\show.blade.php ENDPATH**/ ?>
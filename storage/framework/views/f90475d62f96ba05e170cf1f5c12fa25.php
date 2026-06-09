
<?php $__env->startSection('page-title', 'طلباتي — المشاريع'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'طلبات المشاريع',
    'subtitle' => 'إضافة وتعديل وحذف المشاريع — تُنفَّذ بعد موافقة الإدارة العليا',
    'actionUrl' => route('crm.projects.create'),
    'actionLabel' => 'طلب مشروع جديد',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900 font-tajawal">
    أي إضافة أو تعديل أو حذف لمشروع عقاري يمرّ بموافقة <strong>الإدارة العليا</strong> قبل التطبيق على النظام.
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 divide-y divide-gray-100">
    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('crm.projects.approvals.show', $req)); ?>" class="block px-5 py-4 hover:bg-gray-50 font-tajawal">
        <div class="flex justify-between gap-3">
            <div>
                <p class="font-semibold text-gray-900"><?php echo e($req->summary); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($req->actionLabel()); ?> — <?php echo e($req->created_at->format('Y-m-d H:i')); ?></p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold h-fit
                <?php if($req->statusColor()==='amber'): ?> bg-amber-100 text-amber-800
                <?php elseif($req->statusColor()==='green'): ?> bg-green-100 text-green-800
                <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>"><?php echo e($req->statusLabel()); ?></span>
        </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="p-8 text-center text-sm text-gray-500">لم ترسل أي طلبات بعد.</p>
    <?php endif; ?>
</div>
<?php if($requests->hasPages()): ?>
<div class="mt-4"><?php echo e($requests->links()); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/projects/my-requests.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', 'موافقات المشاريع'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'طلبات المشاريع العقارية',
    'subtitle' => 'مراجعة طلبات الإضافة والتعديل والحذف من فريق المبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بانتظار الموافقة', 'value' => $stats['pending'], 'accent' => 'amber', 'compact' => true, 'href' => route('crm.projects.approvals.index') . '#page-data', 'linkLabel' => 'عرض المعلّقة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'معتمدة الشهر', 'value' => $stats['approved'], 'accent' => 'green', 'compact' => true, 'href' => route('crm.projects.approvals.index', ['status' => 'approved']) . '#page-data', 'linkLabel' => 'عرض المعتمدة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مرفوضة الشهر', 'value' => $stats['rejected'], 'accent' => 'red', 'compact' => true, 'href' => route('crm.projects.approvals.index', ['status' => 'rejected']) . '#page-data', 'linkLabel' => 'عرض المرفوضة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b flex flex-wrap gap-2 font-tajawal">
        <a href="<?php echo e(route('crm.projects.approvals.index')); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?php echo e(!request('status') ? 'text-white' : 'bg-gray-100 text-gray-600'); ?>"
           <?php if(!request('status')): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>معلّقة</a>
        <a href="<?php echo e(route('crm.projects.approvals.index', ['status' => 'approved'])); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?php echo e(request('status')==='approved' ? 'text-white' : 'bg-gray-100 text-gray-600'); ?>"
           <?php if(request('status')==='approved'): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>معتمدة</a>
        <a href="<?php echo e(route('crm.projects.approvals.index', ['status' => 'rejected'])); ?>" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?php echo e(request('status')==='rejected' ? 'text-white' : 'bg-gray-100 text-gray-600'); ?>"
           <?php if(request('status')==='rejected'): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>مرفوضة</a>
    </div>
    <div class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('crm.projects.approvals.show', $req)); ?>" class="block px-5 py-4 hover:bg-gray-50 font-tajawal">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="font-semibold text-gray-900"><?php echo e($req->summary); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($req->actionLabel()); ?> — <?php echo e($req->requester?->name); ?> — <?php echo e($req->created_at->diffForHumans()); ?></p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                    <?php if($req->statusColor()==='amber'): ?> bg-amber-100 text-amber-800
                    <?php elseif($req->statusColor()==='green'): ?> bg-green-100 text-green-800
                    <?php elseif($req->statusColor()==='red'): ?> bg-red-100 text-red-800
                    <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>"><?php echo e($req->statusLabel()); ?></span>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="p-8 text-center text-sm text-gray-500 font-tajawal">لا توجد طلبات في هذا القسم.</p>
        <?php endif; ?>
    </div>
    <?php if($requests->hasPages()): ?>
    <div class="px-5 py-4 border-t"><?php echo e($requests->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\projects\approvals\index.blade.php ENDPATH**/ ?>
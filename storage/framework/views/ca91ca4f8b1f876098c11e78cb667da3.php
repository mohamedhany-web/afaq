
<?php $__env->startSection('page-title', 'حملات التسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'حملات التسويق',
    'subtitle' => 'إدارة الحملات والقنوات والميزانيات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4h10m-10 0a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2" />',
    'actionUrl' => auth()->user()->can('create-marketing') ? route('marketing.campaigns.create') : null,
    'actionLabel' => 'حملة جديدة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الحملات', 'value' => $stats['total'], 'accent' => 'purple', 'href' => route('marketing.campaigns.index') . '#page-data', 'linkLabel' => 'عرض الحملات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نشطة', 'value' => $stats['active'], 'accent' => 'green', 'href' => route('marketing.campaigns.index', ['status' => 'active']) . '#page-data', 'linkLabel' => 'عرض النشطة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'Leads', 'value' => $stats['leads'], 'accent' => 'blue', 'href' => route('marketing.leads.index'), 'linkLabel' => 'عرض Leads'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'الميزانية', 'value' => number_format($stats['budget']), 'accent' => 'amber', 'href' => route('marketing.campaigns.index') . '#page-data', 'linkLabel' => 'عرض الحملات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث..." class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
    <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm font-tajawal">
        <option value="">كل الحالات</option>
        <?php $__currentLoopData = config('marketing.campaign_statuses'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-tajawal" style="background: <?php echo e($themeColor); ?>;">تصفية</button>
</form>

<div id="page-data" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 font-tajawal">
        <div class="flex justify-between items-start gap-2 mb-3">
            <h3 class="font-bold text-gray-900"><?php echo e($campaign->name); ?></h3>
            <span class="text-xs px-2 py-1 rounded-lg bg-purple-50 text-purple-700"><?php echo e($campaign->statusLabel()); ?></span>
        </div>
        <p class="text-xs text-gray-500 mb-3"><?php echo e($campaign->channelLabel()); ?> <?php if($campaign->project): ?> · <?php echo e($campaign->project->name); ?> <?php endif; ?></p>
        <div class="flex justify-between text-sm mb-4">
            <span><?php echo e($campaign->leads_count); ?> lead</span>
            <span><?php echo e(number_format($campaign->budget ?? 0)); ?> ج.م</span>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('marketing.campaigns.show', $campaign)); ?>" class="flex-1 text-center py-2 rounded-xl text-xs font-bold text-white" style="background: <?php echo e($themeColor); ?>;">عرض</a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-marketing')): ?>
            <a href="<?php echo e(route('marketing.campaigns.edit', $campaign)); ?>" class="px-3 py-2 rounded-xl text-xs border border-gray-200">تعديل</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="col-span-full text-center text-gray-500 font-tajawal py-10">لا توجد حملات.</p>
    <?php endif; ?>
</div>
<div class="mt-6"><?php echo e($campaigns->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\campaigns\index.blade.php ENDPATH**/ ?>
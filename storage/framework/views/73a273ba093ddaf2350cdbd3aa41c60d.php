<?php $__env->startSection('page-title', 'مسار المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'مسار المبيعات',
    'subtitle' => 'اختر عميلاً لفتح مساره — السحب والإفلات وتسجيل البيانات داخل صفحة العميل',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
    'actionUrl' => route('crm.clients.create'),
    'actionLabel' => 'عميل جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'العملاء', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'محتملون', 'value' => $stats['prospect'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نشطون', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'لديهم صفقات', 'value' => $stats['with_deals'], 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم، الهاتف، البريد، أو الشركة..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">حالة العميل</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل الحالات</option>
                <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(request('status') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">مرحلة الرحلة</label>
            <select name="lead_stage" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل المراحل</option>
                <?php $__currentLoopData = $stageLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if(request('lead_stage') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">بحث</button>
            <?php if(request()->hasAny(['search', 'status', 'lead_stage', 'has_deals', 'deal_stage'])): ?>
            <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة العملاء</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($clients->total()); ?> عميل</span>
    </div>

    <div class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('crm.pipeline.client', $client)); ?>"
           class="flex items-center gap-4 px-5 sm:px-6 py-4 hover:bg-gray-50/80 transition group">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 text-white font-bold text-sm font-tajawal"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>cc 100%);">
                <?php echo e(mb_substr($client->name, 0, 1)); ?>

            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-900 font-tajawal group-hover:underline truncate"><?php echo e($client->name); ?></p>
                <p class="text-sm text-gray-500 font-tajawal truncate" dir="ltr"><?php echo e($client->phone); ?></p>
                <div class="mt-1 hidden sm:block">
                    <?php echo $__env->make('crm.clients.partials.created-by', ['client' => $client], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <div class="hidden sm:flex flex-col items-end gap-1 shrink-0">
                <?php echo $__env->make('crm.clients.partials.status-badge', ['status' => $client->status], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span class="text-xs px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 font-semibold font-tajawal">
                    <?php echo e($stageLabels[$client->lead_stage] ?? $client->lead_stage); ?>

                </span>
            </div>
            <div class="text-left shrink-0">
                <?php if($client->scoped_sales_count > 0): ?>
                <span class="text-xs font-bold font-tajawal px-2.5 py-1 rounded-lg" style="background: <?php echo e($themeColor); ?>12; color: <?php echo e($themeColor); ?>;">
                    <?php echo e($client->scoped_sales_count); ?> صفقة
                </span>
                <?php else: ?>
                <span class="text-xs text-gray-400 font-tajawal">بدون صفقات</span>
                <?php endif; ?>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 mt-1 mr-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="py-16 text-center text-gray-400 font-tajawal">
            <p class="mb-4">لا يوجد عملاء مطابقون</p>
            <a href="<?php echo e(route('crm.clients.create')); ?>" class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold"
               style="background: <?php echo e($themeColor); ?>;">إضافة عميل</a>
        </div>
        <?php endif; ?>
    </div>

    <?php if($clients->hasPages()): ?>
    <div class="px-5 py-4 border-t border-gray-100"><?php echo e($clients->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/pipeline/index.blade.php ENDPATH**/ ?>
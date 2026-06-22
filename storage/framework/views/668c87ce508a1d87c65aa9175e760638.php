
<?php $__env->startSection('page-title', 'خطط التسويق الشهرية'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);"; ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'خطط التسويق الشهرية',
    'subtitle' => 'توصيف الخطة وتوزيع المهام على الفريق خلال الشهر',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    'actionUrl' => $isManager && auth()->user()->can('create-marketing') ? route('marketing.plans.create') : null,
    'actionLabel' => 'خطة شهرية جديدة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<form method="GET" class="mb-4 flex gap-2 items-center font-tajawal text-sm">
    <select name="year" class="border-2 border-gray-200 rounded-xl px-3 py-2">
        <?php for($y = now()->year + 1; $y >= now()->year - 2; $y--): ?>
        <option value="<?php echo e($y); ?>" <?php if($year == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
        <?php endfor; ?>
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-white" style="background:<?php echo e($themeColor); ?>">عرض</button>
</form>

<?php if($activePlan): ?>
<div class="mb-6 p-5 rounded-2xl border-2 font-tajawal" style="border-color:<?php echo e($themeColor); ?>40;background:<?php echo e($themeColor); ?>08">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold text-gray-500 mb-1">الخطة الحالية</p>
            <h2 class="text-lg font-bold text-gray-900"><?php echo e($activePlan->title); ?></h2>
            <p class="text-sm text-gray-600 mt-1"><?php echo e($activePlan->periodLabel()); ?> · <?php echo e($activePlan->statusLabel()); ?></p>
        </div>
        <a href="<?php echo e(route('marketing.plans.show', $activePlan)); ?>" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:<?php echo e($themeColor); ?>">فتح الخطة والتقويم</a>
    </div>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">خطط <?php echo e($year); ?></div>
    <div class="divide-y divide-gray-100 font-tajawal">
        <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('marketing.plans.show', $plan)); ?>" class="block px-5 py-4 hover:bg-gray-50">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-semibold text-gray-900"><?php echo e($plan->title); ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e($plan->periodLabel()); ?> · <?php echo e($plan->activities_count); ?> مهمة · <?php echo e($plan->statusLabel()); ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-left">
                        <div class="text-xs text-gray-500">التقدم</div>
                        <div class="font-bold text-sm" style="color:<?php echo e($themeColor); ?>"><?php echo e($plan->progressPercent()); ?>%</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-lg <?php echo e($plan->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'); ?>"><?php echo e($plan->statusLabel()); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="p-8 text-center text-gray-500 text-sm">لا توجد خطط لهذا العام.<?php if($isManager): ?> <a href="<?php echo e(route('marketing.plans.create')); ?>" class="font-bold" style="color:<?php echo e($themeColor); ?>">أنشئ خطة شهرية</a><?php endif; ?></p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\plans\index.blade.php ENDPATH**/ ?>
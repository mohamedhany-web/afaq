
<?php $__env->startSection('page-title', 'الجدول الدوري'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'جدول المهام التسويقية',
    'subtitle' => 'مهام يومية / أسبوعية / شهرية — دورية',
    'actionUrl' => auth()->user()->can('create-marketing') ? route('marketing.activities.create') : null,
    'actionLabel' => 'مهمة جديدة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-3 gap-3 mb-4">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'purple'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'دورية', 'value' => $stats['recurring'], 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2 items-center font-tajawal text-sm">
    <input type="date" name="date" value="<?php echo e($date->format('Y-m-d')); ?>" class="border-2 border-gray-200 rounded-xl px-3 py-2">
    <select name="view" class="border-2 border-gray-200 rounded-xl px-3 py-2">
        <option value="week" <?php if($view === 'week'): echo 'selected'; endif; ?>>أسبوع</option>
        <option value="day" <?php if($view === 'day'): echo 'selected'; endif; ?>>يوم</option>
    </select>
    <?php if(count($assignableUsers)): ?>
    <select name="assigned_to" class="border-2 border-gray-200 rounded-xl px-3 py-2">
        <option value="">كل الفريق</option>
        <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($u->id); ?>" <?php if(request('assigned_to') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php endif; ?>
    <button type="submit" class="px-4 py-2 rounded-xl text-white" style="background:<?php echo e($themeColor); ?>">عرض</button>
</form>

<div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
    <div class="divide-y divide-gray-100 font-tajawal">
        <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 <?php echo e($activity->isOverdue() ? 'bg-amber-50/50' : ''); ?>">
            <div>
                <p class="font-semibold text-gray-900"><?php echo e($activity->title); ?></p>
                <p class="text-xs text-gray-500 mt-1">
                    <?php echo e($activity->due_at?->locale('ar')->translatedFormat('d M Y — H:i') ?? '—'); ?>

                    · <?php echo e($activity->typeLabel()); ?>

                    · <?php echo e($activity->assignee?->name ?? '—'); ?>

                    <?php if($activity->recurrence !== 'none'): ?> · <span class="text-purple-600"><?php echo e($activity->recurrenceLabel()); ?></span> <?php endif; ?>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-1 rounded-lg bg-gray-100"><?php echo e($activity->statusLabel()); ?></span>
                <?php if($activity->status !== 'completed'): ?>
                <form action="<?php echo e(route('marketing.activities.update-status', $activity)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg text-white" style="background:<?php echo e($themeColor); ?>">إتمام</button>
                </form>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-marketing')): ?>
                <a href="<?php echo e(route('marketing.activities.edit', $activity)); ?>" class="text-xs px-3 py-1.5 rounded-lg border">تعديل</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="p-8 text-center text-gray-500">لا مهام في هذه الفترة.</p>
        <?php endif; ?>
    </div>
</div>
<div class="mt-4"><?php echo e($activities->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/marketing/activities/index.blade.php ENDPATH**/ ?>
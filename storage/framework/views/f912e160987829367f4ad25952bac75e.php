
<?php $__env->startSection('page-title', 'جدول المهام التسويقية'); ?>

<?php $__env->startSection('content'); ?>
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);"; ?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'جدول المهام التسويقية',
    'subtitle' => 'مهام يومية / أسبوعية / شهرية — بين مدير التسويق والفريق',
    'actionUrl' => auth()->user()->can('create-marketing') ? route('marketing.activities.create', request()->only('marketing_plan_id')) : null,
    'actionLabel' => 'مهمة جديدة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['today'], 'accent' => 'purple', 'compact' => true, 'href' => route('marketing.activities.index', ['filter' => 'today']) . '#page-data', 'linkLabel' => 'عرض اليوم'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'amber', 'compact' => true, 'href' => route('marketing.activities.index', ['filter' => 'overdue']) . '#page-data', 'linkLabel' => 'عرض المتأخرة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'دورية', 'value' => $stats['recurring'], 'accent' => 'blue', 'compact' => true, 'href' => route('marketing.activities.index', ['filter' => 'recurring']) . '#page-data', 'linkLabel' => 'عرض الدورية'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if($isManager): ?>
    <a href="<?php echo e(route('marketing.plans.index')); ?>" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 hover:shadow-xl transition-all flex flex-col justify-center font-tajawal h-full min-h-[108px]">
        <span class="text-xs text-gray-500">خطط الشهر</span>
        <span class="text-sm font-bold mt-1" style="color:<?php echo e($themeColor); ?>">خطة التسويق ←</span>
    </a>
    <?php else: ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مهامي', 'value' => $activities->total(), 'accent' => 'theme', 'compact' => true, 'href' => route('marketing.activities.index') . '#page-data', 'linkLabel' => 'عرض القائمة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</div>

<form method="GET" class="mb-4 bg-white rounded-2xl shadow-lg border border-gray-200 p-4 flex flex-wrap gap-2 items-end font-tajawal text-sm">
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ</label>
        <input type="date" name="date" value="<?php echo e($date->format('Y-m-d')); ?>" class="border-2 border-gray-200 rounded-xl px-3 py-2">
    </div>
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">العرض</label>
        <select name="view" class="border-2 border-gray-200 rounded-xl px-3 py-2">
            <option value="week" <?php if($view === 'week'): echo 'selected'; endif; ?>>أسبوع</option>
            <option value="day" <?php if($view === 'day'): echo 'selected'; endif; ?>>يوم</option>
            <option value="month" <?php if($view === 'month'): echo 'selected'; endif; ?>>شهر</option>
        </select>
    </div>
    <?php if(count($plans)): ?>
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">خطة الشهر</label>
        <select name="marketing_plan_id" class="border-2 border-gray-200 rounded-xl px-3 py-2 min-w-[160px]">
            <option value="">كل المهام</option>
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->id); ?>" <?php if(request('marketing_plan_id') == $p->id): echo 'selected'; endif; ?>><?php echo e($p->title); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>
    <?php if(count($assignableUsers)): ?>
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1">الموظف</label>
        <select name="assigned_to" class="border-2 border-gray-200 rounded-xl px-3 py-2">
            <option value="">كل الفريق</option>
            <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>" <?php if(request('assigned_to') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>
    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background:<?php echo e($themeColor); ?>">عرض</button>
</form>

<?php if($view === 'month' && !empty($monthCalendar)): ?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">تقويم <?php echo e($date->locale('ar')->translatedFormat('F Y')); ?></div>
    <div class="p-4 grid grid-cols-7 gap-2 font-tajawal">
        <?php for($d = 1; $d <= $date->daysInMonth; $d++): ?>
        <?php $tasks = $monthCalendar[$d] ?? collect(); ?>
        <div class="min-h-[80px] rounded-xl border border-gray-100 p-2 bg-gray-50/40 <?php echo e($date->day === $d ? 'ring-2' : ''); ?>" <?php if($date->day === $d): ?> style="ring-color:<?php echo e($themeColor); ?>" <?php endif; ?>>
            <div class="text-xs font-bold text-gray-600 mb-1"><?php echo e($d); ?></div>
            <?php $__currentLoopData = $tasks->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="text-[10px] truncate px-1 py-0.5 rounded bg-white border mb-0.5" title="<?php echo e($t->title); ?>"><?php echo e(Str::limit($t->title, 10)); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">قائمة المهام</div>
    <div class="divide-y divide-gray-100 font-tajawal">
        <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 <?php echo e($activity->isOverdue() ? 'bg-amber-50/50' : ''); ?>">
            <div>
                <p class="font-semibold text-gray-900"><?php echo e($activity->title); ?></p>
                <p class="text-xs text-gray-500 mt-1">
                    <?php echo e($activity->due_at?->locale('ar')->translatedFormat('d M Y — H:i') ?? '—'); ?>

                    · <?php echo e($activity->typeLabel()); ?>

                    · <?php echo e($activity->assignee?->name ?? '—'); ?>

                    <?php if($activity->plan): ?> · <a href="<?php echo e(route('marketing.plans.show', $activity->plan)); ?>" class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e(Str::limit($activity->plan->title, 20)); ?></a> <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\activities\index.blade.php ENDPATH**/ ?>
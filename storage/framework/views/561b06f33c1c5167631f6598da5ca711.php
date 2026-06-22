
<?php $__env->startSection('page-title', $plan->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 font-tajawal text-sm';
    $daysInMonth = \Carbon\Carbon::create($plan->year, $plan->month, 1)->daysInMonth;
    $firstDow = \Carbon\Carbon::create($plan->year, $plan->month, 1)->dayOfWeek; // 0=Sun
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $plan->title,
    'subtitle' => $plan->periodLabel() . ' — ' . $plan->statusLabel(),
    'actionUrl' => route('marketing.plans.index'),
    'actionLabel' => 'كل الخطط',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي المهام', 'value' => $stats['total'], 'accent' => 'theme', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'مكتملة', 'value' => $stats['completed'], 'accent' => 'green', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'accent' => 'amber', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'التقدم', 'value' => $plan->progressPercent() . '%', 'accent' => 'purple', 'compact' => true, 'href' => '#plan-tasks', 'linkLabel' => 'عرض المهام'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($isManager): ?>
<div class="flex flex-wrap gap-2 mb-6 font-tajawal">
    <?php if($plan->status !== 'active'): ?>
    <form action="<?php echo e(route('marketing.plans.activate', $plan)); ?>" method="POST"><?php echo csrf_field(); ?>
        <button type="submit" class="px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold">تفعيل الخطة</button>
    </form>
    <?php endif; ?>
    <a href="<?php echo e(route('marketing.plans.edit', $plan)); ?>" class="px-4 py-2 rounded-xl border text-sm font-semibold">تعديل التوصيف</a>
    <a href="<?php echo e(route('marketing.activities.index', ['view' => 'month', 'date' => $plan->year.'-'.str_pad($plan->month,2,'0',STR_PAD_LEFT).'-01', 'marketing_plan_id' => $plan->id])); ?>" class="px-4 py-2 rounded-xl text-white text-sm font-semibold" style="background:<?php echo e($themeColor); ?>">عرض جدول الشهر</a>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">توصيف الخطة</div>
        <div class="p-5 sm:p-6 space-y-4 text-sm font-tajawal text-gray-700 leading-relaxed">
            <div>
                <h3 class="text-xs font-bold text-gray-500 mb-2 uppercase">الوصف</h3>
                <p class="whitespace-pre-wrap"><?php echo e($plan->description ?: '—'); ?></p>
            </div>
            <?php if($plan->objectives): ?>
            <div>
                <h3 class="text-xs font-bold text-gray-500 mb-2 uppercase">الأهداف</h3>
                <p class="whitespace-pre-wrap"><?php echo e($plan->objectives); ?></p>
            </div>
            <?php endif; ?>
            <?php if($plan->campaign): ?>
            <p class="text-xs text-gray-500">الحملة: <a href="<?php echo e(route('marketing.campaigns.show', $plan->campaign)); ?>" class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($plan->campaign->name); ?></a></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">ملخص الفريق</div>
        <div class="p-5 space-y-2 text-sm font-tajawal">
            <p><span class="text-gray-500">مدير الخطة:</span> <strong><?php echo e($plan->manager?->name ?? '—'); ?></strong></p>
            <?php $byUser = $plan->activities->groupBy('assigned_to'); ?>
            <?php $__currentLoopData = $byUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uid => $tasks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between py-2 border-b border-gray-50">
                <span><?php echo e($tasks->first()->assignee?->name ?? 'غير معيّن'); ?></span>
                <span class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($tasks->count()); ?> مهمة</span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($byUser->isEmpty()): ?><p class="text-gray-400 text-center py-4">لم تُوزَّع مهام بعد</p><?php endif; ?>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b font-bold font-tajawal flex justify-between items-center" style="<?php echo e($headerStyle); ?>">
        <span>توزيع المهام على الشهر</span>
        <span class="text-xs text-gray-500 font-normal"><?php echo e($plan->periodLabel()); ?></span>
    </div>
    <div class="p-4 sm:p-5">
        <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center text-xs font-bold text-gray-500 mb-2 font-tajawal">
            <?php $__currentLoopData = ['أحد','إثن','ثلا','أرب','خمي','جمع','سبت']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($dn); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="grid grid-cols-7 gap-1 sm:gap-2 font-tajawal">
            <?php for($i = 0; $i < $firstDow; $i++): ?><div class="min-h-[72px]"></div><?php endfor; ?>
            <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
            <?php $dayTasks = $calendar[$day] ?? collect(); $isToday = now()->year == $plan->year && now()->month == $plan->month && now()->day == $day; ?>
            <div class="min-h-[72px] sm:min-h-[88px] rounded-xl border p-1.5 text-right <?php echo e($isToday ? 'border-2' : 'border-gray-100 bg-gray-50/50'); ?>" <?php if($isToday): ?> style="border-color:<?php echo e($themeColor); ?>" <?php endif; ?>>
                <div class="text-[10px] sm:text-xs font-bold text-gray-600 mb-1"><?php echo e($day); ?></div>
                <?php $__currentLoopData = $dayTasks->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-[9px] sm:text-[10px] truncate px-1 py-0.5 rounded mb-0.5 <?php echo e($t->status === 'completed' ? 'bg-green-100 text-green-800' : ($t->isOverdue() ? 'bg-amber-100 text-amber-900' : 'bg-white text-gray-700 border border-gray-100')); ?>" title="<?php echo e($t->title); ?>">
                    <?php echo e(Str::limit($t->title, 12)); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($dayTasks->count() > 3): ?><div class="text-[9px] text-gray-400">+<?php echo e($dayTasks->count() - 3); ?></div><?php endif; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<?php if($isManager): ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" x-data="{ rows: [{ title: '', assigned_to: '', due_day: 1, type: 'content', priority: 'medium' }] }">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">إضافة مهام للخطة</div>
        <form action="<?php echo e(route('marketing.plans.tasks.store', $plan)); ?>" method="POST" class="p-5 space-y-3">
            <?php echo csrf_field(); ?>
            <template x-for="(row, idx) in rows" :key="idx">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="sm:col-span-5">
                        <input type="text" :name="'tasks['+idx+'][title]'" x-model="row.title" required class="<?php echo e($input); ?>" placeholder="عنوان المهمة">
                    </div>
                    <div class="sm:col-span-3">
                        <select :name="'tasks['+idx+'][assigned_to]'" x-model="row.assigned_to" required class="<?php echo e($input); ?>">
                            <option value="">الموظف</option>
                            <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <input type="number" :name="'tasks['+idx+'][due_day]'" x-model="row.due_day" min="1" max="31" required class="<?php echo e($input); ?>" placeholder="يوم">
                    </div>
                    <div class="sm:col-span-2 flex gap-1">
                        <input type="hidden" :name="'tasks['+idx+'][type]'" value="content">
                        <input type="hidden" :name="'tasks['+idx+'][priority]'" value="medium">
                        <button type="button" @click="rows.splice(idx,1)" x-show="rows.length > 1" class="px-2 text-red-500 text-xs">حذف</button>
                    </div>
                </div>
            </template>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="rows.push({ title:'', assigned_to:'', due_day:1, type:'content', priority:'medium' })" class="px-3 py-1.5 rounded-lg border text-xs font-bold">+ مهمة</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-white text-sm font-semibold mr-auto" style="background:<?php echo e($themeColor); ?>">حفظ المهام</button>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">توزيع تلقائي على الشهر</div>
        <form action="<?php echo e(route('marketing.plans.distribute', $plan)); ?>" method="POST" class="p-5 space-y-4 font-tajawal text-sm">
            <?php echo csrf_field(); ?>
            <p class="text-gray-600 text-xs">اكتب كل مهمة في سطر — يُوزَّعها النظام على أيام الشهر بين الموظفين المحددين.</p>
            <textarea name="task_lines" rows="6" required class="<?php echo e($input); ?>" placeholder="منشور فيسبوك أسبوعي&#10;حملة بريد إلكتروني&#10;تقرير أداء أسبوعي"></textarea>
            <div>
                <p class="text-xs font-bold text-gray-500 mb-2">الموظفون</p>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                    <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="employee_ids[]" value="<?php echo e($u->id); ?>" class="rounded">
                        <span><?php echo e($u->name); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-xl text-white font-semibold" style="background:linear-gradient(135deg,<?php echo e($themeColor); ?> 0%,<?php echo e($themeColor); ?>dd 100%)">توزيع على الشهر</button>
        </form>
    </div>
</div>
<?php endif; ?>


<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b font-bold font-tajawal" style="<?php echo e($headerStyle); ?>">كل مهام الخطة</div>
    <div class="divide-y divide-gray-100 font-tajawal">
        <?php $__empty_1 = true; $__currentLoopData = $plan->activities->sortBy('due_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="px-5 py-3 flex flex-wrap items-center justify-between gap-2 <?php echo e($activity->isOverdue() ? 'bg-amber-50/40' : ''); ?>">
            <div>
                <p class="font-semibold text-gray-900 text-sm"><?php echo e($activity->title); ?></p>
                <p class="text-xs text-gray-500"><?php echo e($activity->due_at?->format('Y/m/d')); ?> · <?php echo e($activity->assignee?->name); ?> · <?php echo e($activity->statusLabel()); ?></p>
            </div>
            <div class="flex gap-2">
                <?php if($activity->status !== 'completed'): ?>
                <form action="<?php echo e(route('marketing.activities.update-status', $activity)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="status" value="completed">
                    <button class="text-xs px-3 py-1 rounded-lg text-white" style="background:<?php echo e($themeColor); ?>">إتمام</button>
                </form>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-marketing')): ?>
                <a href="<?php echo e(route('marketing.activities.edit', $activity)); ?>" class="text-xs px-3 py-1 rounded-lg border">تعديل</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="p-8 text-center text-gray-500 text-sm">لا مهام في هذه الخطة بعد.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\marketing\plans\show.blade.php ENDPATH**/ ?>

<?php $__env->startSection('page-title', $task->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $pColors = config('crm_tasks.priority_colors', []);
    $phone = $task->client?->phone;
    $wa = $phone ? 'https://wa.me/' . preg_replace('/\D+/', '', $phone) : null;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $task->title,
    'subtitle' => $task->categoryLabel() . ' · ' . $task->priorityLabel() . ' · ' . $task->statusLabel(),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />',
    'actionUrl' => $canManage ? route('crm.tasks.edit', $task) : null,
    'actionLabel' => 'تعديل',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6">
    <div class="xl:col-span-8 space-y-4">
        <div class="bg-white rounded-2xl border shadow-lg p-5 sm:p-6">
            <?php if($task->description): ?><p class="text-gray-700 font-tajawal leading-relaxed mb-4"><?php echo e($task->description); ?></p><?php endif; ?>
            <dl class="grid sm:grid-cols-2 gap-3 text-sm font-tajawal">
                <div><dt class="text-gray-500 text-xs">المكلف</dt><dd class="font-bold"><?php echo e($task->assignee?->name); ?></dd></div>
                <div><dt class="text-gray-500 text-xs">المُعيِّن</dt><dd><?php echo e($task->assigner?->name ?? 'النظام'); ?></dd></div>
                <div><dt class="text-gray-500 text-xs">الموعد النهائي</dt><dd class="font-bold tabular-nums <?php echo e($task->isOverdue() ? 'text-red-600' : ''); ?>"><?php echo e($task->due_at->format('Y/m/d H:i')); ?></dd></div>
                <?php if($task->performance_score): ?><div><dt class="text-gray-500 text-xs">درجة الأداء</dt><dd class="font-bold text-green-700"><?php echo e($task->performance_score); ?>%</dd></div><?php endif; ?>
            </dl>
            <?php if($task->completion_notes): ?>
            <div class="mt-4 p-4 rounded-xl bg-green-50 border border-green-100">
                <p class="text-xs font-bold text-green-800 mb-1">ملاحظات الإنجاز</p>
                <p class="text-sm text-green-900 font-tajawal"><?php echo e($task->completion_notes); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <?php if($task->client && $isAssignee): ?>
        <div class="bg-white rounded-2xl border p-4 flex flex-wrap gap-2 font-tajawal">
            <span class="text-xs font-bold text-gray-500 w-full mb-1">إجراءات سريعة</span>
            <?php if($phone): ?><a href="tel:<?php echo e($phone); ?>" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold">اتصال</a><?php endif; ?>
            <?php if($wa): ?><a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-xl bg-green-600 text-white text-xs font-bold">واتساب</a><?php endif; ?>
            <a href="<?php echo e(route('crm.follow-ups.index')); ?>" class="px-4 py-2 rounded-xl border text-xs font-bold">جدولة متابعة</a>
            <?php if($task->sale_id): ?><a href="<?php echo e(route('crm.pipeline.show', $task->sale_id)); ?>" class="px-4 py-2 rounded-xl border text-xs font-bold">الصفقة</a><?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border overflow-hidden">
            <div class="px-5 py-3 border-b" style="<?php echo e($headerStyle); ?>"><h3 class="font-bold font-tajawal">سجل التتبع</h3></div>
            <ul class="divide-y font-tajawal text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $task->logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3">
                    <span class="font-semibold"><?php echo e($log->action); ?></span>
                    <?php if($log->old_status && $log->new_status): ?><span class="text-gray-500"> <?php echo e($log->old_status); ?> → <?php echo e($log->new_status); ?></span><?php endif; ?>
                    <span class="text-gray-400 text-xs block"><?php echo e($log->created_at->format('Y/m/d H:i')); ?> — <?php echo e($log->user?->name ?? 'النظام'); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="p-6 text-center text-gray-400">لا سجل بعد</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="xl:col-span-4 space-y-4">
        <div class="bg-white rounded-2xl border p-5 font-tajawal space-y-3">
            <h3 class="font-bold text-sm">الإجراءات</h3>
            <?php if($isAssignee): ?>
                <?php if($task->status === 'pending' && $task->requires_acceptance): ?>
                <form method="POST" action="<?php echo e(route('crm.tasks.accept', $task)); ?>"><?php echo csrf_field(); ?><button class="w-full py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">قبول المهمة</button></form>
                <?php endif; ?>
                <?php if(in_array($task->status, ['pending','accepted','overdue'])): ?>
                <form method="POST" action="<?php echo e(route('crm.tasks.start', $task)); ?>"><?php echo csrf_field(); ?><button class="w-full py-2.5 rounded-xl border-2 text-sm font-bold" style="border-color:<?php echo e($themeColor); ?>;color:<?php echo e($themeColor); ?>">بدء التنفيذ</button></form>
                <?php endif; ?>
                <?php if(in_array($task->status, ['in_progress','accepted','overdue','pending'])): ?>
                <form method="POST" action="<?php echo e(route('crm.tasks.complete', $task)); ?>" class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <textarea name="completion_notes" rows="3" class="w-full border-2 rounded-xl px-3 py-2 text-sm" placeholder="ملاحظات الإنجاز (إلزامية)..." required minlength="10"></textarea>
                    <button class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-bold">إكمال المهمة</button>
                </form>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($canVerify && $task->status === 'completed'): ?>
            <form method="POST" action="<?php echo e(route('crm.tasks.verify', $task)); ?>"><?php echo csrf_field(); ?><button class="w-full py-2.5 rounded-xl bg-purple-600 text-white text-sm font-bold">تحقق المدير</button></form>
            <?php endif; ?>
            <?php if($canManage && !in_array($task->status, ['archived','cancelled'])): ?>
            <form method="POST" action="<?php echo e(route('crm.tasks.cancel', $task)); ?>" onsubmit="return confirm('إلغاء المهمة؟')"><?php echo csrf_field(); ?><button class="w-full py-2 text-xs text-red-600 border border-red-200 rounded-lg">إلغاء</button></form>
            <?php endif; ?>
        </div>
        <?php if($task->client): ?>
        <div class="bg-white rounded-2xl border p-4 text-sm font-tajawal">
            <p class="text-xs text-gray-500">العميل</p>
            <a href="<?php echo e(route('crm.clients.show', $task->client)); ?>" class="font-bold" style="color:<?php echo e($themeColor); ?>"><?php echo e($task->client->name); ?></a>
        </div>
        <?php endif; ?>
    </div>
</div>
<a href="<?php echo e(route('crm.tasks.index')); ?>" class="inline-block mt-6 text-sm font-tajawal" style="color:<?php echo e($themeColor); ?>">← كل المهام</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/tasks/show.blade.php ENDPATH**/ ?>
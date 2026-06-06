
<?php $__env->startSection('page-title', 'إدارة المهام'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $isAdmin = $viewMode === \App\Services\CrmRoleResolver::WORKSPACE_ADMIN;
    $isManager = $viewMode === \App\Services\CrmRoleResolver::WORKSPACE_MANAGER;
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $isAdmin ? 'مهام الشركة' : ($isManager ? 'مهام الفريق' : 'مهامي'),
    'subtitle' => 'تعيين وتتبع وإنجاز المهام المرتبطة بالمبيعات',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
    'actionUrl' => $canCreate ? route('crm.tasks.create') : null,
    'actionLabel' => 'مهمة جديدة',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
<div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-800 text-sm font-tajawal border border-green-200"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نشطة', 'value' => $stats['total_active'], 'compact' => true, 'accent' => 'theme'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'اليوم', 'value' => $stats['due_today'], 'compact' => true, 'accent' => 'blue'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'متأخرة', 'value' => $stats['overdue'], 'compact' => true, 'accent' => 'red'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حرجة', 'value' => $stats['critical'], 'compact' => true, 'accent' => 'amber'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'أُنجزت هذا الأسبوع', 'value' => $stats['completed_week'], 'compact' => true, 'accent' => 'green'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php if($isManager || $isAdmin): ?>
<div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden mb-6">
    <div class="px-5 py-3 border-b" style="<?php echo e($headerStyle); ?>"><h3 class="font-bold font-tajawal text-sm">إنتاجية الفريق (7 أيام)</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm font-tajawal">
            <thead class="bg-gray-50 text-xs text-gray-500"><tr>
                <th class="text-right p-3">الموظف</th><th class="text-center p-3">مكتمل</th><th class="text-center p-3">متأخر</th><th class="text-center p-3">نشط</th><th class="text-center p-3">متوسط الأداء</th>
            </tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $teamProductivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t <?php echo e($row['overloaded'] ? 'bg-amber-50' : ''); ?>">
                    <td class="p-3 font-semibold"><?php echo e($row['user']->name); ?> <?php if($row['overloaded']): ?><span class="text-[10px] text-amber-700">حمّل زائد</span><?php endif; ?></td>
                    <td class="p-3 text-center"><?php echo e($row['completed_week']); ?></td>
                    <td class="p-3 text-center text-red-600 font-bold"><?php echo e($row['overdue']); ?></td>
                    <td class="p-3 text-center"><?php echo e($row['open']); ?></td>
                    <td class="p-3 text-center"><?php echo e($row['avg_score']); ?>%</td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="p-6 text-center text-gray-400">لا بيانات فريق</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-2 items-end">
        <div class="flex flex-wrap gap-1">
            <?php $__currentLoopData = ['active' => 'النشطة', 'today' => 'اليوم', 'overdue' => 'متأخرة', 'critical' => 'حرجة', 'high' => 'عالية+', 'completed' => 'مكتملة']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('crm.tasks.index', ['filter' => $key])); ?>"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold <?php echo e(($filter ?? 'active') === $key ? 'text-white' : 'bg-gray-100 text-gray-600'); ?>"
               <?php if(($filter ?? 'active') === $key): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>><?php echo e($lbl); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($assignableUsers->isNotEmpty()): ?>
        <select name="assignee" class="border-2 border-gray-200 rounded-xl px-3 py-2 text-xs" onchange="this.form.submit()">
            <option value="">كل المكلفين</option>
            <?php $__currentLoopData = $assignableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($u->id); ?>" <?php if(request('assignee') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php endif; ?>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php echo $__env->make('crm.tasks.partials.task-card', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-16 text-gray-400 font-tajawal">
            <p class="mb-4">لا توجد مهام في هذا العرض</p>
            <?php if($canCreate): ?><a href="<?php echo e(route('crm.tasks.create')); ?>" class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">إنشاء مهمة</a><?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php if($tasks->hasPages()): ?><div class="mt-6"><?php echo e($tasks->links()); ?></div><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/tasks/index.blade.php ENDPATH**/ ?>
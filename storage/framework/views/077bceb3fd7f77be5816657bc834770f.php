<?php $__env->startSection('page-title', 'المستخدمون'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $activeWorkspace = request('workspace');
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'إدارة المستخدمين',
    'subtitle' => 'مركز إدارة حسابات النظام — اختر القسم والدور ثم أضِف المستخدم من هنا',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'actionUrl' => auth()->user()->can('create-users') ? route('users.create') : null,
    'actionLabel' => 'مستخدم جديد',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?><div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-tajawal"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-tajawal"><?php echo e(session('error')); ?></div><?php endif; ?>

<div class="mb-4 p-4 rounded-2xl border border-gray-200 bg-white font-tajawal text-sm text-gray-600">
    جميع المستخدمين يُضافون من هذه الصفحة. اختر <strong class="text-gray-900">القسم</strong> (مبيعات · تسويق · عمليات · إلخ) ثم <strong class="text-gray-900">الدور</strong> المناسب — يُحدَّد تلقائياً القسم والصلاحيات ومساحة العمل.
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'الإجمالي',
        'value' => $stats['total'],
        'accent' => 'theme',
        'compact' => true,
        'href' => route('users.index') . '#page-data',
        'linkLabel' => 'عرض الكل',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php $__currentLoopData = $workspaceStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $wsAccent = match ($groupKey) {
            'admin' => 'purple',
            'sales' => 'blue',
            'marketing' => 'purple',
            'operations' => 'green',
            'hr' => 'purple',
            'clients' => 'green',
            default => 'theme',
        };
    ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => $group['label'],
        'value' => $group['count'],
        'accent' => $wsAccent,
        'compact' => true,
        'class' => $activeWorkspace === $groupKey ? 'ring-2' : '',
        'href' => route('users.index', ['workspace' => $groupKey]) . '#page-data',
        'linkLabel' => 'عرض ' . $group['label'],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="flex flex-wrap gap-2 mb-4 font-tajawal">
    <a href="<?php echo e(route('users.index')); ?>"
       class="px-3 py-1.5 rounded-lg text-xs font-bold border transition <?php echo e(!$activeWorkspace ? 'text-white border-transparent' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'); ?>"
       <?php if(!$activeWorkspace): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
        الكل (<?php echo e($stats['total']); ?>)
    </a>
    <?php $__currentLoopData = $workspaceGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('users.index', ['workspace' => $groupKey])); ?>"
       class="px-3 py-1.5 rounded-lg text-xs font-bold border transition <?php echo e($activeWorkspace === $groupKey ? 'text-white border-transparent' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'); ?>"
       <?php if($activeWorkspace === $groupKey): ?> style="background:<?php echo e($group['color']); ?>" <?php endif; ?>>
        <?php echo e($group['label']); ?>

        <span class="opacity-80">(<?php echo e($workspaceStats[$groupKey]['count'] ?? 0); ?>)</span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="bg-white rounded-2xl border p-4 mb-6 font-tajawal">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <?php if($activeWorkspace): ?>
        <input type="hidden" name="workspace" value="<?php echo e($activeWorkspace); ?>">
        <?php endif; ?>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">بحث</label>
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم أو البريد..." class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
        </div>
        <div class="w-full sm:w-52">
            <label class="block text-xs font-bold text-gray-500 mb-1">الدور التفصيلي</label>
            <select name="role" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">كل الأدوار</option>
                <?php $__currentLoopData = $workspaceGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $groupRoles = $assignableRoles->filter(fn ($r) => in_array($r->name, $group['roles'], true)); ?>
                <?php if($groupRoles->isNotEmpty()): ?>
                <optgroup label="<?php echo e($group['label']); ?>">
                    <?php $__currentLoopData = $groupRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role->name); ?>" <?php if(request('role') === $role->name): echo 'selected'; endif; ?>>
                        <?php echo e(\App\Services\CrmRoleCatalogService::roleLabel($role->name)); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </optgroup>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-full sm:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                <option value="">الكل</option>
                <option value="verified" <?php if(request('status') === 'verified'): echo 'selected'; endif; ?>>مفعّل</option>
                <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>بانتظار التفعيل</option>
                <option value="with_employee" <?php if(request('status') === 'with_employee'): echo 'selected'; endif; ?>>له سجل موظف</option>
                <option value="without_employee" <?php if(request('status') === 'without_employee'): echo 'selected'; endif; ?>>بدون موظف</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-bold" style="background:<?php echo e($themeColor); ?>">تطبيق</button>
        <?php if(request()->hasAny(['search', 'role', 'status', 'workspace'])): ?>
        <a href="<?php echo e(route('users.index')); ?>" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-gray-600">مسح</a>
        <?php endif; ?>
    </form>
</div>

<div id="page-data" class="bg-white rounded-2xl shadow-lg border overflow-hidden font-tajawal">
    <?php if($activeWorkspace && isset($workspaceGroups[$activeWorkspace])): ?>
    <div class="px-5 py-3 border-b text-sm flex items-center gap-2" style="background: <?php echo e($workspaceGroups[$activeWorkspace]['color']); ?>10;">
        <span class="w-2.5 h-2.5 rounded-full" style="background:<?php echo e($workspaceGroups[$activeWorkspace]['color']); ?>"></span>
        <span class="font-bold text-gray-800"><?php echo e($workspaceGroups[$activeWorkspace]['label']); ?></span>
        <span class="text-gray-500">— <?php echo e($workspaceGroups[$activeWorkspace]['description']); ?></span>
    </div>
    <?php endif; ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-right font-bold">المستخدم</th>
                    <th class="p-4 text-right font-bold">القسم</th>
                    <th class="p-4 text-right font-bold">الدور</th>
                    <th class="p-4 text-right font-bold">الموظف / القسم</th>
                    <th class="p-4 text-right font-bold">الحالة</th>
                    <th class="p-4 text-right font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $roleKey = \App\Services\CrmRoleCatalogService::resolveUserDisplayRole($user);
                $meta = $roleKey ? \App\Services\CrmRoleCatalogService::roleMeta($roleKey) : null;
                $wsKey = $roleKey ? \App\Services\CrmRoleCatalogService::workspaceGroupForRole($roleKey) : null;
                $wsMeta = $wsKey ? \App\Services\CrmRoleCatalogService::workspaceGroupMeta($wsKey) : null;
            ?>
            <tr class="hover:bg-gray-50">
                <td class="p-4">
                    <p class="font-semibold text-gray-900"><?php echo e($user->name); ?></p>
                    <p class="text-xs text-gray-500" dir="ltr"><?php echo e($user->email); ?></p>
                </td>
                <td class="p-4">
                    <?php if($wsMeta): ?>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background: <?php echo e($wsMeta['color']); ?>18; color: <?php echo e($wsMeta['color']); ?>">
                        <?php echo e($wsMeta['label']); ?>

                    </span>
                    <?php else: ?>
                    <span class="text-xs text-gray-400">—</span>
                    <?php endif; ?>
                </td>
                <td class="p-4">
                    <?php if($meta): ?>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background: <?php echo e($meta['color']); ?>18; color: <?php echo e($meta['color']); ?>"><?php echo e($meta['label']); ?></span>
                    <?php else: ?>
                    <span class="text-xs text-gray-400">—</span>
                    <?php endif; ?>
                </td>
                <td class="p-4 text-xs text-gray-600">
                    <?php if($user->employee): ?>
                        <?php echo e($user->employee->department?->name ?? '—'); ?>

                        <span class="block text-gray-400" dir="ltr"><?php echo e($user->employee->employee_id); ?></span>
                    <?php else: ?>
                        <span class="text-amber-600">بدون سجل موظف</span>
                    <?php endif; ?>
                </td>
                <td class="p-4">
                    <?php if($user->email_verified_at): ?>
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">نشط</span>
                    <?php else: ?>
                    <span class="text-xs px-2 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">معلق</span>
                    <?php endif; ?>
                </td>
                <td class="p-4">
                    <div class="flex flex-wrap gap-1.5">
                        <a href="<?php echo e(route('users.show', $user)); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white" style="background:<?php echo e($themeColor); ?>">عرض</a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-users')): ?>
                        <a href="<?php echo e(route('users.edit', $user)); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-bold border border-gray-200">تعديل</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="p-10 text-center text-gray-500">لا يوجد مستخدمون في هذا التصنيف</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($users->hasPages()): ?><div class="p-4 border-t"><?php echo e($users->links()); ?></div><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\users\index.blade.php ENDPATH**/ ?>
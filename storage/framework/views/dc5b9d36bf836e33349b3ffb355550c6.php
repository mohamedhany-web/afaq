<?php $__env->startSection('page-title', ($marketingOnly ?? false) ? 'موظفو التسويق' : 'موظفو المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $indexUrl = route('employees.index', array_filter([
        'sales_only' => ($salesOnly ?? false) ? 1 : null,
        'marketing_only' => ($marketingOnly ?? false) ? 1 : null,
        'search' => request('search'),
        'status' => request('status'),
        'crm_role' => request('crm_role'),
    ]));
    $statusLabels = [
        'active' => ['نشط', 'bg-green-100 text-green-700'],
        'inactive' => ['غير نشط', 'bg-gray-100 text-gray-600'],
        'on_leave' => ['في إجازة', 'bg-amber-100 text-amber-800'],
        'terminated' => ['منتهي', 'bg-red-100 text-red-700'],
    ];
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => ($marketingOnly ?? false) ? 'موظفو التسويق' : 'موظفو المبيعات',
    'subtitle' => ($salesDepartment->name ?? (($marketingOnly ?? false) ? 'التسويق' : 'المبيعات')) . ' — ' . (($marketingOnly ?? false) ? 'إدارة فريق التسويق والحملات' : 'إدارة مندوبي ومديري المبيعات العقارية'),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
    'actionUrl' => $canCreate ? route('employees.create', array_filter([
        'sales_only' => ($salesOnly ?? false) ? 1 : null,
        'marketing_only' => ($marketingOnly ?? false) ? 1 : null,
    ])) : null,
    'actionLabel' => 'موظف جديد',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إجمالي الموظفين', 'value' => $stats['total'], 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => 'نشطون', 'value' => $stats['active'], 'accent' => 'green', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => ($marketingOnly ?? false) ? 'مديرو تسويق' : 'مديرو مبيعات', 'value' => $stats['managers'], 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', ['label' => ($marketingOnly ?? false) ? 'موظفو تسويق' : 'مندوبو مبيعات', 'value' => $stats['agents'], 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:items-end">
        <?php if($salesOnly ?? false): ?><input type="hidden" name="sales_only" value="1"><?php endif; ?>
        <?php if($marketingOnly ?? false): ?><input type="hidden" name="marketing_only" value="1"><?php endif; ?>
        <div class="flex-1">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="الاسم، الرقم، البريد، أو الهاتف..."
                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الدور</label>
            <select name="crm_role" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">كل الأدوار</option>
                <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php if(request('crm_role') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>غير نشط</option>
                <option value="on_leave" <?php if(request('status') === 'on_leave'): echo 'selected'; endif; ?>>في إجازة</option>
                <option value="terminated" <?php if(request('status') === 'terminated'): echo 'selected'; endif; ?>>منتهي الخدمة</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal shadow-sm"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
            <?php if(request()->hasAny(['search', 'status', 'crm_role'])): ?>
            <a href="<?php echo e(route('employees.index', array_filter(['sales_only' => ($salesOnly ?? false) ? 1 : null, 'marketing_only' => ($marketingOnly ?? false) ? 1 : null]))); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between" style="<?php echo e($headerStyle); ?>">
        <h2 class="font-bold text-gray-900 font-tajawal">قائمة الموظفين</h2>
        <span class="text-xs px-3 py-1 rounded-full font-medium font-tajawal" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;"><?php echo e($employees->total()); ?> موظف</span>
    </div>

    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-sm min-w-[800px]">
            <thead class="border-b border-gray-200 bg-gray-50/80">
                <tr class="text-gray-600">
                    <th class="text-right p-4 font-tajawal font-bold">الموظف</th>
                    <th class="text-right p-4 font-tajawal font-bold">الدور</th>
                    <th class="text-right p-4 font-tajawal font-bold">المنصب</th>
                    <th class="text-right p-4 font-tajawal font-bold">التواصل</th>
                    <th class="text-right p-4 font-tajawal font-bold">التوظيف</th>
                    <th class="text-right p-4 font-tajawal font-bold">الحالة</th>
                    <th class="text-right p-4 font-tajawal font-bold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $fullName = trim($employee->first_name . ' ' . $employee->last_name);
                    $initial = mb_substr($fullName, 0, 1);
                    $roleResolved = \App\Services\EmployeeRoleService::resolve($employee);
                    $crmRole = $roleResolved['key'];
                    $roleLabel = $roleResolved['label'];
                    $st = $statusLabels[$employee->status] ?? ['—', 'bg-gray-100 text-gray-600'];
                    $query = array_filter([
                        'sales_only' => ($salesOnly ?? false) ? 1 : null,
                        'marketing_only' => ($marketingOnly ?? false) ? 1 : null,
                    ]);
                ?>
                <tr class="hover:bg-gray-50/80 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0 font-tajawal"
                                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);"><?php echo e($initial); ?></div>
                            <div class="min-w-0">
                                <a href="<?php echo e(route('employees.show', array_merge(['employee' => $employee], $query))); ?>" class="font-semibold text-gray-900 hover:underline font-tajawal block truncate"><?php echo e($fullName); ?></a>
                                <span class="text-xs text-gray-400 font-tajawal" dir="ltr"><?php echo e($employee->employee_id); ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <?php
                            $isMgr = in_array($crmRole, ['manager', 'marketing_manager'], true);
                            $badgeBg = ($marketingOnly ?? false) ? ($isMgr ? '#8b5cf618' : '#f3e8ff') : ($isMgr ? $themeColor . '18' : '#eff6ff');
                            $badgeColor = ($marketingOnly ?? false) ? ($isMgr ? '#7c3aed' : '#9333ea') : ($isMgr ? $themeColor : '#2563eb');
                        ?>
                        <span class="text-xs px-2 py-1 rounded-lg font-semibold font-tajawal"
                              style="background: <?php echo e($badgeBg); ?>; color: <?php echo e($badgeColor); ?>;"><?php echo e($roleLabel); ?></span>
                    </td>
                    <td class="p-4 text-gray-700 font-tajawal text-xs max-w-[140px] truncate"><?php echo e($employee->position ?? '—'); ?></td>
                    <td class="p-4">
                        <div class="text-xs text-gray-600 font-tajawal" dir="ltr"><?php echo e($employee->phone); ?></div>
                        <div class="text-xs text-gray-400 font-tajawal truncate max-w-[160px]" dir="ltr"><?php echo e($employee->email); ?></div>
                    </td>
                    <td class="p-4 text-xs text-gray-600 font-tajawal whitespace-nowrap">
                        <?php echo e($employee->hire_date?->format('Y/m/d') ?? '—'); ?>

                        <span class="block text-gray-400 tabular-nums"><?php echo e(number_format($employee->salary)); ?> ج.م</span>
                    </td>
                    <td class="p-4">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal <?php echo e($st[1]); ?>"><?php echo e($st[0]); ?></span>
                    </td>
                    <td class="p-4">
                        <div class="flex flex-wrap gap-1.5">
                            <a href="<?php echo e(route('employees.show', array_merge(['employee' => $employee], $query))); ?>"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-white font-tajawal"
                               style="background:<?php echo e($themeColor); ?>">عرض</a>
                            <?php if($canEdit): ?>
                            <a href="<?php echo e(route('employees.edit', array_merge(['employee' => $employee], $query))); ?>"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 font-tajawal">تعديل</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="p-12 text-center">
                        <p class="text-gray-400 font-tajawal mb-4">لا يوجد موظفون مطابقون</p>
                        <?php if($canCreate): ?>
                        <a href="<?php echo e(route('employees.create', array_filter(['sales_only' => ($salesOnly ?? false) ? 1 : null, 'marketing_only' => ($marketingOnly ?? false) ? 1 : null]))); ?>"
                           class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                           style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">إضافة موظف</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($employees->hasPages()): ?>
    <div class="p-4 border-t border-gray-100"><?php echo e($employees->links()); ?></div>
    <?php endif; ?>
</div>


<div class="md:hidden mt-4 space-y-3">
    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $fullName = trim($employee->first_name . ' ' . $employee->last_name);
        $query = array_filter([
            'sales_only' => ($salesOnly ?? false) ? 1 : null,
            'marketing_only' => ($marketingOnly ?? false) ? 1 : null,
        ]);
        $mobileRole = \App\Services\EmployeeRoleService::resolve($employee);
    ?>
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
        <a href="<?php echo e(route('employees.show', array_merge(['employee' => $employee], $query))); ?>" class="font-bold text-gray-900 font-tajawal"><?php echo e($fullName); ?></a>
        <p class="text-xs text-gray-500 mt-1 font-tajawal"><?php echo e($mobileRole['label']); ?> · <?php echo e($employee->position); ?></p>
        <div class="flex gap-2 mt-3">
            <a href="<?php echo e(route('employees.show', array_merge(['employee' => $employee], $query))); ?>" class="flex-1 text-center py-2 rounded-lg text-xs font-bold text-white font-tajawal" style="background:<?php echo e($themeColor); ?>">عرض</a>
            <?php if($canEdit): ?>
            <a href="<?php echo e(route('employees.edit', array_merge(['employee' => $employee], $query))); ?>" class="px-4 py-2 rounded-lg text-xs font-semibold border border-gray-200 font-tajawal">تعديل</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/employees/index.blade.php ENDPATH**/ ?>
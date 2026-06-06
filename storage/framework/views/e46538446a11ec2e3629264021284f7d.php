<?php $__env->startSection('page-title', trim($employee->first_name . ' ' . $employee->last_name)); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $fieldLabel = 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = 'text-sm font-medium text-gray-900 font-tajawal';

    $fullName = trim($employee->first_name . ' ' . $employee->last_name);
    $initial = mb_substr($fullName, 0, 1);

    $roleMeta = $roleMeta ?? \App\Services\EmployeeRoleService::resolve($employee);
    $roleLabel = $roleMeta['label'];
    $isMarketing = ($marketingOnly ?? false) || $roleMeta['module'] === 'marketing';

    $employmentLabels = [
        'full_time' => 'دوام كامل',
        'part_time' => 'دوام جزئي',
        'contract' => 'عقد',
        'intern' => 'متدرب',
    ];

    $statusLabels = [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'on_leave' => 'في إجازة',
        'terminated' => 'منتهي الخدمة',
    ];
    $statusColors = [
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-100 text-gray-700',
        'on_leave' => 'bg-amber-100 text-amber-800',
        'terminated' => 'bg-red-100 text-red-800',
    ];
    $status = $employee->status ?? 'active';

    $salesCount = $isMarketing ? 0 : $employee->sales()->count();
    $marketingLeadsCount = $isMarketing && $employee->user
        ? \App\Models\Client::where('created_by', $employee->user->id)->count()
        : 0;
?>

<?php
    $listQuery = array_filter([
        'sales_only' => ($salesOnly ?? false) ? 1 : null,
        'marketing_only' => ($marketingOnly ?? false) ? 1 : null,
    ]);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $fullName,
    'subtitle' => $roleLabel . ' — ' . ($employee->department->name ?? ($isMarketing ? 'التسويق' : 'المبيعات')),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
    'actionUrl' => ($canEdit ?? false) ? route('employees.edit', array_merge(['employee' => $employee], $listQuery)) : null,
    'actionLabel' => 'تعديل البيانات',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 w-full">
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'أيام الحضور',
        'value' => $stats['total_attendance_days'] ?? 0,
        'accent' => 'theme',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'الإجازات',
        'value' => $stats['total_leaves'] ?? 0,
        'accent' => 'amber',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => $isMarketing ? 'Leads مُسجّلة' : 'صفقات CRM',
        'value' => $isMarketing ? $marketingLeadsCount : $salesCount,
        'accent' => 'blue',
        'icon' => $isMarketing
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('crm.partials.stat-card', [
        'label' => 'الراتب الشهري',
        'value' => $employee->salary ? number_format($employee->salary) . ' ج.م' : '—',
        'accent' => 'purple',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 w-full">
    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
            ملخص الموظف
        </div>
        <div class="p-5 sm:p-6 text-center">
            <div class="h-20 w-20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold text-white shadow-lg font-tajawal"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                <?php echo e($initial); ?>

            </div>
            <h2 class="text-lg font-bold text-gray-900 font-tajawal"><?php echo e($fullName); ?></h2>
            <p class="text-sm text-gray-500 mt-1 font-tajawal"><?php echo e($employee->position ?: $roleLabel); ?></p>
            <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold font-tajawal <?php echo e($statusColors[$status] ?? $statusColors['inactive']); ?>">
                    <?php echo e($statusLabels[$status] ?? $status); ?>

                </span>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold font-tajawal"
                      style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">
                    <?php echo e($roleLabel); ?>

                </span>
            </div>
            <dl class="mt-6 space-y-3 text-right">
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">رقم الموظف</dt>
                    <dd class="<?php echo e($fieldValue); ?>" dir="ltr"><?php echo e($employee->employee_id); ?></dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">تاريخ التوظيف</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->hire_date?->format('Y/m/d') ?? '—'); ?></dd>
                </div>
                <?php if($employee->user): ?>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">حساب النظام</dt>
                    <dd class="<?php echo e($fieldValue); ?>" dir="ltr"><?php echo e($employee->user->email); ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
        <div class="px-5 sm:px-6 py-4 border-t border-gray-100 flex flex-col gap-2">
            <?php if($canEdit ?? false): ?>
            <a href="<?php echo e(route('employees.edit', array_merge(['employee' => $employee], $listQuery))); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal text-white"
               style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                تعديل البيانات
            </a>
            <?php endif; ?>
            <?php if($isMarketing && $employee->user?->canAccessMarketing()): ?>
            <a href="<?php echo e(route('marketing.dashboard')); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal border-2 text-center"
               style="border-color:#8b5cf640;color:#7c3aed">
                لوحة التسويق
            </a>
            <?php elseif($employee->user && $employee->user->canAccessCrm()): ?>
            <a href="<?php echo e(route('crm.team-members.show', $employee->user)); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal border-2 text-center"
               style="border-color:<?php echo e($themeColor); ?>40;color:<?php echo e($themeColor); ?>">
                ملف CRM
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('attendances.index', ['employee' => $employee->id])); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">
                سجل الحضور
            </a>
            <a href="<?php echo e(route('salaries.index', ['employee' => $employee->id])); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">
                سجل المرتبات
            </a>
            <a href="<?php echo e(route('employees.index', $listQuery)); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal border-2 border-gray-200 text-gray-600 hover:bg-gray-50">
                العودة للقائمة
            </a>
            <?php if($canDelete ?? false): ?>
            <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" onsubmit="return confirm('حذف هذا الموظف نهائياً؟');">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <?php if($salesOnly ?? false): ?><input type="hidden" name="sales_only" value="1"><?php endif; ?>
                <?php if($marketingOnly ?? false): ?><input type="hidden" name="marketing_only" value="1"><?php endif; ?>
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold font-tajawal border-2 border-red-200 text-red-600 hover:bg-red-50">
                    حذف الموظف
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                بيانات التواصل
            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">البريد الإلكتروني</dt>
                    <dd class="<?php echo e($fieldValue); ?>" dir="ltr"><?php echo e($employee->email); ?></dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">الهاتف</dt>
                    <dd class="flex items-center gap-2 <?php echo e($fieldValue); ?>" dir="ltr">
                        <span><?php echo e($employee->phone ?? '—'); ?></span>
                        <?php if($employee->phone): ?>
                        <button type="button" onclick="openWhatsAppContact('<?php echo e($employee->phone); ?>', '<?php echo e(addslashes($fullName)); ?>')"
                                class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 transition-colors" title="واتساب">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/></svg>
                        </button>
                        <?php endif; ?>
                    </dd>
                </div>
                <?php if($employee->address): ?>
                <div class="sm:col-span-2">
                    <dt class="<?php echo e($fieldLabel); ?>">العنوان</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->address); ?></dd>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                بيانات التوظيف — <?php echo e($employee->department->name ?? 'المبيعات'); ?>

            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">المنصب</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->position ?: $roleLabel); ?></dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">نوع التوظيف</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employmentLabels[$employee->employment_type] ?? $employee->employment_type); ?></dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">ساعات العمل اليومية</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->daily_hours ?? 8); ?> ساعة</dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">الراتب</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->salary ? number_format($employee->salary) . ' ج.م' : '—'); ?></dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">إجازات معلقة</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($stats['pending_leaves'] ?? 0); ?></dd>
                </div>
                <div>
                    <dt class="<?php echo e($fieldLabel); ?>">إجازات معتمدة</dt>
                    <dd class="<?php echo e($fieldValue); ?>"><?php echo e($stats['approved_leaves'] ?? 0); ?></dd>
                </div>
            </div>
        </div>

        <?php if($employee->emergency_contact || $employee->emergency_phone): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                جهة اتصال الطوارئ
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php if($employee->emergency_contact): ?>
                    <div>
                        <dt class="<?php echo e($fieldLabel); ?>">الاسم</dt>
                        <dd class="<?php echo e($fieldValue); ?>"><?php echo e($employee->emergency_contact); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($employee->emergency_phone): ?>
                    <div>
                        <dt class="<?php echo e($fieldLabel); ?>">الهاتف</dt>
                        <dd class="<?php echo e($fieldValue); ?>" dir="ltr"><?php echo e($employee->emergency_phone); ?></dd>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($salesCount > 0): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?> flex items-center justify-between"
                 style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                <span>صفقات المبيعات</span>
                <span class="text-xs font-semibold font-tajawal px-3 py-1 rounded-lg" style="background: <?php echo e($themeColor); ?>15; color: <?php echo e($themeColor); ?>;">
                    <?php echo e(number_format($salesValue)); ?> ج.م إجمالي
                </span>
            </div>
            <div class="p-5 sm:p-6">
                <p class="text-sm text-gray-600 font-tajawal mb-4">لدى هذا الموظف <?php echo e($salesCount); ?> صفقة مسجلة في CRM.</p>
                <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="inline-flex items-center text-sm font-semibold font-tajawal"
                   style="color: <?php echo e($themeColor); ?>;">
                    عرض مسار المبيعات ←
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="<?php echo e($sectionHeader); ?>" style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
                سجل النشاط
            </div>
            <div class="p-5 sm:p-6 space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <span class="h-2 w-2 rounded-full flex-shrink-0" style="background: <?php echo e($themeColor); ?>;"></span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 font-tajawal">تم إنشاء الملف</p>
                        <p class="text-xs text-gray-500 font-tajawal"><?php echo e($employee->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php if($employee->updated_at->ne($employee->created_at)): ?>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <span class="h-2 w-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 font-tajawal">آخر تحديث</p>
                        <p class="text-xs text-gray-500 font-tajawal"><?php echo e($employee->updated_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views/employees/show.blade.php ENDPATH**/ ?>
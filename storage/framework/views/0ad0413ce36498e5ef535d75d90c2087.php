
<?php
    $crmOnlyWorkspace = $webUser && $webUser->usesCrmWorkspace();
    $marketingOnlyWorkspace = $webUser && $webUser->usesMarketingWorkspace();
    $operationsOnlyWorkspace = $webUser && $webUser->usesOperationsWorkspace();
    $hrOnlyWorkspace = $webUser && $webUser->usesHrWorkspace();
    $crmAdminInMainApp = $webUser && $webUser->canAccessCrm() && !$webUser->usesCrmWorkspace();
    $marketingAdminInMainApp = $webUser && $webUser->canAccessMarketing() && !$webUser->usesMarketingWorkspace();
    $crmRole = $webUser ? \App\Services\CrmRoleResolver::for($webUser)->workspace() : null;
    $marketingRole = $webUser ? \App\Services\MarketingRoleResolver::for($webUser)->workspace() : null;
?>

<?php if(!$isClientGuard && $hrOnlyWorkspace && !$crmOnlyWorkspace && !$marketingOnlyWorkspace && !$operationsOnlyWorkspace): ?>
    <?php echo $__env->make('layouts.partials.sidebar-hr', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif(!$isClientGuard && $operationsOnlyWorkspace && !$crmOnlyWorkspace && !$marketingOnlyWorkspace): ?>
    <?php echo $__env->make('layouts.partials.sidebar-operations-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif(!$isClientGuard && $marketingOnlyWorkspace && !$crmOnlyWorkspace): ?>
    <?php echo $__env->make(match ($marketingRole) {
        \App\Services\MarketingRoleResolver::WORKSPACE_ADMIN => 'layouts.partials.sidebar-marketing-admin',
        \App\Services\MarketingRoleResolver::WORKSPACE_MANAGER => 'layouts.partials.sidebar-marketing-manager',
        default => 'layouts.partials.sidebar-marketing-rep',
    }, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif(!$isClientGuard && $crmOnlyWorkspace): ?>
    <?php echo $__env->make(match ($crmRole) {
        \App\Services\CrmRoleResolver::WORKSPACE_ADMIN => 'layouts.partials.sidebar-crm-admin',
        \App\Services\CrmRoleResolver::WORKSPACE_MANAGER,
        \App\Services\CrmRoleResolver::WORKSPACE_TEAM_LEADER => 'layouts.partials.sidebar-crm-manager',
        default => 'layouts.partials.sidebar-crm-rep',
    }, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif(!$isClientGuard): ?>
<a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    لوحة التحكم
</a>
<?php endif; ?>

<?php if($crmAdminInMainApp): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">المبيعات العقارية</h3>
    <?php echo $__env->make('layouts.partials.sidebar-crm-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php endif; ?>

<?php if($marketingAdminInMainApp): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">التسويق</h3>
    <?php echo $__env->make('layouts.partials.sidebar-marketing-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php endif; ?>

<?php if($webUser?->canAccessOperations() && !$webUser?->usesOperationsWorkspace()): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4"><?php echo e(__('operations.nav.workspace_operations')); ?></h3>
    <a href="<?php echo e(route('operations.dashboard')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('operations.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
        <?php echo e(__('operations.sidebar.dashboard')); ?>

    </a>
    <a href="<?php echo e(route('operations.reports.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('operations.reports.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <?php echo e(__('operations.sidebar.operations_reports')); ?>

    </a>
    <a href="<?php echo e(route('operations.attendance-reviews.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('operations.attendance-reviews.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <?php echo e(__('operations.sidebar.absence_reviews')); ?>

    </a>
    <a href="<?php echo e(route('operations.checkout-reviews.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('operations.checkout-reviews.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        <?php echo e(__('operations.sidebar.checkout_approvals')); ?>

    </a>
</div>
<?php endif; ?>

<?php if(!$webUser?->usesCrmWorkspace() && !$webUser?->usesMarketingWorkspace() && !$webUser?->usesOperationsWorkspace() && $webUser && $webUser->can('view-employees')): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">إدارة الموظفين</h3>
    <a href="<?php echo e(route('employees.index', ['sales_only' => 1])); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('employees.*') && request('sales_only') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        موظفو المبيعات
    </a>
    <?php if($webUser->canAccessMarketing()): ?>
    <a href="<?php echo e(route('employees.index', ['marketing_only' => 1])); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('employees.*') && request('marketing_only') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        موظفو التسويق
    </a>
    <?php endif; ?>
    <?php if($webUser->canAccessOperations()): ?>
    <a href="<?php echo e(route('employees.index', ['operations_only' => 1])); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('employees.*') && request('operations_only') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
        مديرو العمليات
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if($webUser && ($webUser->can('view-developers') || $webUser->hasRole(['super_admin', 'admin']))): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">المطورون والتعاقدات</h3>
    <a href="<?php echo e(route('admin.developers.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('admin.developers.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        المطورون العقاريون
    </a>
</div>
<?php endif; ?>

<?php if($webUser?->canAccessHr() && !$webUser?->usesHrWorkspace()): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">الموارد البشرية</h3>
    <a href="<?php echo e(route('hr.dashboard')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        لوحة الموارد البشرية
    </a>
    <a href="<?php echo e(route('hr.exit-permits.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.exit-permits.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
        الأذونات
    </a>
    <a href="<?php echo e(route('hr.absences.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.absences.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        الغياب
    </a>
    <a href="<?php echo e(route('hr.reports.monthly')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.reports.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-7M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
        تقارير الحضور الشهرية
    </a>
    <a href="<?php echo e(route('hr.contracts.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.contracts.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        عقود الموظفين
    </a>
    <a href="<?php echo e(route('hr.custody.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.custody.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        استلام وتسليم العهد
    </a>
    <a href="<?php echo e(route('hr.documents.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.documents.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        ملفات الموظفين
    </a>
</div>
<?php endif; ?>

<?php if($webUser && !$webUser->usesCrmWorkspace() && !$webUser->usesMarketingWorkspace() && !$webUser->usesHrWorkspace() && ($webUser->can('view-leaves') || $webUser->can('view-attendance') || $webUser->can('view-employees'))): ?>)
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">الموارد البشرية</h3>
    <?php if($webUser->can('view-attendance')): ?>
    <a href="<?php echo e(route('attendances.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('attendances.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        الحضور والانصراف
    </a>
    <?php endif; ?>
    <?php if($webUser->can('view-leaves')): ?>
    <a href="<?php echo e(route('leaves.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('leaves.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        الإجازات
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if(!$webUser?->usesCrmWorkspace() && $webUser && ($webUser->can('view-users') || $webUser->can('view-settings') || $webUser->can('manage-roles'))): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">الإعدادات</h3>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-users')): ?>
    <a href="<?php echo e(route('users.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">المستخدمين</a>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-roles')): ?>
    <a href="<?php echo e(route('roles.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>">الأدوار والصلاحيات</a>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-settings')): ?>
    <a href="<?php echo e(route('system-settings.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('system-settings.*') ? 'active' : ''); ?>">إعدادات النظام</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if(!$isClientGuard && $webUser): ?>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">العملاء</h3>
    <?php echo $__env->make('layouts.partials.sidebar-client-intake-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php endif; ?>

<?php if(!$isClientGuard): ?>
<a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium mt-4 <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    الملف الشخصي
</a>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\partials\sidebar-realestate.blade.php ENDPATH**/ ?>
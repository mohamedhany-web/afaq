
<?php
    $crmOnlyWorkspace = $webUser && $webUser->usesCrmWorkspace();
    $marketingOnlyWorkspace = $webUser && $webUser->usesMarketingWorkspace();
    $crmAdminInMainApp = $webUser && $webUser->canAccessCrm() && !$webUser->usesCrmWorkspace();
    $marketingAdminInMainApp = $webUser && $webUser->canAccessMarketing() && !$webUser->usesMarketingWorkspace();
    $crmRole = $webUser ? \App\Services\CrmRoleResolver::for($webUser)->workspace() : null;
    $marketingRole = $webUser ? \App\Services\MarketingRoleResolver::for($webUser)->workspace() : null;
?>

<?php if(!$isClientGuard && $marketingOnlyWorkspace && !$crmOnlyWorkspace): ?>
    <?php echo $__env->make(match ($marketingRole) {
        \App\Services\MarketingRoleResolver::WORKSPACE_ADMIN => 'layouts.partials.sidebar-marketing-admin',
        \App\Services\MarketingRoleResolver::WORKSPACE_MANAGER => 'layouts.partials.sidebar-marketing-manager',
        default => 'layouts.partials.sidebar-marketing-rep',
    }, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif(!$isClientGuard && $crmOnlyWorkspace): ?>
    <?php echo $__env->make(match ($crmRole) {
        \App\Services\CrmRoleResolver::WORKSPACE_ADMIN => 'layouts.partials.sidebar-crm-admin',
        \App\Services\CrmRoleResolver::WORKSPACE_MANAGER => 'layouts.partials.sidebar-crm-manager',
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

<?php if(!$webUser?->usesCrmWorkspace() && !$webUser?->usesMarketingWorkspace() && $webUser && $webUser->can('view-employees')): ?>
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

<?php if(!$isClientGuard): ?>
<a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium mt-4 <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    الملف الشخصي
</a>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\partials\sidebar-realestate.blade.php ENDPATH**/ ?>
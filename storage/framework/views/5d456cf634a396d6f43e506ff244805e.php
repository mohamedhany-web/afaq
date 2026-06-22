<?php if(auth()->guard()->check()): ?>
<?php
    $wsUser = auth()->user();
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $workspaces = [];

    if ($wsUser->canAccessCrm()) {
        $workspaces[] = [
            'key' => 'crm',
            'label' => __('operations.nav.workspace_crm'),
            'url' => route('crm.dashboard'),
            'active' => request()->routeIs('crm.*') || (request()->routeIs('dashboard') && $wsUser->usesCrmWorkspace()),
        ];
    }

    if ($wsUser->canAccessOperations()) {
        $workspaces[] = [
            'key' => 'operations',
            'label' => __('operations.nav.workspace_operations'),
            'url' => route('operations.dashboard'),
            'active' => request()->routeIs('operations.*'),
        ];
    }

    if ($wsUser->canAccessHr()) {
        $workspaces[] = [
            'key' => 'hr',
            'label' => __('operations.nav.workspace_hr'),
            'url' => route('hr.dashboard'),
            'active' => request()->routeIs('hr.*') || ($wsUser->usesHrWorkspace() && (request()->routeIs('attendances.*') || request()->routeIs('leaves.*'))),
        ];
    }

    if ($wsUser->canAccessMarketing()) {
        $workspaces[] = [
            'key' => 'marketing',
            'label' => __('operations.nav.workspace_marketing'),
            'url' => route('marketing.dashboard'),
            'active' => request()->routeIs('marketing.*'),
        ];
    }

    if ($wsUser->hasRole(['super_admin', 'admin']) && !$wsUser->usesCrmWorkspace() && !$wsUser->usesOperationsWorkspace() && !$wsUser->usesHrWorkspace() && !$wsUser->usesMarketingWorkspace()) {
        $workspaces[] = [
            'key' => 'admin',
            'label' => __('operations.nav.workspace_admin'),
            'url' => route('dashboard'),
            'active' => request()->routeIs('dashboard') || request()->routeIs('reports.*') || request()->routeIs('employees.*') || request()->routeIs('admin.*'),
        ];
    }
?>

<?php if(count($workspaces) > 1): ?>
<nav class="mb-4 rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto" aria-label="<?php echo e(__('operations.nav.workspaces')); ?>">
    <div class="flex items-center gap-1 p-1.5 min-w-max font-tajawal">
        <span class="px-3 py-2 text-xs font-bold text-gray-400 shrink-0"><?php echo e(__('operations.nav.workspaces')); ?></span>
        <?php $__currentLoopData = $workspaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($ws['url']); ?>"
           class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all <?php echo e($ws['active'] ? 'text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'); ?>"
           <?php if($ws['active']): ?> style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);" <?php endif; ?>>
            <?php echo e($ws['label']); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</nav>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\partials\workspace-switcher.blade.php ENDPATH**/ ?>
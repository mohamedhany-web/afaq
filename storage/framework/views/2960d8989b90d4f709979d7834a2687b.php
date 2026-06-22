<?php if(auth()->user()?->employee): ?>
<a href="<?php echo e(route('hr.exit-permits.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('hr.exit-permits.*') ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
    <?php echo e($label ?? 'طلب إذن'); ?>

</a>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\partials\sidebar-exit-permit-link.blade.php ENDPATH**/ ?>
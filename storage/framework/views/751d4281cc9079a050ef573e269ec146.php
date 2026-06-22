
<a href="<?php echo e(route('crm.clients.create')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.clients.create') && request('tab') !== 'import' ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    إضافة عميل
</a>
<a href="<?php echo e(route('crm.clients.create', ['tab' => 'import'])); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.clients.create') && request('tab') === 'import' ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
    استيراد Excel
</a>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\layouts\partials\sidebar-client-intake-links.blade.php ENDPATH**/ ?>
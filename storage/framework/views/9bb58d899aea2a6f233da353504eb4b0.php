
<a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('dashboard') || request()->routeIs('crm.dashboard') ? 'active' : ''); ?>">
    <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    لوحة التحكم
</a>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">مبيعاتي</h3>
    <a href="<?php echo e(route('crm.clients.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.clients.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        العملاء
    </a>
    <a href="<?php echo e(route('crm.pipeline.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.pipeline.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
        مسار المبيعات
    </a>
    <a href="<?php echo e(route('crm.tasks.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.tasks.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        مهامي
    </a>
    <a href="<?php echo e(route('crm.follow-ups.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.follow-ups.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        جدول المتابعات
    </a>
    <a href="<?php echo e(route('crm.daily-reports.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.daily-reports.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        تقريري اليومي
    </a>
    <a href="<?php echo e(route('crm.compensation.dashboard')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('crm.compensation.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        راتبي ومؤشراتي
    </a>
</div>
<div class="mt-6">
    <h3 class="sidebar-section-title px-4">الحساب</h3>
    <a href="<?php echo e(route('notifications.index')); ?>" class="sidebar-link flex items-center px-4 py-3 text-sm font-medium <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
        <svg class="ml-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        الإشعارات
        <span id="unread-notifications-count" class="mr-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden">0</span>
    </a>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/layouts/partials/sidebar-crm-rep.blade.php ENDPATH**/ ?>
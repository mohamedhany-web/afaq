<?php
    $hub = $portalHub ?? [];
    $activity = $hub['recent_activity'] ?? collect();
?>

<div class="mb-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden font-tajawal" id="client-portal-hub">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        <div>
            <h2 class="font-bold text-gray-900">بوابة العميل</h2>
            <p class="text-xs text-gray-500 mt-0.5">متابعة مباشرة بين العميل والإدارة</p>
        </div>
        <?php if(auth()->user()?->can('create-clients')): ?>
        <a href="<?php echo e(route('client-accounts.create', ['client_id' => $client->id])); ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-white" style="background:<?php echo e($themeColor); ?>">+ حساب بوابة</a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 p-5 border-b border-gray-100">
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'حسابات البوابة', 'value' => $hub['active_accounts'] ?? 0, 'accent' => 'theme', 'compact' => true, 'href' => route('client-accounts.index', ['client_id' => $client->id]), 'linkLabel' => 'إدارة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'تذاكر مفتوحة', 'value' => $hub['open_tickets'] ?? 0, 'accent' => 'blue', 'compact' => true, 'href' => route('tickets.index', ['client_id' => $client->id]), 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'بلاغات الموقع', 'value' => $hub['open_issues'] ?? 0, 'accent' => 'amber', 'compact' => true, 'href' => route('client-website-issues.index', ['client_id' => $client->id]), 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'اجتماعات معلّقة', 'value' => $hub['pending_meetings'] ?? 0, 'accent' => 'purple', 'compact' => true, 'href' => route('client-meeting-requests.index', ['client_id' => $client->id]), 'linkLabel' => 'عرض'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['label' => 'إشعارات غير مقروءة', 'value' => $hub['unread_notifications'] ?? 0, 'accent' => 'green', 'compact' => true, 'href' => route('client-shared-documents.index', ['client_id' => $client->id]), 'linkLabel' => 'مستندات'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php if($activity->isNotEmpty()): ?>
    <div class="p-5">
        <h3 class="text-sm font-bold text-gray-800 mb-3">آخر نشاط من البوابة</h3>
        <ul class="divide-y divide-gray-100">
            <?php $__currentLoopData = $activity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="py-3 flex items-center justify-between gap-3 text-sm">
                <div>
                    <a href="<?php echo e($item['url']); ?>" class="font-semibold hover:underline" style="color:<?php echo e($themeColor); ?>"><?php echo e($item['title']); ?></a>
                    <p class="text-xs text-gray-500 mt-0.5"><?php echo e(match($item['type']) { 'issue' => 'بلاغ موقع', 'meeting' => 'طلب اجتماع', default => 'تذكرة دعم' }); ?> · <?php echo e($item['status']); ?></p>
                </div>
                <span class="text-xs text-gray-400 shrink-0"><?php echo e($item['at']?->diffForHumans()); ?></span>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php else: ?>
    <p class="p-5 text-sm text-gray-500">لا يوجد نشاط مسجّل من بوابة العميل بعد.</p>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\portal-hub.blade.php ENDPATH**/ ?>
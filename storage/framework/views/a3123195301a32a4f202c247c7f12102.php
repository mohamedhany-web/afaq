<?php $__env->startSection('page-title', 'الإشعارات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $buildQuery = fn (string $f, array $extra = []) => array_filter(array_merge(
        ['filter' => $f],
        $search ? ['search' => $search] : [],
        ($extra['archive'] ?? false) ? ['archive' => 1] : ($archive ? ['archive' => 1] : []),
        $extra
    ));
    $filterPill = fn (string $key) => $filter === $key
        ? 'text-white shadow-md font-bold'
        : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-2 border-gray-100';
    $filterStyle = fn (string $key) => $filter === $key
        ? "background: linear-gradient(135deg, {$themeColor} 0%, {$themeColor}dd 100%);"
        : '';
    $typeMeta = function (string $type) use ($themeColor) {
        return match ($type) {
            'task', 'crm_task' => ['accent' => '#16a34a', 'label' => 'مهمة'],
            'project' => ['accent' => '#2563eb', 'label' => 'مشروع'],
            'message' => ['accent' => '#9333ea', 'label' => 'رسالة'],
            'crm_follow_up', 'crm_reminder' => ['accent' => '#d97706', 'label' => 'متابعة'],
            'crm_daily_report' => ['accent' => $themeColor, 'label' => 'تقرير'],
            default => ['accent' => '#6b7280', 'label' => 'عام'],
        };
    };
    $maxDays = config('notifications.list_max_days', 90);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => 'صندوق الإشعارات',
    'subtitle' => 'عرض ما يحتاج انتباهك — وليس آلاف السجلات دفعة واحدة',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="w-full space-y-6">
    <?php if($highVolume ?? false): ?>
    <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 px-4 py-3 text-sm font-tajawal text-amber-900">
        حجم كبير من الإشعارات (<?php echo e(number_format($totalCount)); ?>). ننصح بفتح تبويب <strong>غير مقروءة</strong> أو <strong>اليوم</strong>، وتفعيل التنظيف التلقائي للمقروءة.
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <?php echo $__env->make('crm.partials.stat-card', ['compact' => true, 'label' => 'غير مقروءة', 'value' => $unreadCount, 'accent' => 'red', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'href' => route('notifications.index', $buildQuery('unread')) . '#page-data', 'linkLabel' => 'عرض غير المقروءة'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['compact' => true, 'label' => 'اليوم', 'value' => $todayCount, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'href' => route('notifications.index', $buildQuery('today')) . '#page-data', 'linkLabel' => 'عرض اليوم'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['compact' => true, 'label' => 'آخر 7 أيام', 'value' => $weekCount ?? 0, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'href' => route('notifications.index', $buildQuery('week')) . '#page-data', 'linkLabel' => 'عرض الأسبوع'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['compact' => true, 'label' => 'CRM', 'value' => $crmCount, 'accent' => 'amber', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>', 'href' => route('notifications.index', $buildQuery('crm')) . '#page-data', 'linkLabel' => 'عرض CRM'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('crm.partials.stat-card', ['compact' => true, 'label' => 'الإجمالي', 'value' => number_format($totalCount), 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>', 'href' => route('notifications.index', $buildQuery('all')) . '#page-data', 'linkLabel' => 'عرض الكل'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <span class="text-xs font-bold text-gray-400 font-tajawal w-full mb-0.5">العرض الرئيسي</span>
                <a href="<?php echo e(route('notifications.index', $buildQuery('unread'))); ?>"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition <?php echo e($filterPill('unread')); ?>"
                   <?php if($filter === 'unread'): ?> style="<?php echo e($filterStyle('unread')); ?>" <?php endif; ?>>يحتاج انتباه (<?php echo e($unreadCount); ?>)</a>
                <a href="<?php echo e(route('notifications.index', $buildQuery('today'))); ?>"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition <?php echo e($filterPill('today')); ?>"
                   <?php if($filter === 'today'): ?> style="<?php echo e($filterStyle('today')); ?>" <?php endif; ?>>اليوم (<?php echo e($todayCount); ?>)</a>
                <a href="<?php echo e(route('notifications.index', $buildQuery('week'))); ?>"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition <?php echo e($filterPill('week')); ?>"
                   <?php if($filter === 'week'): ?> style="<?php echo e($filterStyle('week')); ?>" <?php endif; ?>>آخر 7 أيام (<?php echo e($weekCount ?? 0); ?>)</a>
                <a href="<?php echo e(route('notifications.index', $buildQuery('read'))); ?>"
                   class="px-3 py-2 rounded-xl text-xs sm:text-sm font-tajawal transition <?php echo e($filterPill('read')); ?>"
                   <?php if($filter === 'read'): ?> style="<?php echo e($filterStyle('read')); ?>" <?php endif; ?>>مقروءة (<?php echo e($readCount ?? 0); ?>)</a>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if($unreadCount > 0): ?>
                <form action="<?php echo e(route('notifications.mark-all-read')); ?>" method="POST" id="markAllReadForm" class="inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
                    <?php if($search): ?><input type="hidden" name="search" value="<?php echo e($search); ?>"><?php endif; ?>
                    <?php if($archive): ?><input type="hidden" name="archive" value="1"><?php endif; ?>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold font-tajawal shadow-sm"
                            style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                        تحديد المعروض كمقروء
                    </button>
                </form>
                <?php endif; ?>
                <?php if(($readCount ?? 0) > 0): ?>
                <form action="<?php echo e(route('notifications.clear-read')); ?>" method="POST" class="inline"
                      onsubmit="return confirm('حذف الإشعارات المقروءة الأقدم من <?php echo e(config('notifications.prune_read_after_days', 30)); ?> يوماً؟')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-4 py-2 rounded-xl border-2 border-gray-200 text-gray-600 text-xs font-bold font-tajawal hover:bg-gray-50">
                        تنظيف المقروءة القديمة
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
            <span class="text-xs font-bold text-gray-400 font-tajawal w-full mb-0.5">تصفية حسب النوع</span>
            <a href="<?php echo e(route('notifications.index', $buildQuery('crm'))); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal <?php echo e($filter === 'crm' ? 'text-white font-bold' : 'bg-gray-50 text-gray-600'); ?>" <?php if($filter === 'crm'): ?> style="<?php echo e($filterStyle('crm')); ?>" <?php endif; ?>>CRM (<?php echo e($crmCount); ?>)</a>
            <a href="<?php echo e(route('notifications.index', $buildQuery('message'))); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal <?php echo e($filter === 'message' ? 'text-white font-bold' : 'bg-gray-50 text-gray-600'); ?>" <?php if($filter === 'message'): ?> style="<?php echo e($filterStyle('message')); ?>" <?php endif; ?>>رسائل (<?php echo e($messageCount); ?>)</a>
            <a href="<?php echo e(route('notifications.index', $buildQuery('project'))); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal <?php echo e($filter === 'project' ? 'text-white font-bold' : 'bg-gray-50 text-gray-600'); ?>" <?php if($filter === 'project'): ?> style="<?php echo e($filterStyle('project')); ?>" <?php endif; ?>>مشاريع (<?php echo e($projectCount); ?>)</a>
            <a href="<?php echo e(route('notifications.index', $buildQuery('all'))); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal <?php echo e($filter === 'all' && !$archive ? 'text-white font-bold' : 'bg-gray-50 text-gray-600'); ?>" <?php if($filter === 'all' && !$archive): ?> style="<?php echo e($filterStyle('all')); ?>" <?php endif; ?>>آخر <?php echo e($maxDays); ?> يوم</a>
            <?php if($filter === 'all' || $archive): ?>
            <a href="<?php echo e(route('notifications.index', ['filter' => 'all', 'archive' => 1, 'search' => $search])); ?>"
               class="px-2.5 py-1.5 rounded-lg text-xs font-tajawal <?php echo e($archive ? 'text-white font-bold' : 'bg-gray-50 text-gray-600'); ?>"
               <?php if($archive): ?> style="<?php echo e($filterStyle('all')); ?>" <?php endif; ?>>الأرشيف الكامل</a>
            <?php endif; ?>
        </div>

        <?php if($listCapped ?? false): ?>
        <p class="text-xs text-gray-500 font-tajawal">عرض «الكل» محدود بآخر <?php echo e($maxDays); ?> يوماً. للأقدم استخدم <a href="<?php echo e(route('notifications.index', ['filter' => 'all', 'archive' => 1])); ?>" class="font-bold underline" style="color: <?php echo e($themeColor); ?>;">الأرشيف</a>.</p>
        <?php endif; ?>

        <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
            <?php if($archive): ?><input type="hidden" name="archive" value="1"><?php endif; ?>
            <div class="relative flex-1">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="بحث سريع في العنوان أو النص..."
                       class="w-full border-2 border-gray-200 rounded-xl pl-11 pr-4 py-2.5 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal shrink-0"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">بحث</button>
            <?php if($search): ?>
            <a href="<?php echo e(route('notifications.index', array_filter(['filter' => $filter, 'archive' => $archive ? 1 : null]))); ?>"
               class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold font-tajawal text-center shrink-0">مسح</a>
            <?php endif; ?>
        </form>
    </div>

    <div id="page-data" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <?php if($notifications->count() > 0): ?>
            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border-b border-gray-100 last:border-b-0">
                <div class="px-4 sm:px-5 py-2.5 flex items-center justify-between sticky top-0 z-10 border-b border-gray-100"
                     style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>10 0%, #fff 100%);">
                    <h3 class="text-sm font-bold text-gray-800 font-tajawal"><?php echo e($group['label']); ?></h3>
                    <span class="text-xs text-gray-400 font-tajawal tabular-nums"><?php echo e($group['items']->count()); ?> إشعار</span>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('notifications.partials.inbox-item', compact('notification', 'themeColor', 'typeMeta'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="px-4 sm:px-6 py-4 border-t border-gray-100 overflow-x-auto font-tajawal text-sm text-gray-500">
                عرض <?php echo e($notifications->firstItem()); ?>–<?php echo e($notifications->lastItem()); ?> من <?php echo e(number_format($notifications->total())); ?>

                — <?php echo e($notifications->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-16 px-6">
                <h3 class="text-lg font-bold text-gray-900 font-tajawal">لا توجد إشعارات في هذا العرض</h3>
                <p class="mt-2 text-sm text-gray-500 font-tajawal">
                    <?php if($filter === 'unread'): ?>
                        لا يوجد ما يحتاج انتباهك الآن.
                    <?php else: ?>
                        جرّب تبويب «غير مقروءة» أو «اليوم».
                    <?php endif; ?>
                </p>
                <a href="<?php echo e(route('notifications.index', ['filter' => 'unread'])); ?>"
                   class="inline-flex mt-5 px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                   style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">غير مقروءة</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); });
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('markAllReadForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const orig = btn.innerHTML;
        btn.disabled = true;
        const body = new FormData(this);
        fetch('<?php echo e(route('notifications.mark-all-read')); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: body,
        })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); else { btn.disabled = false; btn.innerHTML = orig; } })
        .catch(() => { btn.disabled = false; btn.innerHTML = orig; });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\notifications\index.blade.php ENDPATH**/ ?>
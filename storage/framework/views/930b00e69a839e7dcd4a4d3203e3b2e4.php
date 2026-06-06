<?php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $typeMeta = $typeMeta ?? function (string $type) use ($themeColor) {
        return match ($type) {
            'task', 'crm_task' => ['accent' => '#16a34a', 'label' => 'مهمة'],
            'project' => ['accent' => '#2563eb', 'label' => 'مشروع'],
            'message' => ['accent' => '#9333ea', 'label' => 'رسالة'],
            'crm_follow_up', 'crm_reminder' => ['accent' => '#d97706', 'label' => 'متابعة'],
            'crm_daily_report' => ['accent' => $themeColor, 'label' => 'تقرير'],
            default => ['accent' => '#6b7280', 'label' => 'عام'],
        };
    };
?>
<div class="notifications-dropdown-panel w-[min(22rem,calc(100vw-1.25rem))] rounded-2xl shadow-2xl border border-gray-200 overflow-hidden bg-white font-tajawal" dir="rtl">
    <div class="px-4 py-3.5 flex items-center justify-between gap-3 text-white"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
        <div class="flex items-center gap-2 min-w-0">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 shrink-0">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </span>
            <div class="min-w-0">
                <h3 class="text-sm font-bold leading-tight">الإشعارات</h3>
                <p class="text-[10px] text-white/80 truncate">آخر التنبيهات</p>
            </div>
        </div>
        <?php if(($unreadCount ?? 0) > 0): ?>
        <span class="shrink-0 bg-white/25 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold tabular-nums"><?php echo e($unreadCount); ?> غير مقروء</span>
        <?php endif; ?>
    </div>

    <div class="max-h-[min(20rem,55vh)] overflow-y-auto overscroll-contain divide-y divide-gray-50">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('notifications.partials.dropdown-item', [
                'notification' => $notification,
                'themeColor' => $themeColor,
                'typeMeta' => $typeMeta,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-10 px-5">
                <div class="h-14 w-14 rounded-2xl flex items-center justify-center mx-auto mb-3"
                     style="background: <?php echo e($themeColor); ?>12;">
                    <svg class="h-7 w-7" style="color: <?php echo e($themeColor); ?>;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-gray-800">لا توجد إشعارات</p>
                <p class="text-xs text-gray-500 mt-1">ستظهر هنا التنبيهات الجديدة</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="px-3 py-3 border-t border-gray-100 bg-gray-50/90 flex flex-col sm:flex-row gap-2">
        <a href="<?php echo e(route('notifications.index')); ?>"
           class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-white text-xs font-bold shadow-sm hover:opacity-95 transition-opacity"
           style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            عرض جميع الإشعارات
            <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <?php if(($unreadCount ?? 0) > 0): ?>
        <button type="button"
                onclick="markAllNotificationsDropdownRead(this)"
                class="px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-700 text-xs font-bold hover:bg-white transition-colors shrink-0">
            تحديد الكل كمقروء
        </button>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/notifications/partials/dropdown-panel.blade.php ENDPATH**/ ?>
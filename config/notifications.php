<?php

return [
    /** الفلتر الافتراضي عند فتح صفحة الإشعارات */
    'default_filter' => env('NOTIFICATIONS_DEFAULT_FILTER', 'unread'),

    /** عدد الإشعارات في الصفحة */
    'per_page' => (int) env('NOTIFICATIONS_PER_PAGE', 50),

    /** مدة تخزين إحصائيات الصفحة (ثوانٍ) */
    'stats_cache_seconds' => (int) env('NOTIFICATIONS_STATS_CACHE', 30),

    /** حذف الإشعارات المقروءة الأقدم من (أيام) — أمر مجدول */
    'prune_read_after_days' => (int) env('NOTIFICATIONS_PRUNE_READ_DAYS', 30),

    /** عند عرض «الكل» لا نعرض أقدم من (أيام) إلا بطلب الأرشيف */
    'list_max_days' => (int) env('NOTIFICATIONS_LIST_MAX_DAYS', 90),

    /** تحذير في الواجهة إذا تجاوز العدد */
    'high_volume_threshold' => (int) env('NOTIFICATIONS_HIGH_VOLUME', 500),
];

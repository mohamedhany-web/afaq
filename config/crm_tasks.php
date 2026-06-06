<?php

return [
    'priorities' => ['low', 'medium', 'high', 'critical'],
    'priority_labels' => [
        'low' => 'منخفضة',
        'medium' => 'متوسطة',
        'high' => 'عالية',
        'critical' => 'حرجة',
    ],
    'priority_colors' => [
        'low' => '#6b7280',
        'medium' => '#2563eb',
        'high' => '#d97706',
        'critical' => '#dc2626',
    ],

    'statuses' => ['pending', 'accepted', 'in_progress', 'completed', 'overdue', 'cancelled', 'verified', 'archived'],
    'status_labels' => [
        'pending' => 'قيد الانتظار',
        'accepted' => 'مقبولة',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتملة',
        'overdue' => 'متأخرة',
        'cancelled' => 'ملغاة',
        'verified' => 'تم التحقق',
        'archived' => 'مؤرشفة',
    ],

    'categories' => ['calls', 'follow_ups', 'meetings', 'visits', 'closing', 'admin_work', 'campaign'],
    'category_labels' => [
        'calls' => 'مكالمات',
        'follow_ups' => 'متابعات',
        'meetings' => 'اجتماعات',
        'visits' => 'معاينات',
        'closing' => 'إغلاق صفقات',
        'admin_work' => 'عمل إداري',
        'campaign' => 'حملات',
    ],

    'assigner_types' => ['admin', 'manager', 'system'],

    'max_open_tasks_per_user' => 25,
    'overload_threshold' => 12,
    'reminder_minutes_before' => 60,
    'acceptance_timeout_hours' => 24,
    'hot_lead_hours' => 2,
    'stuck_pipeline_days' => 5,

    'active_statuses' => ['pending', 'accepted', 'in_progress', 'overdue'],
];

<?php

/**
 * الهرم الوظيفي — من الأعلى للأسفل.
 * مدير العمليات يراجع حضور/غياب جميع الموظفين يومياً.
 */
return [
    'levels' => [
        1 => [
            'key' => 'executive',
            'label' => 'الإدارة العليا',
            'roles' => ['super_admin', 'admin'],
        ],
        2 => [
            'key' => 'operations',
            'label' => 'مدير العمليات',
            'roles' => ['operation_manager'],
            'reviews_all_attendance' => true,
        ],
        3 => [
            'key' => 'department_heads',
            'label' => 'مديرو الأقسام',
            'roles' => ['sales_manager', 'manager', 'marketing_manager', 'hr', 'project_manager'],
        ],
        4 => [
            'key' => 'team_leaders',
            'label' => 'قادة الفرق',
            'roles' => ['sales_team_leader'],
        ],
        5 => [
            'key' => 'staff',
            'label' => 'الموظفون',
            'roles' => ['sales_rep', 'sales_agent', 'marketing_rep', 'employee', 'accountant', 'support', 'designer', 'developer'],
        ],
    ],

    /** من يراجع غياب الموظفين يومياً */
    'attendance_reviewer_roles' => ['operation_manager', 'hr', 'super_admin', 'admin'],

    /** ربط القسم بالمدير المباشر الافتراضي (دور Spatie) */
    'department_default_manager_role' => [
        'SAL' => 'sales_manager',
        'MKT' => 'marketing_manager',
        'OPS' => 'admin',
        'HR' => 'hr',
    ],

    /** ساعة إغلاق مراجعة الغياب يومياً (بعدها يُطبَّق الغياب تلقائياً إن لم تُراجع) */
    'absence_review_deadline_hour' => 12,

    /** ساعة تسجيل الغياب المحتمل تلقائياً */
    'absence_flag_hour' => 18,
];

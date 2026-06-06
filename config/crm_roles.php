<?php

/**
 * أدوار وصلاحيات نظام Solvesta العقاري — المصدر الوحيد للواجهة.
 */
return [
    'assignable_roles' => [
        'super_admin',
        'admin',
        'sales_manager',
        'sales_rep',
        'marketing_manager',
        'marketing_rep',
        'hr',
        'client',
    ],

    'role_aliases' => [
        'manager' => 'sales_manager',
        'sales_agent' => 'sales_rep',
        'employee' => 'sales_rep',
    ],

    'roles' => [
        'super_admin' => [
            'label' => 'مدير النظام',
            'description' => 'صلاحيات كاملة على النظام بالكامل',
            'color' => '#dc2626',
            'workspace' => 'admin',
            'assignable' => false,
        ],
        'admin' => [
            'label' => 'مدير عام',
            'description' => 'إدارة الشركة، الموظفين، CRM، التقارير والإعدادات',
            'color' => '#4f46e5',
            'workspace' => 'admin',
            'assignable' => true,
        ],
        'sales_manager' => [
            'label' => 'مدير مبيعات',
            'description' => 'إدارة فريق المبيعات، المسار، التقارير وتحليلات الأداء',
            'color' => '#0ea5e9',
            'workspace' => 'crm_manager',
            'assignable' => true,
            'legacy_names' => ['manager'],
        ],
        'sales_rep' => [
            'label' => 'موظف مبيعات',
            'description' => 'عملاء، صفقات، مهام، متابعات وتقارير يومية',
            'color' => '#d97706',
            'workspace' => 'crm_rep',
            'assignable' => true,
            'legacy_names' => ['sales_agent', 'employee'],
        ],
        'marketing_manager' => [
            'label' => 'مدير تسويق',
            'description' => 'إدارة الحملات، الفريق، المحتوى والتقارير التسويقية',
            'color' => '#8b5cf6',
            'workspace' => 'marketing_manager',
            'assignable' => true,
        ],
        'marketing_rep' => [
            'label' => 'موظف تسويق',
            'description' => 'تنفيذ الحملات، المحتوى، وجمع العملاء المحتملين',
            'color' => '#a855f7',
            'workspace' => 'marketing_rep',
            'assignable' => true,
        ],
        'hr' => [
            'label' => 'موارد بشرية',
            'description' => 'الموظفين، الحضور، الإجازات والرواتب',
            'color' => '#9333ea',
            'workspace' => 'hr',
            'assignable' => true,
        ],
        'client' => [
            'label' => 'عميل',
            'description' => 'بوابة العميل — مشاريعه وفواتيره وطلباته',
            'color' => '#10b981',
            'workspace' => 'client_portal',
            'assignable' => true,
        ],
    ],

    'permission_groups' => [
        'users' => [
            'label' => 'المستخدمون والأدوار',
            'permissions' => ['view-users', 'create-users', 'edit-users', 'delete-users', 'manage-roles'],
        ],
        'employees' => [
            'label' => 'الموظفون',
            'permissions' => ['view-employees', 'create-employees', 'edit-employees', 'delete-employees'],
        ],
        'crm' => [
            'label' => 'CRM العقاري',
            'permissions' => [
                'view-clients', 'create-clients', 'edit-clients', 'delete-clients',
                'view-sales', 'create-sales', 'edit-sales', 'delete-sales',
                'view-all-projects', 'create-projects', 'edit-projects',
            ],
        ],
        'attendance' => [
            'label' => 'الحضور والإجازات',
            'permissions' => [
                'view-attendance', 'create-attendance', 'edit-attendance',
                'view-leaves', 'create-leaves', 'edit-leaves', 'approve-leaves',
            ],
        ],
        'finance' => [
            'label' => 'المالية والرواتب',
            'permissions' => [
                'view-finance', 'view-salaries', 'create-salaries', 'edit-salaries', 'approve-salaries',
                'view-invoices', 'create-invoices', 'edit-invoices',
                'view-contracts', 'create-contracts', 'edit-contracts',
            ],
        ],
        'reports' => [
            'label' => 'التقارير والتحليلات',
            'permissions' => ['view-reports', 'generate-reports', 'export-reports', 'view-dashboard', 'view-analytics'],
        ],
        'settings' => [
            'label' => 'الإعدادات',
            'permissions' => ['view-settings', 'edit-settings'],
        ],
        'client_portal' => [
            'label' => 'بوابة العميل',
            'permissions' => [
                'view-client-portal', 'view-client-projects', 'view-client-invoices',
                'view-client-tickets', 'create-client-tickets',
            ],
        ],
        'marketing' => [
            'label' => 'التسويق',
            'permissions' => [
                'view-marketing', 'create-marketing', 'edit-marketing', 'delete-marketing', 'manage-marketing',
            ],
        ],
        'other' => [
            'label' => 'أخرى',
            'permissions' => ['view-departments', 'view-training', 'view-meetings', 'view-assets'],
        ],
    ],
];

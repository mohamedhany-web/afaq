<?php

/**
 * أدوار وصلاحيات نظام Solvesta العقاري — المصدر الوحيد للواجهة.
 */
return [
    'assignable_roles' => [
        'super_admin',
        'admin',
        'sales_manager',
        'sales_team_leader',
        'sales_rep',
        'marketing_manager',
        'marketing_rep',
        'operation_manager',
        'hr',
        'client',
    ],

    'role_aliases' => [
        'manager' => 'sales_manager',
        'sales_agent' => 'sales_rep',
        'employee' => 'sales_rep',
    ],

    /*
    | مجموعات مساحات العمل — لعرض وفلترة المستخدمين في /users
    */
    'workspace_groups' => [
        'admin' => [
            'label' => 'الإدارة',
            'description' => 'مدير النظام والإدارة العامة — صلاحيات شاملة',
            'color' => '#4f46e5',
            'roles' => ['super_admin', 'admin'],
            'needs_employee' => false,
        ],
        'sales' => [
            'label' => 'المبيعات',
            'description' => 'مدير مبيعات، قادة الفرق، وموظفي المبيعات — CRM والصفقات',
            'color' => '#0ea5e9',
            'roles' => ['sales_manager', 'sales_team_leader', 'sales_rep'],
            'needs_employee' => true,
            'default_department' => 'المبيعات',
        ],
        'marketing' => [
            'label' => 'التسويق',
            'description' => 'مدير التسويق وموظفي التسويق — الحملات والمحتوى',
            'color' => '#8b5cf6',
            'roles' => ['marketing_manager', 'marketing_rep'],
            'needs_employee' => true,
            'default_department' => 'التسويق',
        ],
        'operations' => [
            'label' => 'العمليات',
            'description' => 'مدير العمليات — المشاريع، المطورين، الحضور والتقارير',
            'color' => '#059669',
            'roles' => ['operation_manager'],
            'needs_employee' => true,
            'default_department' => 'العمليات',
        ],
        'hr' => [
            'label' => 'الموارد البشرية',
            'description' => 'الموظفين، الحضور، الإجازات والرواتب',
            'color' => '#9333ea',
            'roles' => ['hr'],
            'needs_employee' => true,
        ],
        'clients' => [
            'label' => 'العملاء',
            'description' => 'بوابة العميل — مشاريعه وفواتيره',
            'color' => '#10b981',
            'roles' => ['client'],
            'needs_employee' => false,
        ],
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
            'description' => 'إدارة قسم المبيعات، جميع الفرق، الخطط الاستراتيجية والإيرادات',
            'color' => '#0ea5e9',
            'workspace' => 'crm_manager',
            'assignable' => true,
            'legacy_names' => ['manager'],
        ],
        'sales_team_leader' => [
            'label' => 'قائد فريق مبيعات',
            'description' => 'قيادة فريق يومياً، توزيع العملاء، متابعة الأداء، التدريب والإغلاق',
            'color' => '#0284c7',
            'workspace' => 'crm_team_leader',
            'assignable' => true,
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
        'operation_manager' => [
            'label' => 'مدير عمليات',
            'description' => 'إدارة التشغيل، المشاريع، المطورين، التقارير الدورية ومؤشرات الأداء',
            'color' => '#059669',
            'workspace' => 'operations_manager',
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
        'operations' => [
            'label' => 'العمليات',
            'permissions' => [
                'view-all-projects', 'create-projects', 'edit-projects', 'approve-project-changes',
                'view-developers', 'manage-developers',
                'view-all-tasks', 'create-tasks', 'edit-tasks',
            ],
        ],
        'other' => [
            'label' => 'أخرى',
            'permissions' => ['view-departments', 'view-training', 'view-meetings', 'view-assets'],
        ],
    ],
];

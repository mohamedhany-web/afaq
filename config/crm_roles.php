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

    'permission_modules' => [
        ['key' => 'users', 'label' => 'المستخدمون', 'group' => 'admin', 'view' => 'view-users', 'create' => 'create-users', 'edit' => 'edit-users', 'delete' => 'delete-users', 'extras' => [
            'manage-roles' => 'إدارة الأدوار والصلاحيات',
            'view-login-activity' => 'سجل تسجيل الدخول',
            'view-system-monitoring' => 'مراقبة النظام',
        ]],
        ['key' => 'departments', 'label' => 'الأقسام', 'group' => 'admin', 'view' => 'view-departments', 'create' => 'create-departments', 'edit' => 'edit-departments', 'delete' => 'delete-departments'],
        ['key' => 'reports', 'label' => 'التقارير العامة', 'group' => 'admin', 'view' => 'view-reports', 'create' => 'generate-reports', 'edit' => null, 'delete' => null, 'extras' => [
            'export-reports' => 'تصدير التقارير',
            'view-dashboard' => 'لوحة التحكم',
            'view-analytics' => 'التحليلات وذكاء المبيعات',
        ]],
        ['key' => 'settings', 'label' => 'إعدادات النظام', 'group' => 'admin', 'view' => 'view-settings', 'create' => null, 'edit' => 'edit-settings', 'delete' => null],
        ['key' => 'meetings', 'label' => 'الاجتماعات', 'group' => 'admin', 'view' => 'view-meetings', 'create' => 'create-meetings', 'edit' => 'edit-meetings', 'delete' => 'delete-meetings'],
        ['key' => 'assets', 'label' => 'الأصول', 'group' => 'admin', 'view' => 'view-assets', 'create' => 'create-assets', 'edit' => 'edit-assets', 'delete' => 'delete-assets', 'extras' => ['manage-asset-maintenance' => 'صيانة الأصول']],
        ['key' => 'data_management', 'label' => 'إدارة البيانات', 'group' => 'admin', 'view' => 'export-data', 'create' => 'import-data', 'edit' => 'backup-data', 'delete' => 'restore-data'],

        ['key' => 'crm_access', 'label' => 'وصول مساحة CRM', 'group' => 'crm', 'view' => 'access-crm', 'create' => null, 'edit' => null, 'delete' => null, 'extras' => [
            'view-crm-clients' => 'عرض عملاء CRM',
            'view-crm-pipeline' => 'عرض مسار المبيعات',
            'view-team-sales' => 'عرض مبيعات الفريق',
            'manage-sales-teams' => 'إدارة فرق المبيعات',
            'view-crm-intelligence' => 'ذكاء المبيعات (CRM Intelligence)',
        ]],
        ['key' => 'clients', 'label' => 'العملاء — أساسي', 'group' => 'crm', 'view' => 'view-clients', 'create' => 'create-clients', 'edit' => 'edit-clients', 'delete' => 'delete-clients', 'extras' => [
            'transfer-clients' => 'تحويل/توزيع عميل واحد',
            'approve-client-changes' => 'موافقة تعديل/حذف العملاء',
            'import-clients' => 'استيراد العملاء (Excel)',
            'export-clients' => 'تصدير قائمة العملاء',
            'bulk-update-clients' => 'تحديث جماعي (حالة/مصدر)',
            'bulk-delete-clients' => 'حذف جماعي للعملاء',
            'view-client-deletion-log' => 'سجل حذف العملاء',
            'manage-client-staff-notes' => 'ملاحظات الموظفين على العميل',
        ]],
        ['key' => 'lead_distribution', 'label' => 'توزيع العملاء المحتملين', 'group' => 'crm', 'view' => 'distribute-leads', 'create' => null, 'edit' => 'auto-distribute-leads', 'delete' => null],
        ['key' => 'follow_ups', 'label' => 'المتابعات والجدولة', 'group' => 'crm', 'view' => 'view-follow-ups', 'create' => 'manage-follow-ups', 'edit' => null, 'delete' => null],
        ['key' => 'sales', 'label' => 'الصفقات والمسار', 'group' => 'crm', 'view' => 'view-sales', 'create' => 'create-sales', 'edit' => 'edit-sales', 'delete' => 'delete-sales'],
        ['key' => 'daily_sales_reports', 'label' => 'التقارير اليومية للمبيعات', 'group' => 'crm', 'view' => 'view-daily-sales-reports', 'create' => 'manage-daily-sales-reports', 'edit' => null, 'delete' => null],
        ['key' => 'projects', 'label' => 'المشاريع العقارية', 'group' => 'crm', 'view' => 'view-all-projects', 'create' => 'create-projects', 'edit' => 'edit-projects', 'delete' => 'delete-projects', 'extras' => [
            'approve-project-changes' => 'موافقة طلبات المشاريع',
            'view-own-projects' => 'عرض مشاريعي فقط',
            'edit-own-projects' => 'تعديل مشاريعي فقط',
            'manage-project-units' => 'إدارة وحدات المشروع (توليد/ترقيم)',
        ]],
        ['key' => 'tasks', 'label' => 'المهام', 'group' => 'crm', 'view' => 'view-all-tasks', 'create' => 'create-tasks', 'edit' => 'edit-tasks', 'delete' => 'delete-tasks', 'extras' => [
            'view-own-tasks' => 'عرض مهامي فقط',
            'transfer-tasks' => 'تحويل/سحب المهام بين السيلز',
        ]],
        ['key' => 'compensation', 'label' => 'التعويضات والعمولات', 'group' => 'crm', 'view' => 'view-compensation', 'create' => 'manage-compensation', 'edit' => null, 'delete' => null, 'extras' => [
            'approve-compensation-payroll' => 'اعتماد كشوف التعويض',
        ]],
        ['key' => 'employee_compliance', 'label' => 'امتثال الموظفين', 'group' => 'crm', 'view' => 'view-employee-compliance', 'create' => null, 'edit' => null, 'delete' => null],
        ['key' => 'freelance_agents', 'label' => 'وكلاء العمولة المستقلين', 'group' => 'crm', 'view' => 'manage-freelance-agents', 'create' => null, 'edit' => null, 'delete' => null],

        ['key' => 'operations_access', 'label' => 'مساحة العمليات', 'group' => 'operations', 'view' => 'access-operations', 'create' => null, 'edit' => null, 'delete' => null],
        ['key' => 'developers', 'label' => 'المطورون العقاريون', 'group' => 'operations', 'view' => 'view-developers', 'create' => null, 'edit' => 'manage-developers', 'delete' => null],
        ['key' => 'inventory', 'label' => 'مخزون الوحدات', 'group' => 'operations', 'view' => 'view-inventory', 'create' => null, 'edit' => 'export-inventory', 'delete' => null],
        ['key' => 'operations_reports', 'label' => 'تقارير العمليات الدورية', 'group' => 'operations', 'view' => 'view-operations-reports', 'create' => null, 'edit' => 'annotate-operations-reports', 'delete' => null],
        ['key' => 'attendance_reviews', 'label' => 'مراجعات الحضور', 'group' => 'operations', 'view' => 'review-attendance-absence', 'create' => null, 'edit' => 'review-attendance-checkout', 'delete' => null],
        ['key' => 'exit_permits', 'label' => 'تصاريح الخروج', 'group' => 'operations', 'view' => 'manage-exit-permits', 'create' => null, 'edit' => null, 'delete' => null],

        ['key' => 'employees', 'label' => 'الموظفون', 'group' => 'hr', 'view' => 'view-employees', 'create' => 'create-employees', 'edit' => 'edit-employees', 'delete' => 'delete-employees'],
        ['key' => 'attendance', 'label' => 'الحضور', 'group' => 'hr', 'view' => 'view-attendance', 'create' => 'create-attendance', 'edit' => 'edit-attendance', 'delete' => 'delete-attendance'],
        ['key' => 'leaves', 'label' => 'الإجازات', 'group' => 'hr', 'view' => 'view-leaves', 'create' => 'create-leaves', 'edit' => 'edit-leaves', 'delete' => 'delete-leaves', 'extras' => ['approve-leaves' => 'الموافقة على الإجازات']],
        ['key' => 'salaries', 'label' => 'الرواتب', 'group' => 'hr', 'view' => 'view-salaries', 'create' => 'create-salaries', 'edit' => 'edit-salaries', 'delete' => 'delete-salaries', 'extras' => ['approve-salaries' => 'اعتماد الرواتب']],
        ['key' => 'training', 'label' => 'التدريب', 'group' => 'hr', 'view' => 'view-training', 'create' => 'create-training', 'edit' => 'edit-training', 'delete' => 'delete-training'],

        ['key' => 'finance', 'label' => 'المحاسبة', 'group' => 'finance', 'view' => 'view-finance', 'create' => 'create-finance', 'edit' => 'edit-finance', 'delete' => 'delete-finance', 'extras' => ['approve-expenses' => 'اعتماد المصروفات']],
        ['key' => 'invoices', 'label' => 'الفواتير', 'group' => 'finance', 'view' => 'view-invoices', 'create' => 'create-invoices', 'edit' => 'edit-invoices', 'delete' => 'delete-invoices'],
        ['key' => 'contracts', 'label' => 'العقود', 'group' => 'finance', 'view' => 'view-contracts', 'create' => 'create-contracts', 'edit' => 'edit-contracts', 'delete' => 'delete-contracts'],

        ['key' => 'marketing', 'label' => 'التسويق', 'group' => 'marketing', 'view' => 'view-marketing', 'create' => 'create-marketing', 'edit' => 'edit-marketing', 'delete' => 'delete-marketing', 'extras' => ['manage-marketing' => 'إدارة التسويق']],

        ['key' => 'tickets', 'label' => 'تذاكر الدعم', 'group' => 'support', 'view' => 'view-tickets', 'create' => 'create-tickets', 'edit' => 'edit-tickets', 'delete' => 'delete-tickets'],
        ['key' => 'messages', 'label' => 'الرسائل الداخلية', 'group' => 'support', 'view' => 'view-messages', 'create' => 'create-messages', 'edit' => 'edit-messages', 'delete' => 'delete-messages', 'extras' => [
            'send-messages' => 'إرسال رسائل',
            'reply-messages' => 'الرد على الرسائل',
            'mark-messages-important' => 'تعليم الرسائل كمهمة',
            'view-all-messages' => 'عرض جميع رسائل النظام',
            'send-announcements' => 'إرسال إعلانات عامة',
            'send-group-messages' => 'إرسال رسائل جماعية',
        ]],

        ['key' => 'client_portal', 'label' => 'بوابة العميل', 'group' => 'portal', 'view' => 'view-client-portal', 'create' => 'create-client-tickets', 'edit' => null, 'delete' => null, 'extras' => [
            'view-client-projects' => 'مشاريع العميل',
            'view-client-invoices' => 'فواتير العميل',
            'view-client-tickets' => 'تذاكر العميل',
        ]],

        ['key' => 'design', 'label' => 'التصميم (قديم)', 'group' => 'legacy', 'view' => 'view-design', 'create' => 'create-design', 'edit' => 'edit-design', 'delete' => 'delete-design', 'extras' => ['manage-design' => 'إدارة التصميم']],
        ['key' => 'bugs', 'label' => 'الأخطاء (قديم)', 'group' => 'legacy', 'view' => 'view-bugs', 'create' => 'create-bugs', 'edit' => 'edit-bugs', 'delete' => 'delete-bugs'],
        ['key' => 'qa', 'label' => 'اختبارات الجودة (قديم)', 'group' => 'legacy', 'view' => 'view-qa', 'create' => 'create-qa', 'edit' => 'edit-qa', 'delete' => 'delete-qa'],
    ],

    /** @deprecated استخدم permission_modules — يُبنى تلقائياً للتوافق */
    'permission_groups' => [],
];

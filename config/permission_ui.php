<?php

/**
 * ربط الصلاحيات بعناصر الواجهة (سايدبار / صفحات).
 * يُستخدم في شاشة إدارة الصلاحيات لمعاينة ما يظهر للمستخدم.
 */
return [
    'sidebar_items' => [
        'view-clients' => ['label' => 'قائمة العملاء', 'route' => 'crm.clients.index'],
        'create-clients' => ['label' => 'إضافة عميل', 'route' => 'crm.clients.create'],
        'edit-clients' => ['label' => 'تعديل بيانات العميل', 'route' => 'crm.clients.edit'],
        'delete-clients' => ['label' => 'حذف عميل', 'route' => null],
        'approve-client-changes' => ['label' => 'موافقات تعديل العملاء', 'route' => 'crm.clients.approvals.index'],

        'view-sales' => ['label' => 'مسار المبيعات', 'route' => 'crm.pipeline.index'],
        'create-sales' => ['label' => 'صفقة جديدة', 'route' => 'crm.pipeline.create'],
        'edit-sales' => ['label' => 'تعديل صفقة', 'route' => null],
        'delete-sales' => ['label' => 'حذف صفقة', 'route' => null],

        'view-all-projects' => ['label' => 'المشاريع العقارية', 'route' => 'crm.projects.index'],
        'create-projects' => ['label' => 'إنشاء مشروع', 'route' => 'crm.projects.create'],
        'edit-projects' => ['label' => 'تعديل مشروع', 'route' => 'crm.projects.edit'],
        'delete-projects' => ['label' => 'حذف مشروع', 'route' => null],
        'approve-project-changes' => ['label' => 'موافقات المشاريع', 'route' => 'crm.projects.approvals.index'],

        'view-developers' => ['label' => 'المطورون العقاريون', 'route' => 'admin.developers.index'],
        'manage-developers' => ['label' => 'إدارة المطورين', 'route' => 'admin.developers.index'],

        'manage-sales-teams' => ['label' => 'إدارة فرق المبيعات', 'route' => 'crm.teams.index'],
        'access-crm' => ['label' => 'مساحة CRM', 'route' => 'crm.dashboard'],

        'view-all-tasks' => ['label' => 'المهام', 'route' => 'crm.tasks.index'],
        'create-tasks' => ['label' => 'إنشاء مهمة', 'route' => 'crm.tasks.create'],
        'edit-tasks' => ['label' => 'تعديل مهمة', 'route' => null],
        'delete-tasks' => ['label' => 'حذف مهمة', 'route' => null],

        'view-users' => ['label' => 'المستخدمون', 'route' => 'users.index'],
        'manage-roles' => ['label' => 'الأدوار والصلاحيات', 'route' => 'roles.index'],

        'view-employees' => ['label' => 'الموظفون', 'route' => 'employees.index'],
        'view-attendance' => ['label' => 'الحضور والانصراف', 'route' => 'attendances.index'],
        'view-leaves' => ['label' => 'الإجازات', 'route' => 'leaves.index'],
        'view-salaries' => ['label' => 'الرواتب', 'route' => 'salaries.index'],
        'view-invoices' => ['label' => 'الفواتير', 'route' => 'invoices.index'],
        'view-contracts' => ['label' => 'العقود', 'route' => 'contracts.index'],
        'view-finance' => ['label' => 'المحاسبة', 'route' => 'accounting.index'],
        'view-reports' => ['label' => 'التقارير', 'route' => 'reports.index'],
        'view-settings' => ['label' => 'الإعدادات', 'route' => 'settings.index'],

        'view-marketing' => ['label' => 'التسويق', 'route' => 'marketing.dashboard'],
        'manage-marketing' => ['label' => 'إدارة التسويق', 'route' => 'marketing.dashboard'],

        'view-departments' => ['label' => 'الأقسام', 'route' => 'departments.index'],
        'view-tickets' => ['label' => 'تذاكر الدعم', 'route' => 'tickets.index'],
        'view-dashboard' => ['label' => 'لوحة التحكم', 'route' => 'dashboard'],
        'view-analytics' => ['label' => 'ذكاء المبيعات', 'route' => 'crm.intelligence.index'],
        'view-crm-intelligence' => ['label' => 'ذكاء المبيعات', 'route' => 'crm.intelligence.index'],
        'access-operations' => ['label' => 'مساحة العمليات', 'route' => 'operations.dashboard'],
        'view-messages' => ['label' => 'الرسائل', 'route' => 'messages.index'],
        'view-login-activity' => ['label' => 'سجل الدخول', 'route' => 'login-activity.index'],
        'view-compensation' => ['label' => 'التعويضات', 'route' => 'crm.compensation.dashboard'],
        'view-follow-ups' => ['label' => 'المتابعات', 'route' => 'crm.follow-ups.index'],
        'view-daily-sales-reports' => ['label' => 'التقارير اليومية', 'route' => 'crm.daily-reports.index'],
        'view-inventory' => ['label' => 'مخزون الوحدات', 'route' => 'operations.inventory.index'],
    ],
];

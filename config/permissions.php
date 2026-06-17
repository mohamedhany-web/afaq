<?php

/**
 * السجل المركزي لصلاحيات النظام.
 *
 * عند إضافة ميزة جديدة:
 * 1. أضف مفاتيح الصلاحية هنا في registry
 * 2. أضف وحدة عرض في config/crm_roles.php → permission_modules
 * 3. شغّل: php artisan permissions:sync
 * 4. أضف التسمية العربية في App\Helpers\RoleHelper::getPermissionName
 * 5. اربط المسارات بـ middleware('permission:...') أو @can
 */
return [
  'registry' => [
    // المستخدمون والأدوار
    'view-users', 'create-users', 'edit-users', 'delete-users', 'manage-roles',

    // الموظفون
    'view-employees', 'create-employees', 'edit-employees', 'delete-employees',

    // العملاء
    'view-clients', 'create-clients', 'edit-clients', 'delete-clients', 'approve-client-changes',

    // CRM — وصول ومساحة العمل
    'access-crm', 'view-crm-clients', 'view-crm-pipeline', 'view-team-sales', 'manage-sales-teams',

    // الصفقات
    'view-sales', 'create-sales', 'edit-sales', 'delete-sales',

    // المشاريع
    'view-all-projects', 'view-own-projects', 'create-projects', 'edit-projects', 'delete-projects', 'approve-project-changes',

    // المطورون
    'view-developers', 'manage-developers',

    // المهام
    'view-all-tasks', 'view-own-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks',

    // التسويق
    'view-marketing', 'create-marketing', 'edit-marketing', 'delete-marketing', 'manage-marketing',

    // الحضور والإجازات
    'view-attendance', 'create-attendance', 'edit-attendance', 'delete-attendance',
    'view-leaves', 'create-leaves', 'edit-leaves', 'delete-leaves', 'approve-leaves',

    // الرواتب والمالية
    'view-salaries', 'create-salaries', 'edit-salaries', 'delete-salaries', 'approve-salaries',
    'view-finance', 'create-finance', 'edit-finance', 'delete-finance', 'approve-expenses',
    'view-invoices', 'create-invoices', 'edit-invoices', 'delete-invoices',
    'view-contracts', 'create-contracts', 'edit-contracts', 'delete-contracts',

    // الأقسام والتذاكر
    'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
    'view-tickets', 'create-tickets', 'edit-tickets', 'delete-tickets',

    // التدريب والاجتماعات
    'view-training', 'create-training', 'edit-training', 'delete-training',
    'view-meetings', 'create-meetings', 'edit-meetings', 'delete-meetings',

    // التقارير ولوحة التحكم
    'view-reports', 'generate-reports', 'export-reports', 'view-dashboard', 'view-analytics',

    // الإعدادات
    'view-settings', 'edit-settings',

    // الأصول
    'view-assets', 'create-assets', 'edit-assets', 'delete-assets', 'manage-asset-maintenance',

    // إدارة البيانات
    'export-data', 'import-data', 'backup-data', 'restore-data',

    // بوابة العميل
    'view-client-portal', 'view-client-projects', 'view-client-invoices', 'view-client-tickets', 'create-client-tickets',

    // وحدات قديمة (ما زالت في قاعدة البيانات — للتوافق)
    'view-design', 'create-design', 'edit-design', 'delete-design', 'manage-design',
    'view-bugs', 'create-bugs', 'edit-bugs', 'delete-bugs',
    'view-qa', 'create-qa', 'edit-qa', 'delete-qa',
  ],
];

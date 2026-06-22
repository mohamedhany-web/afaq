<?php

namespace App\Helpers;

class RoleHelper
{
    /**
     * Get all role names in Arabic
     */
    public static function getRoleNames(): array
    {
        $fromConfig = collect(config('crm_roles.roles', []))
            ->mapWithKeys(fn ($meta, $key) => [$key => $meta['label']])
            ->all();

        return array_merge($fromConfig, [
            'manager' => 'مدير مبيعات',
            'sales_agent' => 'موظف مبيعات',
            'employee' => 'موظف مبيعات',
            'marketing_manager' => 'مدير تسويق',
            'marketing_rep' => 'موظف تسويق',
            'client' => 'عميل',
        ]);
    }

    /**
     * Get role name in Arabic
     */
    public static function getRoleName(string $role): string
    {
        return self::getRoleNames()[$role] ?? $role;
    }

    /**
     * Get role descriptions
     */
    public static function getRoleDescriptions(): array
    {
        return collect(config('crm_roles.roles', []))
            ->mapWithKeys(fn ($meta, $key) => [$key => $meta['description']])
            ->all();
    }

    /**
     * Get role description
     */
    public static function getRoleDescription(string $role): string
    {
        return self::getRoleDescriptions()[$role] ?? '';
    }

    /**
     * Get role colors
     */
    public static function getRoleColors(): array
    {
        return [
            'super_admin' => 'red',
            'admin' => 'indigo',
            'project_manager' => 'blue',
            'employee' => 'green',
            'hr' => 'purple',
            'accountant' => 'yellow',
            'sales_rep' => 'orange',
            'support' => 'pink',
            'developer' => 'cyan',
            'designer' => 'teal',
        ];
    }

    /**
     * Get role color
     */
    public static function getRoleColor(string $role): string
    {
        return self::getRoleColors()[$role] ?? 'gray';
    }

    /**
     * Get permission name in Arabic
     */
    public static function getPermissionName(string $permission): string
    {
        $names = [
            // User Management
            'view-users' => 'عرض المستخدمين',
            'create-users' => 'إنشاء مستخدمين',
            'edit-users' => 'تعديل مستخدمين',
            'delete-users' => 'حذف مستخدمين',
            
            // Employee Management
            'view-employees' => 'عرض الموظفين',
            'create-employees' => 'إنشاء موظفين',
            'edit-employees' => 'تعديل موظفين',
            'delete-employees' => 'حذف موظفين',
            
            // Project Management
            'view-all-projects' => 'عرض جميع المشاريع',
            'view-own-projects' => 'عرض المشاريع الخاصة',
            'create-projects' => 'إنشاء مشاريع',
            'edit-projects' => 'تعديل مشاريع',
            'delete-projects' => 'حذف مشاريع',
            'approve-project-changes' => 'الموافقة على طلبات المشاريع',
            
            // Task Management
            'view-all-tasks' => 'عرض جميع المهام',
            'view-own-tasks' => 'عرض المهام الخاصة',
            'create-tasks' => 'إنشاء مهام',
            'edit-tasks' => 'تعديل مهام',
            'delete-tasks' => 'حذف مهام',
            
            // Client Management
            'view-clients' => 'عرض العملاء',
            'create-clients' => 'إنشاء عملاء',
            'edit-clients' => 'تعديل عملاء',
            'delete-clients' => 'حذف عملاء',
            'transfer-clients' => 'تحويل/توزيع العملاء',
            'approve-client-changes' => 'موافقة تعديل/حذف العملاء',
            'transfer-tasks' => 'تحويل/سحب المهام بين السيلز',

            'view-developers' => 'عرض المطورين العقاريين',
            'manage-developers' => 'إدارة المطورين العقاريين',

            'view-marketing' => 'عرض التسويق',
            'create-marketing' => 'إنشاء حملات تسويق',
            'edit-marketing' => 'تعديل التسويق',
            'delete-marketing' => 'حذف التسويق',
            'manage-marketing' => 'إدارة التسويق',

            'access-crm' => 'الوصول لمساحة CRM',
            'view-crm-clients' => 'عرض عملاء CRM',
            'view-crm-pipeline' => 'عرض مسار CRM',
            'view-team-sales' => 'عرض مبيعات الفريق',
            'manage-sales-teams' => 'إدارة فرق المبيعات',

            'view-design' => 'عرض التصميم',
            'create-design' => 'إنشاء تصميم',
            'edit-design' => 'تعديل التصميم',
            'delete-design' => 'حذف التصميم',
            'manage-design' => 'إدارة التصميم',

            // Sales Management
            'view-sales' => 'عرض المبيعات',
            'create-sales' => 'إنشاء مبيعات',
            'edit-sales' => 'تعديل مبيعات',
            'delete-sales' => 'حذف مبيعات',
            
            // Finance & Accounting
            'view-finance' => 'عرض المالية',
            'create-finance' => 'إنشاء عمليات مالية',
            'edit-finance' => 'تعديل عمليات مالية',
            'delete-finance' => 'حذف عمليات مالية',
            'approve-expenses' => 'الموافقة على المصروفات',
            
            // Attendance
            'view-attendance' => 'عرض الحضور',
            'create-attendance' => 'تسجيل حضور',
            'edit-attendance' => 'تعديل حضور',
            'delete-attendance' => 'حذف حضور',
            
            // Leaves
            'view-leaves' => 'عرض الإجازات',
            'create-leaves' => 'إنشاء إجازات',
            'edit-leaves' => 'تعديل إجازات',
            'delete-leaves' => 'حذف إجازات',
            'approve-leaves' => 'الموافقة على الإجازات',
            
            // Salaries
            'view-salaries' => 'عرض الرواتب',
            'create-salaries' => 'إنشاء رواتب',
            'edit-salaries' => 'تعديل رواتب',
            'approve-salaries' => 'اعتماد الرواتب',
            
            // Client portal
            'view-client-portal' => 'بوابة العميل',
            'view-client-projects' => 'مشاريع العميل (بوابة)',
            'view-client-invoices' => 'فواتير العميل (بوابة)',
            'view-client-tickets' => 'تذاكر العميل (بوابة)',
            'create-client-tickets' => 'إنشاء تذكرة (بوابة)',
            
            // Sales Management
            'view-invoices' => 'عرض الفواتير',
            'create-invoices' => 'إنشاء فواتير',
            'edit-invoices' => 'تعديل فواتير',
            'delete-invoices' => 'حذف فواتير',
            
            // Contracts
            'view-contracts' => 'عرض العقود',
            'create-contracts' => 'إنشاء عقود',
            'edit-contracts' => 'تعديل عقود',
            'delete-contracts' => 'حذف عقود',
            
            // Bugs
            'view-bugs' => 'عرض الأخطاء',
            'create-bugs' => 'إنشاء أخطاء',
            'edit-bugs' => 'تعديل أخطاء',
            'delete-bugs' => 'حذف أخطاء',
            
            // QA
            'view-qa' => 'عرض اختبارات الجودة',
            'create-qa' => 'إنشاء اختبارات',
            'edit-qa' => 'تعديل اختبارات',
            'delete-qa' => 'حذف اختبارات',
            
            // Tickets
            'view-tickets' => 'عرض التذاكر',
            'create-tickets' => 'إنشاء تذاكر',
            'edit-tickets' => 'تعديل تذاكر',
            'delete-tickets' => 'حذف تذاكر',
            
            // Departments
            'view-departments' => 'عرض الأقسام',
            'create-departments' => 'إنشاء أقسام',
            'edit-departments' => 'تعديل أقسام',
            'delete-departments' => 'حذف أقسام',
            
            // Reports
            'view-reports' => 'عرض التقارير',
            'generate-reports' => 'إنشاء تقارير',
            'export-reports' => 'تصدير تقارير',
            
            // Dashboard
            'view-dashboard' => 'عرض لوحة التحكم',
            'view-analytics' => 'عرض التحليلات',
            
            // Settings
            'view-settings' => 'عرض الإعدادات',
            'edit-settings' => 'تعديل الإعدادات',
            'manage-roles' => 'إدارة الأدوار والصلاحيات',
            
            // Training & Development
            'view-training' => 'عرض التدريب',
            'create-training' => 'إنشاء برامج تدريبية',
            'edit-training' => 'تعديل برامج تدريبية',
            'delete-training' => 'حذف برامج تدريبية',
            
            // Meetings & Conferences
            'view-meetings' => 'عرض الاجتماعات',
            'create-meetings' => 'إنشاء اجتماعات',
            'edit-meetings' => 'تعديل اجتماعات',
            'delete-meetings' => 'حذف اجتماعات',
            
            // Assets Management
            'view-assets' => 'عرض الأصول',
            'create-assets' => 'إنشاء أصول',
            'edit-assets' => 'تعديل أصول',
            'delete-assets' => 'حذف أصول',
            'manage-asset-maintenance' => 'إدارة صيانة الأصول',
            
            // Data Management
            'export-data' => 'تصدير البيانات',
            'import-data' => 'استيراد البيانات',
            'backup-data' => 'نسخ احتياطي للبيانات',
            'restore-data' => 'استعادة البيانات',
        ];
        
        return $names[$permission] ?? str_replace('-', ' ', $permission);
    }
}


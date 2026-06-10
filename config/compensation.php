<?php

return [
    'evaluation_periods' => ['daily', 'weekly', 'monthly', 'quarterly'],

    'evaluation_period_labels' => [
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
        'quarterly' => 'ربع سنوي',
    ],

    'target_roles' => ['rep', 'team_leader', 'manager', 'operation_manager'],

    'target_role_labels' => [
        'rep' => 'مندوب مبيعات',
        'team_leader' => 'قائد فريق مبيعات',
        'manager' => 'مدير مبيعات',
        'operation_manager' => 'مدير عمليات',
    ],

    'commission_models' => [
        'percentage' => 'نسبة من قيمة الصفقة',
        'fixed_per_deal' => 'مبلغ ثابت لكل صفقة',
        'revenue_tier' => 'شرائح حسب الإيراد',
        'hybrid' => 'هجين (أساسي + حافز)',
        'freelance_scheme' => 'هيكل عمولات الوكيل المستقل',
    ],

    'bonus_amount_types' => ['fixed', 'percent_salary', 'percent_revenue'],

    'deduction_amount_types' => ['fixed', 'percent_salary'],

    'adjustment_statuses' => ['pending', 'approved', 'rejected'],

    'payroll_run_statuses' => ['draft', 'pending_approval', 'approved', 'paid'],

    'performance_levels' => [
        ['min' => 95, 'max' => 100, 'key' => 'outstanding', 'label' => 'متميز'],
        ['min' => 85, 'max' => 94.99, 'key' => 'excellent', 'label' => 'ممتاز'],
        ['min' => 75, 'max' => 84.99, 'key' => 'good', 'label' => 'جيد'],
        ['min' => 60, 'max' => 74.99, 'key' => 'needs_improvement', 'label' => 'يحتاج تحسين'],
        ['min' => 0, 'max' => 59.99, 'key' => 'critical', 'label' => 'حرج'],
    ],

    'rep_kpi_slugs' => [
        'leads_contacted' => 'عملاء تم التواصل معهم',
        'follow_ups_completed' => 'متابعات منجزة',
        'property_visits' => 'معاينات عقارية',
        'qualified_leads' => 'عملاء مؤهلون',
        'conversion_rate' => 'معدل التحويل %',
        'closed_deals' => 'صفقات مغلقة',
        'revenue_generated' => 'إيراد محقق',
        'crm_compliance' => 'التزام التقارير %',
        'attendance_compliance' => 'التزام الحضور %',
        'report_compliance' => 'رفع التقارير %',
    ],

    'manager_kpi_slugs' => [
        'team_revenue' => 'إيراد الفريق',
        'team_conversion_rate' => 'تحويل الفريق %',
        'team_target_achievement' => 'تحقيق هدف الفريق %',
        'lead_distribution' => 'كفاءة توزيع العملاء',
        'follow_up_compliance' => 'التزام المتابعات %',
        'team_productivity' => 'إنتاجية الفريق',
        'team_retention' => 'استبقاء الفريق %',
    ],

    'rep_kpi_defaults' => [
        ['slug' => 'leads_contacted', 'weight' => 5, 'target_value' => 80],
        ['slug' => 'follow_ups_completed', 'weight' => 15, 'target_value' => 60],
        ['slug' => 'property_visits', 'weight' => 15, 'target_value' => 20],
        ['slug' => 'qualified_leads', 'weight' => 10, 'target_value' => 25],
        ['slug' => 'conversion_rate', 'weight' => 10, 'target_value' => 25],
        ['slug' => 'closed_deals', 'weight' => 20, 'target_value' => 8],
        ['slug' => 'revenue_generated', 'weight' => 20, 'target_value' => 2000000],
        ['slug' => 'crm_compliance', 'weight' => 5, 'target_value' => 90],
        ['slug' => 'attendance_compliance', 'weight' => 5, 'target_value' => 90],
    ],

    'manager_kpi_defaults' => [
        ['slug' => 'team_revenue', 'weight' => 25, 'target_value' => 10000000],
        ['slug' => 'team_conversion_rate', 'weight' => 15, 'target_value' => 30],
        ['slug' => 'team_target_achievement', 'weight' => 20, 'target_value' => 100],
        ['slug' => 'lead_distribution', 'weight' => 10, 'target_value' => 50],
        ['slug' => 'follow_up_compliance', 'weight' => 15, 'target_value' => 85],
        ['slug' => 'team_productivity', 'weight' => 10, 'target_value' => 100],
        ['slug' => 'team_retention', 'weight' => 5, 'target_value' => 95],
    ],

    'team_leader_kpi_slugs' => [
        'team_revenue' => 'إيراد الفريق',
        'team_target_achievement' => 'تحقيق التارجت %',
        'team_closing_rate' => 'معدل إغلاق الفريق %',
        'follow_up_compliance' => 'التزام المتابعات %',
        'lead_leakage_rate' => 'العملاء المفقودون %',
        'team_attendance_rate' => 'التزام الحضور %',
        'crm_compliance' => 'التزام CRM %',
        'pipeline_accuracy' => 'دقة Pipeline %',
        'closed_deals' => 'صفقات مغلقة',
        'reservation_value' => 'قيمة الحجوزات',
        'team_productivity' => 'إنتاجية الفريق',
        'training_completion_rate' => 'اكتمال التدريب %',
    ],

    'team_leader_kpi_defaults' => [
        ['slug' => 'team_revenue', 'weight' => 20, 'target_value' => 3000000],
        ['slug' => 'team_target_achievement', 'weight' => 20, 'target_value' => 100],
        ['slug' => 'team_closing_rate', 'weight' => 15, 'target_value' => 30],
        ['slug' => 'follow_up_compliance', 'weight' => 15, 'target_value' => 95],
        ['slug' => 'lead_leakage_rate', 'weight' => 10, 'target_value' => 2],
        ['slug' => 'crm_compliance', 'weight' => 10, 'target_value' => 95],
        ['slug' => 'pipeline_accuracy', 'weight' => 5, 'target_value' => 95],
        ['slug' => 'team_attendance_rate', 'weight' => 5, 'target_value' => 95],
    ],

    'operation_manager_kpi_slugs' => [
        'contact_rate' => 'نسبة التواصل %',
        'lead_distribution_time' => 'زمن توزيع العميل (د)',
        'lead_leakage_rate' => 'نسبة العملاء المفقودين %',
        'crm_compliance_rate' => 'التزام CRM %',
        'pipeline_update_rate' => 'تحديث Pipeline %',
        'lead_to_meeting_conversion' => 'تحويل لمعاينة %',
        'reservation_to_contract_conversion' => 'تحويل لتعاقد %',
        'inventory_accuracy' => 'دقة المخزون %',
        'follow_up_compliance' => 'التزام المتابعات %',
        'report_delivery_time' => 'التزام التقارير %',
        'projects_on_track_pct' => 'مشاريع على المسار %',
        'team_attendance_pct' => 'حضور الفريق %',
        'operational_efficiency' => 'كفاءة تشغيلية %',
    ],

    'operation_manager_kpi_defaults' => [
        ['slug' => 'contact_rate', 'weight' => 10, 'target_value' => 100],
        ['slug' => 'lead_distribution_time', 'weight' => 8, 'target_value' => 100],
        ['slug' => 'lead_leakage_rate', 'weight' => 8, 'target_value' => 100],
        ['slug' => 'crm_compliance_rate', 'weight' => 10, 'target_value' => 100],
        ['slug' => 'pipeline_update_rate', 'weight' => 8, 'target_value' => 100],
        ['slug' => 'lead_to_meeting_conversion', 'weight' => 10, 'target_value' => 100],
        ['slug' => 'reservation_to_contract_conversion', 'weight' => 10, 'target_value' => 100],
        ['slug' => 'inventory_accuracy', 'weight' => 8, 'target_value' => 100],
        ['slug' => 'follow_up_compliance', 'weight' => 8, 'target_value' => 100],
        ['slug' => 'report_delivery_time', 'weight' => 8, 'target_value' => 100],
        ['slug' => 'projects_on_track_pct', 'weight' => 6, 'target_value' => 100],
        ['slug' => 'team_attendance_pct', 'weight' => 6, 'target_value' => 100],
    ],
];

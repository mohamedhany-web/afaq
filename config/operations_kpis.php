<?php

/**
 * مؤشرات أداء مدير العمليات العقاري — 7 مجموعات.
 * direction: higher = الأعلى أفضل | lower = الأقل أفضل
 */
return [
    'groups' => [
        'lead_management' => [
            'label' => 'إدارة العملاء المحتملين',
            'icon' => 'users',
            'kpis' => [
                'lead_response_time' => ['label' => 'زمن الرد على العميل', 'unit' => 'دقيقة', 'target' => 5, 'direction' => 'lower'],
                'lead_distribution_time' => ['label' => 'زمن توزيع العميل', 'unit' => 'دقيقة', 'target' => 2, 'direction' => 'lower'],
                'lead_leakage_rate' => ['label' => 'نسبة العملاء المفقودين', 'unit' => '%', 'target' => 2, 'direction' => 'lower'],
                'contact_rate' => ['label' => 'نسبة التواصل', 'unit' => '%', 'target' => 90, 'direction' => 'higher'],
            ],
        ],
        'crm_management' => [
            'label' => 'إدارة CRM',
            'icon' => 'database',
            'kpis' => [
                'crm_compliance_rate' => ['label' => 'التزام استخدام النظام', 'unit' => '%', 'target' => 95, 'direction' => 'higher'],
                'data_accuracy_rate' => ['label' => 'دقة البيانات', 'unit' => '%', 'target' => 98, 'direction' => 'higher'],
                'duplicate_records_rate' => ['label' => 'نسبة التكرار', 'unit' => '%', 'target' => 1, 'direction' => 'lower'],
                'pipeline_update_rate' => ['label' => 'تحديث الـ Pipeline', 'unit' => '%', 'target' => 95, 'direction' => 'higher'],
            ],
        ],
        'sales_operations' => [
            'label' => 'عمليات المبيعات',
            'icon' => 'chart',
            'kpis' => [
                'lead_to_meeting_conversion' => ['label' => 'تحويل لاجتماع', 'unit' => '%', 'target' => 35, 'direction' => 'higher'],
                'meeting_to_reservation_conversion' => ['label' => 'تحويل لحجز', 'unit' => '%', 'target' => 25, 'direction' => 'higher'],
                'reservation_to_contract_conversion' => ['label' => 'تحويل لتعاقد', 'unit' => '%', 'target' => 60, 'direction' => 'higher'],
                'sales_cycle_duration' => ['label' => 'مدة دورة البيع', 'unit' => 'يوم', 'target' => 45, 'direction' => 'lower'],
            ],
        ],
        'revenue_impact' => [
            'label' => 'الأثر على الإيرادات',
            'icon' => 'currency',
            'kpis' => [
                'revenue_growth_support' => ['label' => 'دعم نمو الإيرادات', 'unit' => '%', 'target' => 10, 'direction' => 'higher'],
                'lost_opportunity_recovery' => ['label' => 'استعادة فرص ضائعة', 'unit' => 'عدد', 'target' => 5, 'direction' => 'higher'],
                'cost_per_sale_reduction' => ['label' => 'خفض تكلفة البيع', 'unit' => '%', 'target' => 5, 'direction' => 'higher'],
                'marketing_roi_improvement' => ['label' => 'تحسين عائد التسويق', 'unit' => '%', 'target' => 15, 'direction' => 'higher'],
            ],
        ],
        'inventory_operations' => [
            'label' => 'عمليات المخزون',
            'icon' => 'building',
            'kpis' => [
                'inventory_accuracy' => ['label' => 'دقة بيانات الوحدات', 'unit' => '%', 'target' => 100, 'direction' => 'higher'],
                'unit_availability_accuracy' => ['label' => 'دقة حالة الوحدات', 'unit' => '%', 'target' => 100, 'direction' => 'higher'],
                'double_booking_incidents' => ['label' => 'حجز مكرر', 'unit' => 'عدد', 'target' => 0, 'direction' => 'lower'],
                'active_inventory_units' => ['label' => 'وحدات متاحة', 'unit' => 'عدد', 'target' => 50, 'direction' => 'higher'],
            ],
        ],
        'team_performance' => [
            'label' => 'أداء الفريق',
            'icon' => 'team',
            'kpis' => [
                'sales_activity_compliance' => ['label' => 'التزام تسجيل الأنشطة', 'unit' => '%', 'target' => 95, 'direction' => 'higher'],
                'follow_up_compliance' => ['label' => 'التزام المتابعات', 'unit' => '%', 'target' => 95, 'direction' => 'higher'],
                'employee_productivity_score' => ['label' => 'إنتاجية الموظفين', 'unit' => 'نقطة', 'target' => 80, 'direction' => 'higher'],
                'training_completion_rate' => ['label' => 'إتمام التدريب', 'unit' => '%', 'target' => 100, 'direction' => 'higher'],
            ],
        ],
        'reporting_management' => [
            'label' => 'التقارير والإدارة',
            'icon' => 'report',
            'kpis' => [
                'report_accuracy' => ['label' => 'دقة التقارير', 'unit' => '%', 'target' => 100, 'direction' => 'higher'],
                'report_delivery_time' => ['label' => 'التزام مواعيد التقارير', 'unit' => '%', 'target' => 100, 'direction' => 'higher'],
                'dashboard_freshness' => ['label' => 'حداثة البيانات', 'unit' => '%', 'target' => 100, 'direction' => 'higher'],
                'reports_submitted' => ['label' => 'تقارير مُرفوعة', 'unit' => 'عدد', 'target' => 12, 'direction' => 'higher'],
            ],
        ],
    ],

    /** مؤشرات الزمن والتواصل — تُعرض مطوية في لوحة العمليات */
    'timing_slugs' => [
        'lead_response_time',
        'lead_distribution_time',
        'contact_rate',
        'sales_cycle_duration',
        'report_delivery_time',
    ],

    /** مؤشرات تُستخدم في قالب التعويضات (مرجّحة) */
    'compensation_slugs' => [
        'contact_rate',
        'lead_distribution_time',
        'lead_leakage_rate',
        'crm_compliance_rate',
        'pipeline_update_rate',
        'lead_to_meeting_conversion',
        'reservation_to_contract_conversion',
        'inventory_accuracy',
        'follow_up_compliance',
        'report_delivery_time',
        'projects_on_track_pct',
        'team_attendance_pct',
    ],
];

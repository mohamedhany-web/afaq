<?php

return [
    'period_types' => [
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
    ],

    'mandatory_for_manager' => ['daily', 'weekly', 'monthly'],

    'metric_labels' => [
        'contact_rate' => 'نسبة التواصل %',
        'lead_distribution_time' => 'زمن التوزيع (د)',
        'lead_leakage_rate' => 'نسبة الفقد %',
        'crm_compliance_rate' => 'التزام CRM %',
        'pipeline_update_rate' => 'تحديث Pipeline %',
        'inventory_accuracy' => 'دقة المخزون %',
        'follow_up_compliance' => 'التزام المتابعات %',
        'projects_on_track_pct' => 'مشاريع على المسار %',
        'team_attendance_pct' => 'حضور الفريق %',
        'reports_submitted' => 'تقارير مُرفوعة',
        'open_issues' => 'قضايا مفتوحة (غير موزّع + متعثر)',
        'unassigned_leads' => 'عملاء بانتظار التوزيع',
        'stale_leads' => 'عملاء متعثرون',
    ],
];

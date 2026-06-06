<?php

return [
    'daily_report_deadline_hour' => 18,

    'departments' => [
        'SAL' => 'المبيعات',
        'MKT' => 'التسويق',
    ],

    'source_types' => [
        'crm_task' => 'مهمة مبيعات متأخرة',
        'crm_follow_up' => 'متابعة عميل متأخرة',
        'daily_sales_report' => 'تقرير مبيعات يومي غير مرفوع',
        'marketing_activity' => 'نشاط تسويق متأخر',
        'marketing_report' => 'تقرير تسويق دوري غير مرفوع',
    ],

    'applies_to' => [
        'all' => 'الجميع (مدير وموظف)',
        'manager' => 'المديرون فقط',
        'employee' => 'الموظفون فقط',
    ],

    'report_period_types' => [
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
    ],
];

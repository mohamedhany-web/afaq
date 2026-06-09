<?php

return [
    'daily_report_deadline_hour' => 18,

    /** بعد بداية الدوام + سماح التأخير — ساعات إضافية قبل اعتبار «لم يبدأ يوم العمل» */
    'no_start_grace_hours_after_shift' => 2,

    /** حد KPI الشهري — أقل منه يُطبَّق خصم تلقائي (أول 7 أيام من الشهر للشهر السابق) */
    'kpi_penalty_threshold' => 60,
    'kpi_penalty_until_day' => 7,

    /** كل 30 دقيقة تأخير = مضاعفة مبلغ قاعدة تأخر الحضور */
    'late_penalty_minutes_per_block' => 30,

    'departments' => [
        'SAL' => 'المبيعات',
        'MKT' => 'التسويق',
        'HR' => 'الحضور والانضباط',
    ],

    'source_types' => [
        'crm_task' => 'مهمة مبيعات متأخرة',
        'crm_follow_up' => 'متابعة عميل متأخرة',
        'daily_sales_report' => 'تقرير مبيعات يومي غير مرفوع',
        'marketing_activity' => 'نشاط تسويق متأخر',
        'marketing_report' => 'تقرير تسويق دوري غير مرفوع',
        'attendance_late' => 'تأخر حضور',
        'attendance_no_start' => 'عدم بدء يوم العمل',
        'attendance_short_hours' => 'ساعات عمل ناقصة',
        'kpi_monthly' => 'KPI شهري دون الحد الأدنى',
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

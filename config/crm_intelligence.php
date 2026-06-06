<?php

return [
    'lost_reasons' => [
        'price' => 'السعر مرتفع',
        'competitor' => 'اختيار منافس',
        'financing' => 'مشكلة تمويل',
        'location' => 'الموقع غير مناسب',
        'unit_specs' => 'مواصفات الوحدة',
        'no_response' => 'العميل لا يرد',
        'timing' => 'التوقيت غير مناسب',
        'changed_mind' => 'تراجع عن الشراء',
        'other' => 'سبب آخر',
    ],

    'departments' => [
        'marketing' => 'التسويق',
        'sales' => 'المبيعات',
        'collection' => 'التحصيل',
        'customer_service' => 'خدمة العملاء',
        'maintenance' => 'الصيانة',
        'admin' => 'الإدارة',
    ],

    'timeline_event_types' => [
        'lead_created' => 'تسجيل عميل جديد',
        'stage_changed' => 'تغيير مرحلة العميل',
        'interaction' => 'تفاعل مع العميل',
        'deal_created' => 'إنشاء صفقة',
        'deal_stage_changed' => 'تغيير مرحلة صفقة',
        'deal_won' => 'إغلاق صفقة — بيع',
        'deal_lost' => 'إغلاق صفقة — خسارة',
        'assigned' => 'تعيين مسؤول',
        'post_sales' => 'ما بعد البيع',
    ],

    'post_sales_types' => [
        'complaint' => 'شكوى',
        'maintenance' => 'صيانة',
        'modification' => 'تعديل',
        'delay' => 'تأخير تسليم',
        'warranty' => 'ضمان',
        'handover' => 'تسليم',
    ],

    'post_sales_statuses' => [
        'open' => 'مفتوح',
        'in_progress' => 'قيد المعالجة',
        'resolved' => 'تم الحل',
        'closed' => 'مغلق',
    ],
];

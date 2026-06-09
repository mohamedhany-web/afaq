<?php

return [
    'transaction_types' => [
        'primary' => 'مبيعات المطورين (Primary)',
        'resale_full' => 'إعادة بيع — دورة كاملة',
        'resale_listing_only' => 'إعادة بيع — جلب وحدة فقط',
        'resale_selling_only' => 'إعادة بيع — جلب مشتري فقط',
        'resale_dual' => 'إعادة بيع — وكيل جالب + وكيل بائع',
        'rental' => 'إيجارات',
    ],

    'payout_statuses' => [
        'pending' => 'معلّق',
        'ready' => 'جاهز للصرف',
        'paid' => 'تم الصرف',
        'cancelled' => 'ملغي',
    ],

    'contract_statuses' => [
        'active' => 'نشط',
        'terminated' => 'منتهي',
        'expired' => 'منتهي الصلاحية',
    ],

    /** نسب الوكيل من عمولة الشركة المحصّلة */
    'scheme' => [
        'primary_normal' => ['agent' => 40, 'company' => 60],
        'primary_target' => ['agent' => 50, 'company' => 50],
        'resale_full' => ['agent' => 50, 'company' => 50],
        'resale_listing_only' => ['listing_agent' => 15, 'company' => 85],
        'resale_selling_only' => ['selling_agent' => 35, 'company' => 65],
        'resale_dual' => ['listing_agent' => 15, 'selling_agent' => 35, 'company' => 50],
        'rental' => ['agent' => 50, 'company' => 50],
    ],

    'payout_days_min' => 7,
    'payout_days_max' => 14,

    'scheme_table' => [
        [
            'type' => 'primary',
            'condition' => 'مبيعات عادية (بدون تحقيق تارجت)',
            'agent_rate' => '40%',
            'company_rate' => '60%',
            'payout' => 'فور تحصيل الشركة للدفعة من المطور العقاري',
        ],
        [
            'type' => 'primary_target',
            'condition' => 'مبيعات المطورين عند تحقيق التارجت الربع سنوي',
            'agent_rate' => '50%',
            'company_rate' => '50%',
            'payout' => 'تُصرف النسبة الإضافية بأثر رجعي أو على العمليات التالية',
        ],
        [
            'type' => 'resale_full',
            'condition' => 'الوكيل جلب الوحدة وسوّقها وباعها بنفسه',
            'agent_rate' => '50%',
            'company_rate' => '50%',
            'payout' => 'فور توقيع العقود وتحصيل كاش العمولات',
        ],
        [
            'type' => 'resale_listing_only',
            'condition' => 'الوكيل جلب وحدة حصرية ووكيل آخر باعها',
            'agent_rate' => '15%',
            'company_rate' => '85%',
            'payout' => 'عند إتمام البيع وتحصيل العمولة (يُخصم منها حق الوكيل البائع)',
        ],
        [
            'type' => 'resale_selling_only',
            'condition' => 'الوكيل أحضر مشترياً لوحدة موجودة مسبقاً',
            'agent_rate' => '35%',
            'company_rate' => '65%',
            'payout' => 'عند إتمام البيع (يُخصم منها حق الوكيل الجالب)',
        ],
        [
            'type' => 'rental',
            'condition' => 'إتمام عملية إيجار سكني أو تجاري',
            'agent_rate' => '50%',
            'company_rate' => '50%',
            'payout' => 'فور توقيع عقد الإيجار وتحصيل عمولة الشهر',
        ],
    ],
];

<?php

return [
    'labels' => [
        'personal' => 'شخصي',
        'referral' => 'ترشيح',
        'event' => 'إيفينت',
        'marketing' => 'ماركتينج',
        'paid_ad' => 'إعلان ممول',
        'broker' => 'بروكر',
    ],

    /** ألوان شارات المصدر في الواجهة */
    'colors' => [
        'personal' => ['bg' => '#f1f5f9', 'text' => '#475569'],
        'referral' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
        'event' => ['bg' => '#ede9fe', 'text' => '#6d28d9'],
        'marketing' => ['bg' => '#fef3c7', 'text' => '#b45309'],
        'paid_ad' => ['bg' => '#ffe4e6', 'text' => '#be123c'],
        'broker' => ['bg' => '#ccfbf1', 'text' => '#0f766e'],
    ],

    /** حقول إضافية تظهر حسب المصدر المختار */
    'detail_fields' => [
        'referral' => [
            'referrer_name' => 'اسم المُرشِّح',
        ],
        'event' => [
            'event_name' => 'اسم الإيفينت',
        ],
        'marketing' => [
            'campaign_name' => 'نوع أو اسم الحملة التسويقية',
        ],
        'broker' => [
            'broker_name' => 'اسم البروكر',
            'broker_id_number' => 'رقم البطاقة / الهوية',
        ],
    ],

    /** قيم قديمة → المصدر الموحّد */
    'legacy_map' => [
        'website' => 'personal',
        'walk_in' => 'personal',
        'manual' => 'personal',
        'call' => 'personal',
        'email' => 'personal',
        'whatsapp' => 'personal',
        'other' => 'personal',
        'import' => 'personal',
        'campaign' => 'marketing',
        'social' => 'marketing',
        'social_media' => 'marketing',
        'advertisement' => 'paid_ad',
        'facebook_ads' => 'paid_ad',
        'google_ads' => 'paid_ad',
        'referral' => 'referral',
        'event' => 'event',
        'marketing' => 'marketing',
        'paid_ad' => 'paid_ad',
        'personal' => 'personal',
        'broker' => 'broker',
    ],
];

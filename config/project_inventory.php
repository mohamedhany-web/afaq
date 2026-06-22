<?php

/**
 * مصدر مخزون المشروع — وحدات الشركة / وحدات الغير / مشاريع المطورين
 */
return [
    'sources' => [
        'company' => 'وحدات الشركة',
        'non_company' => 'وحدات الغير',
        'developer' => 'مشاريع المطورين',
    ],

    /** مصدر → نوع الملكية الافتراضي */
    'default_ownership' => [
        'company' => 'afaq_private',
        'non_company' => 'direct_owner',
        'developer' => 'developer',
    ],

    /** أنواع ملكية «وحدات الغير» */
    'non_company_ownership' => [
        'direct_owner' => 'مالك مباشر',
        'trader' => 'تاجر',
        'broker' => 'وسيط',
        'investor' => 'مستثمر',
        'partnership' => 'مشاركات',
        'property_management' => 'إدارة ممتلكات',
    ],

    /** جداول تصنيف مشاريع المطورين */
    'developer_tables' => [
        'commercial' => 'تجاري',
        'residential' => 'سكني',
        'medical' => 'طبي',
    ],

    'directions' => [
        'north' => 'شمال',
        'south' => 'جنوب',
        'east' => 'شرق',
        'west' => 'غرب',
        'north_east' => 'شمال شرق',
        'north_west' => 'شمال غرب',
        'south_east' => 'جنوب شرق',
        'south_west' => 'جنوب غرب',
        'garden' => 'حديقة',
        'street' => 'شارع',
    ],
];

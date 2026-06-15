<?php

return [
    'labels' => [
        'individual' => 'فرد',
        'company' => 'شركة',
        'freelance' => 'فري لانس',
        'investor' => 'مستثمر',
        'partner' => 'شريك',
    ],

    /** قيم قديمة في قاعدة البيانات → التصنيف الجديد */
    'legacy_map' => [
        'small_business' => 'company',
        'enterprise' => 'company',
    ],
];

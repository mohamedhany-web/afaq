<?php

return [
    'period_types' => [
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
    ],

    'mandatory_by_role' => [
        'marketing_manager' => ['daily', 'weekly', 'monthly'],
        'marketing_rep' => ['daily'],
    ],

    'statuses' => [
        'draft' => 'مسودة',
        'submitted' => 'مرفوع',
    ],
];

<?php

return [
    /*
    | ترقيم الطوابق والوحدات — معيار شركة آفاق
    | كل وحدة: {prefix}.{تسلسل}  مثل B.1 · GF.2 · FF.3
    */
    'floor_levels' => [
        -1 => [
            'prefix' => 'B',
            'label' => 'البدروم',
            'label_en' => 'Basement',
        ],
        0 => [
            'prefix' => 'GF',
            'label' => 'الدور الأرضي',
            'label_en' => 'Ground Floor',
        ],
        1 => [
            'prefix' => 'FF',
            'label' => 'الدور الأول',
            'label_en' => 'First Floor',
        ],
        2 => [
            'prefix' => 'SF',
            'label' => 'الدور الثاني',
            'label_en' => 'Second Floor',
        ],
        3 => [
            'prefix' => 'TF',
            'label' => 'الدور الثالث',
            'label_en' => 'Third Floor',
        ],
        4 => [
            'prefix' => '4F',
            'label' => 'الدور الرابع',
            'label_en' => 'Fourth Floor',
        ],
    ],

    'use_types' => [
        'residential' => 'سكني',
        'commercial' => 'تجاري',
        'administrative' => 'إداري',
    ],

    'statuses' => [
        'available' => 'متاح',
        'reserved' => 'محجوز',
        'sold' => 'مباع',
    ],

    'plan_types' => [
        'cash' => 'كاش',
        'installment' => 'قسط',
    ],

    'status_colors' => [
        'available' => '#22c55e',
        'reserved' => '#f59e0b',
        'sold' => '#ef4444',
    ],

    'use_colors' => [
        'residential' => '#3b82f6',
        'commercial' => '#f97316',
        'administrative' => '#8b5cf6',
    ],
];

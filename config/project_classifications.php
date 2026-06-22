<?php

/**
 * تصنيفات المشروع العقاري — سكني / إداري / تجاري / طبي / مختلط
 * تُطابق use_type للوحدات (ما عدا mixed).
 */
return [
    'types' => [
        'residential' => 'سكني',
        'administrative' => 'إداري',
        'commercial' => 'تجاري',
        'medical' => 'طبي',
        'mixed' => 'مختلط',
    ],

    /** تصنيفات لها وحدات فعلية */
    'concrete' => [
        'residential' => 'سكني',
        'administrative' => 'إداري',
        'commercial' => 'تجاري',
        'medical' => 'طبي',
    ],

    'colors' => [
        'residential' => '#3b82f6',
        'administrative' => '#8b5cf6',
        'commercial' => '#f97316',
        'medical' => '#14b8a6',
        'mixed' => '#6366f1',
    ],
];

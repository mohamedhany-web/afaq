<?php

return [
    'default_work_start' => '09:00',
    'default_work_end' => '17:00',
    'default_late_grace_minutes' => 15,

    /** أيام الإجازة الأسبوعية الافتراضية: 5=الجمعة، 6=السبت (Carbon dayOfWeek) */
    'default_weekly_off_days' => [5, 6],

    /** 0=الأحد … 6=السبت */
    'weekdays' => [
        0 => 'الأحد',
        1 => 'الإثنين',
        2 => 'الثلاثاء',
        3 => 'الأربعاء',
        4 => 'الخميس',
        5 => 'الجمعة',
        6 => 'السبت',
    ],
];

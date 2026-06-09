<?php

return [
    /** ساعة بدء العمل — بعدها يُسجّل تأخر */
    'work_start_hour' => 9,
    'work_start_minute' => 0,

    /** ساعة آخر موعد لبدء يوم العمل قبل اعتباره غياباً */
    'work_day_start_deadline_hour' => 11,

    /** حدود التقييم الإجمالي */
    'score_weights' => [
        'reports' => 35,
        'attendance' => 30,
        'tasks' => 20,
        'follow_ups' => 15,
    ],

    'status_labels' => [
        'excellent' => ['min' => 90, 'label' => 'ملتزم', 'color' => 'green'],
        'good' => ['min' => 75, 'label' => 'جيد', 'color' => 'blue'],
        'warning' => ['min' => 60, 'label' => 'يحتاج متابعة', 'color' => 'amber'],
        'critical' => ['min' => 0, 'label' => 'غير ملتزم', 'color' => 'red'],
    ],
];

<?php

return [
    /** أدوار معفاة من زر بدء اليوم (مدير النظام وما شابه) */
    'exempt_roles' => ['super_admin', 'admin'],

    'default_daily_hours' => 8,

    /** دقائق قبل نهاية المدة لتحذير الموظف */
    'warning_before_minutes' => 15,
];

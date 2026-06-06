<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('client-portal:invoice-due-reminders')->dailyAt('08:00');
Schedule::command('crm:follow-up-reminders')->everyFiveMinutes();
Schedule::command('crm:task-maintenance')->everyFiveMinutes();
Schedule::command('marketing:process-recurrence')->hourly();
Schedule::command('penalties:apply-overdue')->hourly();
Schedule::command('attendance:auto-checkout')->everyMinute();
Schedule::command('notifications:prune-read')->dailyAt('03:00');

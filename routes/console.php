<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-generate invoices on the 1st of every month at 6am
Schedule::command('invoices:generate-monthly')->monthlyOn(1, '06:00');

// Mark overdue invoices every day at midnight
Schedule::command('invoices:mark-overdue')->dailyAt('00:01');
Schedule::command('makazihub:generate-monthly-invoices')->monthlyOn(1, '07:00');
Schedule::command('makazihub:mark-overdue-invoices')->dailyAt('00:05');
Schedule::command('makazihub:send-overdue-reminders')->dailyAt('09:00');
Schedule::command('makazihub:lease-expiry-warnings')->dailyAt('08:00');

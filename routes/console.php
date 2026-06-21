<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto check-in/check-out bookings every minute based on actual time
Schedule::command('bookings:auto-checkout')->everyMinute();

// Auto-complete past convention events daily at 1 AM
Schedule::command('conventions:update-statuses')->dailyAt('01:00');

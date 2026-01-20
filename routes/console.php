<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-checkout overdue bookings daily at midnight
Schedule::command('bookings:auto-checkout')->daily();

// Auto-complete past convention events daily at 1 AM
Schedule::command('conventions:update-statuses')->dailyAt('01:00');

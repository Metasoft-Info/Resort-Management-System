<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class RunScheduledTasks
{
    public function handle(Request $request, Closure $next)
    {
        // Run auto check-in/check-out at most once per minute
        if (Cache::lock('scheduled-tasks-lock', 60)->get()) {
            try {
                Artisan::call('bookings:auto-checkout');
            } catch (\Throwable $e) {
                // Silently fail — don't break the page load
            }
        }

        return $next($request);
    }
}

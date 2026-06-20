<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking};
use Carbon\Carbon;

class TodaysSummaryController extends Controller {
    public function index() {
        $today = Carbon::today();
        $user = auth()->user();
        
        // Determine user's access and current mode
        $hasResortAccess = $user->hasResortAccess();
        $hasConventionAccess = $user->hasConventionAccess();
        $currentMode = $user->getDashboardMode();
        
        // Today's Check-ins: check_in_date is today AND not yet checked_out
        $todayCheckins = Booking::with('room')
            ->whereDate('check_in_date', $today)
            ->where('status', '!=', 'checked_out')
            ->get();
        
        // Today's Check-outs: check_out_date is today OR status is checked_out (updated today)
        $todayCheckouts = Booking::with('room')
            ->where(function($query) use ($today) {
                $query->whereDate('check_out_date', $today)
                    ->orWhere(function($q) use ($today) {
                        $q->where('status', 'checked_out')
                          ->whereDate('updated_at', $today);
                    });
            })
            ->get();
        
        $currentlyStaying = Booking::with('room')
            ->where('check_in_date', '<=', $today)->where('check_out_date', '>=', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])->get();
        $todayConventions = ConventionBooking::with('conventionHall')->whereDate('event_date', $today)->get();
        
        // Resort stats
        $resortStats = [
            'checkins_count' => $todayCheckins->count(),
            'checkouts_count' => $todayCheckouts->count(),
            'staying_count' => $currentlyStaying->count(),
            'today_revenue' => $todayCheckins->sum(fn($b) => $b->getGrandTotal()),
            'pending_payments' => $currentlyStaying->sum(fn($b) => $b->getCalculatedRemaining()),
        ];
        
        // Convention stats
        $conventionStats = [
            'events_count' => $todayConventions->count(),
            'confirmed_count' => $todayConventions->where('status', 'confirmed')->count(),
            'pending_count' => $todayConventions->where('status', 'pending')->count(),
            'today_revenue' => $todayConventions->sum('total_amount'),
        ];
        
        // Combined stats for backward compatibility
        $stats = [
            'checkins_count' => $todayCheckins->count(),
            'checkouts_count' => $todayCheckouts->count(),
            'staying_count' => $currentlyStaying->count(),
            'conventions_count' => $todayConventions->count(),
        ];
        
        return view('admin.todays-summary', compact(
            'stats', 'resortStats', 'conventionStats',
            'todayCheckins', 'todayCheckouts', 'currentlyStaying', 'todayConventions',
            'hasResortAccess', 'hasConventionAccess', 'currentMode'
        ));
    }
}
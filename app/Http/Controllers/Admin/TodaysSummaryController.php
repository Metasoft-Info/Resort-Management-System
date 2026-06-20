<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking};
use Carbon\Carbon;

class TodaysSummaryController extends Controller {
    public function index() {
        $now = Carbon::now('Asia/Dhaka');
        $today = $now->copy()->startOfDay();
        $user = auth()->user();
        
        // Determine user's access and current mode
        $hasResortAccess = $user->hasResortAccess();
        $hasConventionAccess = $user->hasConventionAccess();
        $currentMode = $user->getDashboardMode();
        
        // Today's Check-ins: check_in_date is today AND not yet checked_out
        $todayCheckins = Booking::with('room')
            ->whereDate('check_in_date', $today)
            ->whereNotIn('status', ['checked_out', 'cancelled'])
            ->get();
        
        // Today's Check-outs: actually checked_out today
        $todayCheckouts = Booking::with('room')
            ->where('status', 'checked_out')
            ->whereDate('check_out_date', $today)
            ->get();
        
        // Currently staying: actually checked_in
        $currentlyStaying = Booking::with('room')
            ->where('status', 'checked_in')
            ->get();
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
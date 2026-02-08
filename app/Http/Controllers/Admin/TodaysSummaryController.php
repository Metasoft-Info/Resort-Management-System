<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking};
use Carbon\Carbon;

class TodaysSummaryController extends Controller {
    public function index() {
        $today = Carbon::today();
        
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
        $stats = [
            'checkins_count' => $todayCheckins->count(),
            'checkouts_count' => $todayCheckouts->count(),
            'staying_count' => $currentlyStaying->count(),
            'conventions_count' => $todayConventions->count(),
        ];
        return view('admin.todays-summary', compact('stats', 'todayCheckins', 'todayCheckouts', 'currentlyStaying', 'todayConventions'));
    }
}
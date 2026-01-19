<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking};
use Carbon\Carbon;

class TodaysSummaryController extends Controller {
    public function index() {
        $today = Carbon::today();
        $todayCheckins = Booking::with('room')->whereDate('check_in_date', $today)->get();
        $todayCheckouts = Booking::with('room')->whereDate('check_out_date', $today)->get();
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
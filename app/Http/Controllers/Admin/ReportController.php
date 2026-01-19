<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking};
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function roomBookings(Request $request) {
        $query = Booking::with('room');
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        $bookings = $query->paginate(20);
        $totalRevenue = $query->sum('total_amount');
        return view('admin.reports.room-bookings', compact('bookings', 'totalRevenue'));
    }
    public function conventionBookings(Request $request) {
        $query = ConventionBooking::with('conventionHall');
        if($request->start_date) $query->whereDate('event_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('event_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        $bookings = $query->paginate(20);
        $totalRevenue = $query->sum('total_amount');
        return view('admin.reports.convention-bookings', compact('bookings', 'totalRevenue'));
    }
}
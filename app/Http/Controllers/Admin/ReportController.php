<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Booking, ConventionBooking, RoomType, Room, ConventionHall};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller {
    public function roomBookings(Request $request) {
        $query = Booking::with(['room.roomType']);
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->room_type_id) $query->whereHas('room', fn($q) => $q->where('room_type_id', $request->room_type_id));
        if($request->room_id) $query->where('room_id', $request->room_id);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('customer_nid', 'like', "%{$request->search}%");
            });
        }
        
        // Calculate totals before pagination for accurate figures
        $totalBookings = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalAdvance = (clone $query)->sum('advance_payment');
        $totalRemaining = (clone $query)->sum('remaining_payment');
        
        $bookings = $query->orderBy('check_in_date', 'desc')->paginate(20)->withQueryString();
        $roomTypes = RoomType::all();
        $rooms = Room::orderBy('room_number')->get();
        
        return view('admin.reports.room-bookings', compact('bookings', 'totalRevenue', 'totalBookings', 'totalAdvance', 'totalRemaining', 'roomTypes', 'rooms'));
    }
    
    public function exportRoomBookings(Request $request) {
        $query = Booking::with(['room.roomType']);
        if($request->start_date) $query->whereDate('check_in_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('check_in_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->room_type_id) $query->whereHas('room', fn($q) => $q->where('room_type_id', $request->room_type_id));
        if($request->room_id) $query->where('room_id', $request->room_id);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('customer_nid', 'like', "%{$request->search}%");
            });
        }
        
        $bookings = $query->orderBy('check_in_date', 'desc')->get();
        
        $csvContent = "ID,Customer Name,Phone,NID,Room,Room Type,Check-In,Check-Out,Total Amount,Advance,Remaining,Payment Status,Status\n";
        foreach ($bookings as $b) {
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"{$b->customer_nid}\",";
            $csvContent .= "\"" . ($b->room->room_number ?? 'N/A') . "\",";
            $csvContent .= "\"" . ($b->room->roomType->name ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->check_in_date}\",";
            $csvContent .= "\"{$b->check_out_date}\",";
            $csvContent .= "\"{$b->total_amount}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->remaining_payment}\",";
            $csvContent .= "\"{$b->payment_status}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $filename = 'room-bookings-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
    
    public function conventionBookings(Request $request) {
        $query = ConventionBooking::with('conventionHall');
        if($request->start_date) $query->whereDate('event_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('event_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->hall_id) $query->where('convention_hall_id', $request->hall_id);
        if($request->time_slot) $query->where('time_slot', $request->time_slot);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        if($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%")
                  ->orWhere('organization_name', 'like', "%{$request->search}%");
            });
        }
        
        $totalBookings = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalAdvance = (clone $query)->sum('advance_payment');
        $totalRemaining = (clone $query)->sum('remaining_payment');
        
        $bookings = $query->orderBy('event_date', 'desc')->paginate(20)->withQueryString();
        $halls = ConventionHall::all();
        
        return view('admin.reports.convention-bookings', compact('bookings', 'totalRevenue', 'totalBookings', 'totalAdvance', 'totalRemaining', 'halls'));
    }
    
    public function exportConventionBookings(Request $request) {
        $query = ConventionBooking::with('conventionHall');
        if($request->start_date) $query->whereDate('event_date', '>=', $request->start_date);
        if($request->end_date) $query->whereDate('event_date', '<=', $request->end_date);
        if($request->status) $query->where('status', $request->status);
        if($request->hall_id) $query->where('convention_hall_id', $request->hall_id);
        if($request->time_slot) $query->where('time_slot', $request->time_slot);
        if($request->payment_status) $query->where('payment_status', $request->payment_status);
        
        $bookings = $query->orderBy('event_date', 'desc')->get();
        
        $csvContent = "ID,Customer Name,Organization,Phone,Hall,Event Date,Time Slot,Total Amount,Advance,Remaining,Payment Status,Status\n";
        foreach ($bookings as $b) {
            $csvContent .= "\"{$b->id}\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->customer_name) . "\",";
            $csvContent .= "\"" . str_replace('"', '""', $b->organization_name ?? '') . "\",";
            $csvContent .= "\"{$b->customer_phone}\",";
            $csvContent .= "\"" . ($b->conventionHall->name ?? 'N/A') . "\",";
            $csvContent .= "\"{$b->event_date}\",";
            $csvContent .= "\"{$b->time_slot}\",";
            $csvContent .= "\"{$b->total_amount}\",";
            $csvContent .= "\"{$b->advance_payment}\",";
            $csvContent .= "\"{$b->remaining_payment}\",";
            $csvContent .= "\"{$b->payment_status}\",";
            $csvContent .= "\"{$b->status}\"\n";
        }
        
        $filename = 'convention-bookings-report-' . date('Y-m-d') . '.csv';
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
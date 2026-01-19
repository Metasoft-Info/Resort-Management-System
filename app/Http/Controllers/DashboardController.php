<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ConventionBooking;
use App\Models\ConventionHall;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::whereIn('status', ['confirmed', 'checked_in'])->count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'convention_bookings' => ConventionBooking::count(),
            'today_checkins' => Booking::whereDate('check_in_date', today())->count(),
            'total_revenue' => Booking::where('status', '!=', 'cancelled')->sum('total_amount'),
        ];

        $recentBookings = Booking::with('room')->latest()->take(10)->get();
        $allRooms = Room::all();
        $allHalls = ConventionHall::all();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'allRooms', 'allHalls'));
    }

    public function searchRoomAvailability(Request $request)
    {
        $checkIn = $request->checkIn;
        $checkOut = $request->checkOut;

        // Get booked room IDs for the date range
        $bookedRoomIds = Booking::where(function($query) use ($checkIn, $checkOut) {
            $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function($q) use ($checkIn, $checkOut) {
                      $q->where('check_in_date', '<=', $checkIn)
                        ->where('check_out_date', '>=', $checkOut);
                  });
        })
        ->whereNotIn('status', ['cancelled'])
        ->pluck('room_id')->toArray();

        $availableRooms = Room::whereNotIn('id', $bookedRoomIds)
            ->where('status', 'available')
            ->get();

        $roomBookings = Booking::with('room')
            ->whereIn('room_id', $bookedRoomIds)
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->whereNotIn('status', ['cancelled'])
            ->get();

        return response()->json([
            'availableRooms' => $availableRooms,
            'roomBookings' => $roomBookings
        ]);
    }

    public function searchHallAvailability(Request $request)
    {
        $date = $request->date;
        $slot = $request->slot;

        $bookedHallIds = ConventionBooking::whereDate('event_date', $date)
            ->where('time_slot', $slot)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('convention_hall_id')->toArray();

        $hallBookings = ConventionBooking::with('conventionHall')
            ->whereDate('event_date', $date)
            ->where('time_slot', $slot)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        return response()->json([
            'bookedHallIds' => $bookedHallIds,
            'hallBookings' => $hallBookings
        ]);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRoom;
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
        $user = auth()->user();
        $today = Carbon::today();
        
        // Determine user's access and current mode
        $hasResortAccess = $user->hasResortAccess();
        $hasConventionAccess = $user->hasConventionAccess();
        $currentMode = $user->getDashboardMode();
        
        // Get rooms that are currently occupied (have active bookings for today)
        // Check legacy room_id column
        $occupiedRoomIdsLegacy = Booking::whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>', $today)
            ->whereNotNull('room_id')
            ->pluck('room_id')
            ->toArray();
        
        // Check booking_rooms table for multi-room bookings
        $occupiedRoomIdsMulti = BookingRoom::whereHas('booking', function($q) use ($today) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                  ->where('check_in_date', '<=', $today)
                  ->where('check_out_date', '>', $today);
            })
            ->pluck('room_id')
            ->toArray();
        
        $occupiedRoomIds = array_unique(array_merge($occupiedRoomIdsLegacy, $occupiedRoomIdsMulti));
        
        $totalRooms = Room::count();
        $availableRoomsCount = $totalRooms - count($occupiedRoomIds);
        
        // Calculate total revenue including convention bookings
        $roomRevenue = Booking::where('status', '!=', 'cancelled')->sum('total_amount');
        $conventionRevenue = ConventionBooking::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenue = $roomRevenue + $conventionRevenue;
        
        // Resort Stats
        $resortStats = [
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::whereIn('status', ['confirmed', 'checked_in'])->count(),
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRoomsCount,
            'today_checkins' => Booking::whereDate('check_in_date', $today)->count(),
            'today_checkouts' => Booking::whereDate('check_out_date', $today)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'room_revenue' => $roomRevenue,
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];
        
        // Convention Stats
        $conventionStats = [
            'total_bookings' => ConventionBooking::count(),
            'active_bookings' => ConventionBooking::whereIn('status', ['confirmed', 'pending'])->count(),
            'total_halls' => ConventionHall::count(),
            'today_events' => ConventionBooking::whereDate('event_date', $today)->whereIn('status', ['confirmed'])->count(),
            'upcoming_events' => ConventionBooking::where('event_date', '>', $today)->whereIn('status', ['confirmed', 'pending'])->count(),
            'convention_revenue' => $conventionRevenue,
            'pending_bookings' => ConventionBooking::where('status', 'pending')->count(),
        ];
        
        // Combined stats for overview
        $stats = [
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::whereIn('status', ['confirmed', 'checked_in'])->count(),
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRoomsCount,
            'convention_bookings' => ConventionBooking::count(),
            'today_checkins' => Booking::whereDate('check_in_date', $today)->count(),
            'today_checkouts' => Booking::whereDate('check_out_date', $today)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'total_revenue' => $totalRevenue,
            'room_revenue' => $roomRevenue,
            'convention_revenue' => $conventionRevenue,
        ];

        $recentBookings = Booking::with('room')->latest()->take(10)->get();
        $recentConventionBookings = ConventionBooking::with('conventionHall')->latest()->take(10)->get();
        $allRooms = Room::with('roomType')->get();
        $allHalls = ConventionHall::all();
        
        // Get room status with current and upcoming bookings
        $roomsWithStatus = [];
        foreach ($allRooms as $room) {
            // Check legacy room_id
            $currentBooking = Booking::where('room_id', $room->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<=', $today)
                ->where('check_out_date', '>', $today)
                ->first();
            
            // If not found in legacy, check booking_rooms table
            if (!$currentBooking) {
                $bookingRoom = BookingRoom::where('room_id', $room->id)
                    ->whereHas('booking', function($q) use ($today) {
                        $q->whereIn('status', ['confirmed', 'checked_in'])
                          ->where('check_in_date', '<=', $today)
                          ->where('check_out_date', '>', $today);
                    })
                    ->with('booking')
                    ->first();
                if ($bookingRoom) {
                    $currentBooking = $bookingRoom->booking;
                }
            }
            
            // Check upcoming bookings - legacy
            $upcomingBooking = Booking::where('room_id', $room->id)
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('check_in_date', '>', $today)
                ->orderBy('check_in_date')
                ->first();
            
            // If not found, check booking_rooms
            if (!$upcomingBooking) {
                $upcomingBookingRoom = BookingRoom::where('room_id', $room->id)
                    ->whereHas('booking', function($q) use ($today) {
                        $q->whereIn('status', ['confirmed', 'pending'])
                          ->where('check_in_date', '>', $today);
                    })
                    ->with('booking')
                    ->first();
                if ($upcomingBookingRoom) {
                    $upcomingBooking = $upcomingBookingRoom->booking;
                }
            }
            
            $roomsWithStatus[] = [
                'room' => $room,
                'status' => $currentBooking ? 'occupied' : 'available',
                'current_booking' => $currentBooking,
                'upcoming_booking' => $upcomingBooking,
                'available_from' => $currentBooking ? $currentBooking->check_out_date : $today,
            ];
        }

        // Chart data for last 7 days
        $chartLabels = [];
        $chartBookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('D');
            $chartBookings[] = Booking::whereDate('created_at', $date)->count();
        }

        // Revenue data for last 4 weeks
        $revenueLabels = [];
        $revenueData = [];
        $conventionRevenueData = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = Carbon::today()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::today()->subWeeks($i)->endOfWeek();
            $revenueLabels[] = 'Week ' . (4 - $i);
            $weekRoomRevenue = Booking::whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $weekConventionRevenue = ConventionBooking::whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            $revenueData[] = $weekRoomRevenue;
            $conventionRevenueData[] = $weekConventionRevenue;
        }
        
        // Convention booking chart data
        $conventionChartBookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $conventionChartBookings[] = ConventionBooking::whereDate('created_at', $date)->count();
        }

        $chartData = [
            'labels' => $chartLabels,
            'bookings' => $chartBookings,
            'conventionBookings' => $conventionChartBookings,
            'revenueLabels' => $revenueLabels,
            'revenue' => $revenueData,
            'conventionRevenue' => $conventionRevenueData,
        ];

        // Today's checkouts - already calculated above

        return view('admin.dashboard', compact(
            'stats', 
            'resortStats',
            'conventionStats',
            'recentBookings', 
            'recentConventionBookings',
            'allRooms', 
            'allHalls', 
            'roomsWithStatus', 
            'chartData',
            'hasResortAccess',
            'hasConventionAccess',
            'currentMode'
        ));
    }
    
    /**
     * Toggle dashboard mode (Resort / Convention)
     */
    public function toggleMode(Request $request)
    {
        $user = auth()->user();
        $newMode = $request->input('mode');
        
        if (in_array($newMode, ['resort', 'convention'])) {
            // Verify user has access to this mode
            if ($newMode === 'resort' && !$user->hasResortAccess()) {
                return response()->json(['success' => false, 'message' => 'No resort access']);
            }
            if ($newMode === 'convention' && !$user->hasConventionAccess()) {
                return response()->json(['success' => false, 'message' => 'No convention access']);
            }
            
            $user->dashboard_mode = $newMode;
            $user->save();
            
            return response()->json(['success' => true, 'mode' => $newMode]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid mode']);
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


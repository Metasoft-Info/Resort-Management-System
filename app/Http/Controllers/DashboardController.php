<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ConventionBooking;
use App\Models\ConventionHall;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now('Asia/Dhaka');
        $today = $now->copy()->startOfDay();
        
        // Determine user's access and current mode
        $hasResortAccess = $user->hasResortAccess();
        $hasConventionAccess = $user->hasConventionAccess();
        $currentMode = $user->getDashboardMode();
        
        // Get rooms that are currently occupied (status checked_in or confirmed with time passed)
        $activeBookings = Booking::with(['bookingRooms', 'room'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->get();
        
        $occupiedRoomIds = [];
        $activeBookingIds = [];
        foreach ($activeBookings as $booking) {
            if ($booking->isOccupyingAt($now)) {
                $activeBookingIds[] = $booking->id;
                if ($booking->room_id) {
                    $occupiedRoomIds[] = $booking->room_id;
                }
                foreach ($booking->bookingRooms as $br) {
                    $occupiedRoomIds[] = $br->room_id;
                }
            }
        }
        $occupiedRoomIds = array_values(array_unique($occupiedRoomIds));
        
        $totalRooms = Room::count();
        $availableRoomsCount = $totalRooms - count($occupiedRoomIds);
        
        // Calculate total revenue using model methods for accuracy (preserves agreed room prices)
        $roomRevenue = Booking::where('status', '!=', 'cancelled')->get()->sum(fn($b) => $b->getGrandTotal());
        $conventionRevenue = ConventionBooking::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenue = $roomRevenue + $conventionRevenue;
        
        // Resort Stats
        $resortStats = [
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::where('status', 'checked_in')->count(),
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRoomsCount,
            'today_checkins' => Booking::whereDate('check_in_date', $today)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'today_checkouts' => Booking::whereDate('check_out_date', $today)->where('status', 'checked_in')->count(),
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
            'active_bookings' => Booking::where('status', 'checked_in')->count(),
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRoomsCount,
            'convention_bookings' => ConventionBooking::count(),
            'today_checkins' => Booking::whereDate('check_in_date', $today)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'today_checkouts' => Booking::whereDate('check_out_date', $today)->where('status', 'checked_in')->count(),
            'total_revenue' => $totalRevenue,
            'room_revenue' => $roomRevenue,
            'convention_revenue' => $conventionRevenue,
        ];

        $recentBookings = Booking::with(['room', 'bookingRooms.room'])->latest()->take(10)->get();
        $recentConventionBookings = ConventionBooking::with('conventionHall')->latest()->take(10)->get();
        $allRooms = Room::with('roomType')->get();
        $allHalls = ConventionHall::all();
        
        // Get room status with current and upcoming bookings
        $roomsWithStatus = [];
        foreach ($allRooms as $room) {
            // Determine if room is currently occupied using merged occupied room IDs
            $isOccupied = in_array($room->id, $occupiedRoomIds);

            // Find current booking that is currently occupying this room
            $currentBooking = Booking::where('room_id', $room->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->get()
                ->first(fn($b) => $b->isOccupyingAt($now));

            if (!$currentBooking) {
                $currentBooking = Booking::whereHas('bookingRooms', function($q) use ($room) {
                        $q->where('room_id', $room->id);
                    })
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->get()
                    ->first(fn($b) => $b->isOccupyingAt($now));
            }

            // Check upcoming bookings - legacy and pivot
            $upcomingBooking = Booking::where('room_id', $room->id)
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('check_in_date', '>', $today)
                ->orderBy('check_in_date')
                ->first();

            if (!$upcomingBooking) {
                $upcomingBooking = Booking::whereHas('bookingRooms', function($q) use ($room) {
                        $q->where('room_id', $room->id);
                    })
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->where('check_in_date', '>', $today)
                    ->orderBy('check_in_date')
                    ->first();
            }

            $availableFrom = $currentBooking ? $currentBooking->getCheckOutDateTime() : $now;

            $roomsWithStatus[] = [
                'room' => $room,
                'status' => $isOccupied ? 'occupied' : 'available',
                'current_booking' => $currentBooking,
                'upcoming_booking' => $upcomingBooking,
                'available_from' => $availableFrom,
            ];
        }

        // Hall availability status for today and next 6 days
        $hallsWithStatus = [];
        foreach ($allHalls as $hall) {
            $hallDays = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::today()->addDays($i);
                $dateStr = $date->format('Y-m-d');
                
                // Check full_day booking first
                $fullDayBooked = ConventionBooking::where('hall_id', $hall->id)
                    ->whereDate('event_date', $dateStr)
                    ->where('time_slot', 'full_day')
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->exists();
                
                // Check morning slot booking
                $morningBooked = ConventionBooking::where('hall_id', $hall->id)
                    ->whereDate('event_date', $dateStr)
                    ->whereIn('time_slot', ['morning', 'full_day'])
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->exists();
                
                // Check night slot booking  
                $nightBooked = ConventionBooking::where('hall_id', $hall->id)
                    ->whereDate('event_date', $dateStr)
                    ->whereIn('time_slot', ['night', 'full_day'])
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->exists();
                
                // full_day status: available, booked, or unavailable (when morning/night booked separately)
                $fullDayStatus = 'available';
                if ($fullDayBooked) {
                    $fullDayStatus = 'booked'; // Actually booked as full_day
                } elseif ($morningBooked || $nightBooked) {
                    $fullDayStatus = 'unavailable'; // Can't book full_day because morning or night is taken
                }
                
                $hallDays[] = [
                    'date' => $date,
                    'date_str' => $dateStr,
                    'day_name' => $date->format('D'),
                    'day_num' => $date->format('d'),
                    'morning' => $morningBooked ? 'booked' : 'available',
                    'night' => $nightBooked ? 'booked' : 'available',
                    'full_day' => $fullDayStatus,
                ];
            }
            $hallsWithStatus[] = [
                'hall' => $hall,
                'days' => $hallDays,
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
                ->get()
                ->sum(fn($b) => $b->getGrandTotal());
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
            'hallsWithStatus',
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
        $checkIn = \Carbon\Carbon::parse($request->checkIn)->setTimeFromTimeString('12:00');
        $checkOut = \Carbon\Carbon::parse($request->checkOut)->setTimeFromTimeString('12:00');

        // Get active bookings and check actual datetime overlap using model helpers
        $activeBookings = Booking::with(['bookingRooms', 'room'])
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<', $checkOut->toDateString())
            ->where('check_out_date', '>', $checkIn->toDateString())
            ->get();

        $bookedRoomIds = [];
        foreach ($activeBookings as $booking) {
            $existingCheckIn = $booking->getCheckInDateTime();
            $existingCheckOut = $booking->getCheckOutDateTime();
            if ($existingCheckIn && $existingCheckOut && $existingCheckIn->lt($checkOut) && $existingCheckOut->gt($checkIn)) {
                if ($booking->room_id) {
                    $bookedRoomIds[] = $booking->room_id;
                }
                foreach ($booking->bookingRooms as $br) {
                    $bookedRoomIds[] = $br->room_id;
                }
            }
        }
        $bookedRoomIds = array_values(array_unique($bookedRoomIds));

        $availableRooms = Room::whereNotIn('id', $bookedRoomIds)->get();

        return response()->json([
            'availableRooms' => $availableRooms,
            'roomBookings' => []
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


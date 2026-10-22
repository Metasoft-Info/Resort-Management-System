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
        $user = auth()->user();
        $now = Carbon::now('Asia/Dhaka');
        $today = $now->copy()->startOfDay();
        
        // Determine user's access and current mode
        $hasResortAccess = $user->hasResortAccess();
        $hasConventionAccess = $user->hasConventionAccess();
        $currentMode = $user->getDashboardMode();
        
        $todayDate = $today->toDateString();

        // Only load stays that can overlap today; use the same time-aware
        // occupancy rule for the room cards and the dashboard counters.
        $activeBookings = Booking::with(['bookingRooms', 'room'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<=', $todayDate)
            ->where('check_out_date', '>=', $todayDate)
            ->get();
        
        $currentBookingsByRoom = [];
        $activeBookingIds = [];
        foreach ($activeBookings as $booking) {
            if ($booking->isOccupyingAt($now)) {
                $activeBookingIds[] = $booking->id;
                foreach ($booking->getAllRoomIds() as $roomId) {
                    $currentBookingsByRoom[$roomId] ??= $booking;
                }
            }
        }
        $activeBookingIds = array_values(array_unique($activeBookingIds));
        $occupiedRoomIds = array_keys($currentBookingsByRoom);

        // Fetch upcoming stays once instead of re-reading them for every room.
        $upcomingBookingsByRoom = [];
        $upcomingBookings = Booking::with(['bookingRooms', 'room'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_in_date', '>', $todayDate)
            ->orderBy('check_in_date')
            ->get();
        foreach ($upcomingBookings as $booking) {
            foreach ($booking->getAllRoomIds() as $roomId) {
                $upcomingBookingsByRoom[$roomId] ??= $booking;
            }
        }
        
        $totalRooms = Room::count();
        $availableRoomsCount = $totalRooms - count($occupiedRoomIds);
        
        // Keep the canonical booking calculation, but eager-load its timeline
        // and process in bounded batches so dashboard memory/query count stays stable.
        $roomRevenue = 0.0;
        Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts.fromRoom.roomType'])
            ->where('status', '!=', 'cancelled')
            ->chunkById(250, function ($bookings) use (&$roomRevenue) {
                foreach ($bookings as $booking) {
                    $roomRevenue += $booking->getGrandTotal();
                }
            });
        $conventionRevenue = ConventionBooking::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalRevenue = $roomRevenue + $conventionRevenue;

        $totalBookings = Booking::count();
        $totalConventionBookings = ConventionBooking::count();
        $todayCheckins = Booking::whereDate('check_in_date', $today)->whereIn('status', ['confirmed', 'checked_in'])->count();
        $todayCheckouts = Booking::whereDate('check_out_date', $today)->where('status', 'checked_out')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $pendingConventionBookings = ConventionBooking::where('status', 'pending')->count();
        
        // Resort Stats
        $resortStats = [
            'total_bookings' => $totalBookings,
            'active_bookings' => count($activeBookingIds),
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRoomsCount,
            'today_checkins' => $todayCheckins,
            'today_checkouts' => $todayCheckouts,
            'room_revenue' => $roomRevenue,
            'pending_bookings' => $pendingBookings,
        ];
        
        // Convention Stats
        $conventionStats = [
            'total_bookings' => $totalConventionBookings,
            'active_bookings' => ConventionBooking::whereIn('status', ['confirmed', 'pending'])->count(),
            'total_halls' => ConventionHall::count(),
            'today_events' => ConventionBooking::whereDate('event_date', $today)->whereIn('status', ['confirmed'])->count(),
            'upcoming_events' => ConventionBooking::where('event_date', '>', $today)->whereIn('status', ['confirmed', 'pending'])->count(),
            'convention_revenue' => $conventionRevenue,
            'pending_bookings' => $pendingConventionBookings,
        ];
        
        // Combined stats for overview
        $stats = [
            'total_bookings' => $totalBookings,
            'active_bookings' => count($activeBookingIds),
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRoomsCount,
            'convention_bookings' => $totalConventionBookings,
            'today_checkins' => $todayCheckins,
            'today_checkouts' => $todayCheckouts,
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

            $currentBooking = $currentBookingsByRoom[$room->id] ?? null;
            $upcomingBooking = $upcomingBookingsByRoom[$room->id] ?? null;

            $availableFrom = $currentBooking ? $currentBooking->getCheckOutDateTime() : $now;

            $roomsWithStatus[] = [
                'room' => $room,
                'status' => $isOccupied ? 'occupied' : 'available',
                'current_booking' => $currentBooking,
                'upcoming_booking' => $upcomingBooking,
                'available_from' => $availableFrom,
            ];
        }

        // Load the next seven days of hall bookings in one query, not one
        // query per hall per day.
        $firstHallDate = Carbon::today('Asia/Dhaka')->toDateString();
        $lastHallDate = Carbon::today('Asia/Dhaka')->addDays(6)->toDateString();
        $hallBookingsByDay = ConventionBooking::with('conventionHall')
            ->whereDate('event_date', '>=', $firstHallDate)
            ->whereDate('event_date', '<=', $lastHallDate)
            ->whereIn('status', ['confirmed', 'pending'])
            ->get()
            ->groupBy(fn ($booking) => $booking->hall_id . '|' . Carbon::parse($booking->event_date)->toDateString());

        // Hall availability status for today and next 6 days
        $hallsWithStatus = [];
        foreach ($allHalls as $hall) {
            $hallDays = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::today()->addDays($i);
                $dateStr = $date->format('Y-m-d');
                
                $dayBookings = $hallBookingsByDay->get($hall->id . '|' . $dateStr, collect());

                $fullDayBooking = $dayBookings->firstWhere('time_slot', 'full_day');
                $morningBooking = $dayBookings->firstWhere('time_slot', 'morning');
                $nightBooking = $dayBookings->firstWhere('time_slot', 'night');

                $fullDayBooked = (bool) $fullDayBooking;
                $morningBooked = $fullDayBooked || (bool) $morningBooking;
                $nightBooked = $fullDayBooked || (bool) $nightBooking;
                
                // full_day status: available, booked, or unavailable (when morning/night booked separately)
                $fullDayStatus = 'available';
                if ($fullDayBooked) {
                    $fullDayStatus = 'booked';
                } elseif ($morningBooked || $nightBooked) {
                    $fullDayStatus = 'unavailable';
                }

                // Build hover details
                $morningDetails = null;
                if ($fullDayBooking) {
                    $morningDetails = $fullDayBooking;
                } elseif ($morningBooking) {
                    $morningDetails = $morningBooking;
                }

                $nightDetails = null;
                if ($fullDayBooking) {
                    $nightDetails = $fullDayBooking;
                } elseif ($nightBooking) {
                    $nightDetails = $nightBooking;
                }

                $fullDayDetails = $fullDayBooking;
                
                $hallDays[] = [
                    'date' => $date,
                    'date_str' => $dateStr,
                    'day_name' => $date->format('D'),
                    'day_num' => $date->format('d'),
                    'morning' => $morningBooked ? 'booked' : 'available',
                    'night' => $nightBooked ? 'booked' : 'available',
                    'full_day' => $fullDayStatus,
                    'morning_booking' => $morningDetails,
                    'night_booking' => $nightDetails,
                    'full_day_booking' => $fullDayDetails,
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
        $chartStart = Carbon::today('Asia/Dhaka')->subDays(6)->startOfDay();
        $chartEnd = Carbon::today('Asia/Dhaka')->endOfDay();
        $bookingCountsByDate = Booking::whereBetween('created_at', [$chartStart, $chartEnd])
            ->selectRaw('DATE(created_at) as report_date, COUNT(*) as total')
            ->groupBy('report_date')
            ->pluck('total', 'report_date');
        $conventionCountsByDate = ConventionBooking::whereBetween('created_at', [$chartStart, $chartEnd])
            ->selectRaw('DATE(created_at) as report_date, COUNT(*) as total')
            ->groupBy('report_date')
            ->pluck('total', 'report_date');
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('Asia/Dhaka')->subDays($i);
            $chartLabels[] = $date->format('D');
            $chartBookings[] = (int) $bookingCountsByDate->get($date->toDateString(), 0);
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
                ->with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts.fromRoom.roomType'])
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
            $date = Carbon::today('Asia/Dhaka')->subDays($i);
            $conventionChartBookings[] = (int) $conventionCountsByDate->get($date->toDateString(), 0);
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
                $bookedRoomIds = array_merge($bookedRoomIds, $booking->getAllRoomIds());
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

        $query = ConventionBooking::with('conventionHall')
            ->whereDate('event_date', $date)
            ->whereNotIn('status', ['cancelled']);

        // Slot overlap logic: full_day blocks both morning and night
        if ($slot === 'morning') {
            $query->whereIn('time_slot', ['morning', 'full_day']);
        } elseif ($slot === 'night') {
            $query->whereIn('time_slot', ['night', 'full_day']);
        } else {
            $query->where('time_slot', 'full_day');
        }

        $hallBookings = $query->get();
        $bookedHallIds = $hallBookings->pluck('hall_id')->toArray();

        return response()->json([
            'bookedHallIds' => $bookedHallIds,
            'hallBookings' => $hallBookings
        ]);
    }
}


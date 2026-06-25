<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ConventionBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = [];
        $today = Carbon::today();
        $now = Carbon::now();
        
        // ========== ROOM BOOKINGS ==========
        
        // 1. Today's Check-ins
        $todayCheckins = Booking::with('room')
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->get();
        
        foreach ($todayCheckins as $booking) {
            $notifications[] = [
                'type' => 'checkin',
                'title' => "Today's Check-In",
                'message' => $booking->customer_name . ' - ' . ($booking->room->room_number ?? 'N/A'),
                'time' => 'Today',
                'link' => route('admin.bookings.show', $booking->id),
                'read' => false,
                'priority' => 1,
            ];
        }
        
        // 2. Today's Check-outs
        $todayCheckouts = Booking::with('room')
            ->whereDate('check_out_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get();
        
        foreach ($todayCheckouts as $booking) {
            $notifications[] = [
                'type' => 'checkout',
                'title' => "Today's Check-Out",
                'message' => $booking->customer_name . ' - ' . ($booking->room->room_number ?? 'N/A'),
                'time' => 'Today',
                'link' => route('admin.bookings.show', $booking->id),
                'read' => false,
                'priority' => 2,
            ];
        }
        
        // 3. Room bookings with due payments (checked out but has remaining balance)
        $duePayments = Booking::where('remaining_payment', '>', 0)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereDate('check_out_date', '<=', $today)
            ->get();
        
        foreach ($duePayments as $booking) {
            $notifications[] = [
                'type' => 'due_payment',
                'title' => 'Outstanding Payment (Room)',
                'message' => $booking->customer_name . ' - BDT ' . number_format($booking->remaining_payment),
                'time' => Carbon::parse($booking->check_out_date)->diffForHumans(),
                'link' => route('admin.bookings.show', $booking->id),
                'read' => false,
                'priority' => 0,
            ];
        }
        
        // ========== CONVENTION BOOKINGS ==========
        
        // 4. Today's Convention Events
        $todayEvents = ConventionBooking::with('conventionHall')
            ->whereDate('event_date', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->get();
        
        foreach ($todayEvents as $event) {
            $slotText = $event->time_slot == 'morning' ? 'Morning' : ($event->time_slot == 'night' ? 'Night' : 'Full Day');
            $notifications[] = [
                'type' => 'convention_today',
                'title' => "Today's Convention Event",
                'message' => $event->customer_name . ' - ' . ($event->conventionHall->name ?? 'N/A') . ' (' . $slotText . ')',
                'time' => 'Today',
                'link' => route('admin.convention-bookings.show', $event->id),
                'read' => false,
                'priority' => 1,
            ];
        }
        
        // 5. Tomorrow's Convention Events
        $tomorrowEvents = ConventionBooking::with('conventionHall')
            ->whereDate('event_date', $today->copy()->addDay())
            ->whereIn('status', ['confirmed', 'pending'])
            ->get();
        
        foreach ($tomorrowEvents as $event) {
            $slotText = $event->time_slot == 'morning' ? 'Morning' : ($event->time_slot == 'night' ? 'Night' : 'Full Day');
            $notifications[] = [
                'type' => 'convention_upcoming',
                'title' => "Tomorrow's Convention Event",
                'message' => $event->customer_name . ' - ' . ($event->conventionHall->name ?? 'N/A') . ' (' . $slotText . ')',
                'time' => 'Tomorrow',
                'link' => route('admin.convention-bookings.show', $event->id),
                'read' => false,
                'priority' => 3,
            ];
        }
        
        // 6. Convention bookings with due payments
        $conventionDues = ConventionBooking::where('remaining_payment', '>', 0)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDate('event_date', '<=', $today)
            ->get();
        
        foreach ($conventionDues as $event) {
            $notifications[] = [
                'type' => 'due_payment',
                'title' => 'Outstanding Payment (Convention)',
                'message' => $event->customer_name . ' - BDT ' . number_format($event->remaining_payment),
                'time' => Carbon::parse($event->event_date)->diffForHumans(),
                'link' => route('admin.convention-bookings.show', $event->id),
                'read' => false,
                'priority' => 0,
            ];
        }
        
        // Sort by priority (0 = highest)
        usort($notifications, function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
        
        // Limit to 20 most important
        $notifications = array_slice($notifications, 0, 20);
        
        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => count($notifications),
        ]);
    }
    
    public function markRead()
    {
        // In a full implementation, you'd mark notifications as read in a database table
        // For now, just return success
        return response()->json(['success' => true]);
    }
}

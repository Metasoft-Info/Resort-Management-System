<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class AutoCheckoutBookings extends Command
{
    protected $signature = 'bookings:auto-checkout';
    protected $description = 'Automatically check-in and check-out bookings based on date+time';

    public function handle()
    {
        $now = Carbon::now('Asia/Dhaka');

        // ---- AUTO CHECK-IN ----
        $checkInBookings = Booking::where('status', 'confirmed')
            ->whereDate('check_in_date', '<=', $now->toDateString())
            ->get();

        $checkInCount = 0;
        foreach ($checkInBookings as $booking) {
            if ($booking->shouldBeCheckedIn($now)) {
                $booking->update(['status' => 'checked_in']);
                $this->markRoomsOccupied($booking);
                $checkInCount++;
                $this->info("Auto checked-in booking #{$booking->id} - {$booking->customer_name}");
            }
        }
        $this->info("Total bookings auto checked-in: {$checkInCount}");

        // ---- AUTO CHECK-OUT ----
        $checkOutBookings = Booking::where('status', 'checked_in')
            ->whereDate('check_out_date', '<=', $now->toDateString())
            ->get();

        $checkOutCount = 0;
        foreach ($checkOutBookings as $booking) {
            if ($booking->shouldBeCheckedOut($now)) {
                $booking->update(['status' => 'checked_out']);
                $this->markRoomsAvailable($booking);
                $checkOutCount++;
                $this->info("Auto checked-out booking #{$booking->id} - {$booking->customer_name}");
            }
        }
        $this->info("Total bookings auto checked-out: {$checkOutCount}");

        return Command::SUCCESS;
    }

    private function markRoomsOccupied(Booking $booking)
    {
        $roomIds = $this->getBookingRoomIds($booking);
        if (!empty($roomIds)) {
            Room::whereIn('id', $roomIds)->update(['status' => 'occupied']);
        }
    }

    private function markRoomsAvailable(Booking $booking)
    {
        $roomIds = $this->getBookingRoomIds($booking);
        if (!empty($roomIds)) {
            Room::whereIn('id', $roomIds)->update(['status' => 'available']);
        }
    }

    private function getBookingRoomIds(Booking $booking): array
    {
        $roomIds = [];
        if ($booking->room_id) {
            $roomIds[] = $booking->room_id;
        }
        foreach ($booking->bookingRooms as $bookingRoom) {
            $roomIds[] = $bookingRoom->room_id;
        }
        return array_unique($roomIds);
    }
}

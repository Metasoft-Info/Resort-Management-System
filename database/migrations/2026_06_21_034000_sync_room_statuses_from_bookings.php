<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;
use App\Models\Room;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'occupied' to the rooms.status enum if not present
        $currentEnum = \DB::select("SHOW COLUMNS FROM rooms WHERE Field = 'status'")[0]->Type ?? '';
        if (strpos($currentEnum, 'occupied') === false) {
            \DB::statement("ALTER TABLE rooms MODIFY status ENUM('available', 'booked', 'maintenance', 'occupied') NOT NULL DEFAULT 'available'");
        }

        // Find all rooms currently occupied by active checked-in bookings
        $occupiedRoomIds = [];
        $bookings = Booking::with('bookingRooms')->where('status', 'checked_in')->get();
        foreach ($bookings as $booking) {
            if ($booking->room_id) {
                $occupiedRoomIds[] = $booking->room_id;
            }
            foreach ($booking->bookingRooms as $br) {
                $occupiedRoomIds[] = $br->room_id;
            }
        }
        $occupiedRoomIds = array_unique($occupiedRoomIds);

        // Mark occupied rooms
        if (!empty($occupiedRoomIds)) {
            Room::whereIn('id', $occupiedRoomIds)->update(['status' => 'occupied']);
        }

        // Mark all other rooms as available
        Room::whereNotIn('id', $occupiedRoomIds)->update(['status' => 'available']);
    }

    public function down(): void
    {
        //
    }
};

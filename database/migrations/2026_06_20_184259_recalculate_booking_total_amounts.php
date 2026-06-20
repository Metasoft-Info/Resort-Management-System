<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $bookings = \DB::table('bookings')->get();

        foreach ($bookings as $booking) {
            $nights = max(1, \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)));

            // Check if premium booking (has booking_rooms)
            $bookingRooms = \DB::table('booking_rooms')
                ->where('booking_id', $booking->id)
                ->get();

            $correctTotal = 0;

            if ($bookingRooms->count() > 0) {
                // Premium booking: sum stored room prices
                foreach ($bookingRooms as $br) {
                    $correctTotal += ($br->price_per_night ?? 0) * $nights;
                }
            } else {
                // Legacy single-room booking: get room price
                $room = \DB::table('rooms')
                    ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                    ->where('rooms.id', $booking->room_id)
                    ->select('rooms.price_per_night', 'room_types.base_price')
                    ->first();

                if ($room) {
                    $price = $room->price_per_night ?? $room->base_price ?? 0;
                    $correctTotal = $price * $nights;
                }
            }

            // If we could calculate a valid total and it's different from stored, update it
            if ($correctTotal > 0 && $correctTotal != $booking->total_amount) {
                \DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update(['total_amount' => $correctTotal]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

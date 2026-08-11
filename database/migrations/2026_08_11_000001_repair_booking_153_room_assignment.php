<?php

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Repair the malformed assignment captured in the 2026-08-11 backup.
     * Booking #153 was created for room 404, but an old public helper added
     * room 201 later without updating the booking total.
     */
    public function up(): void
    {
        $booking = Booking::find(153);
        $room201 = Room::where('room_number', '201')->first();
        $room404 = Room::where('room_number', '404')->first();

        if (!$booking || !$room201 || !$room404) {
            return;
        }

        $hasExpectedRoom = BookingRoom::where('booking_id', $booking->id)
            ->where('room_id', $room404->id)
            ->exists();
        $unexpectedRoom = BookingRoom::where('booking_id', $booking->id)
            ->where('room_id', $room201->id)
            ->where('created_at', '>', $booking->created_at)
            ->exists();

        if (!$hasExpectedRoom || !$unexpectedRoom) {
            return;
        }

        DB::transaction(function () use ($booking, $room201) {
            BookingRoom::where('booking_id', $booking->id)
                ->where('room_id', $room201->id)
                ->where('created_at', '>', $booking->created_at)
                ->delete();

            // booking_rooms is the canonical association for this booking.
            if ((int) $booking->room_id === (int) $room201->id) {
                $booking->room_id = null;
            }

            $booking->refresh();
            $booking->load(['bookingRooms', 'payments', 'room.roomType']);
            $booking->total_amount = $booking->getCalculatedTotal();
            $booking->remaining_payment = max(0, $booking->getCalculatedRemaining());
            $booking->payment_status = $booking->getCalculatedPaymentStatus();

            $roomNumbers = $booking->getAllRooms()->pluck('room_number')->implode(', ');
            $notes = preg_replace('/\[Rooms:.*?\]/', '', $booking->notes ?? '');
            $booking->notes = trim($notes) . " [Rooms: {$roomNumbers}]";
            $booking->save();
        });
    }

    public function down(): void
    {
        // This is a one-time data repair and is intentionally not reversible.
    }
};

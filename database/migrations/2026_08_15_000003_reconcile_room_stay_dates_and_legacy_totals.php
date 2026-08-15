<?php

use App\Models\Booking;
use App\Models\BookingRoom;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair room-level dates and legacy bookings before recalculating bills.
     * A booking has one shared checkout date. Old rows may have an earlier
     * room checkout, which makes a visible 3-night stay bill as 2 nights.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bookings') || !Schema::hasTable('booking_rooms')) {
            return;
        }

        Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'payments'])
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    DB::transaction(function () use ($booking) {
                        $parentCheckIn = $booking->check_in_date?->toDateString();
                        $parentCheckOut = $booking->check_out_date?->toDateString();

                        if ($booking->bookingRooms->isEmpty() && $booking->room_id && $parentCheckIn && $parentCheckOut) {
                            $room = $booking->room;
                            $currentRate = $room?->price_per_night
                                ?? $room?->roomType?->base_price;

                            BookingRoom::firstOrCreate(
                                [
                                    'booking_id' => $booking->id,
                                    'room_id' => $booking->room_id,
                                ],
                                [
                                    'price_per_night' => (float) ($currentRate ?? 0),
                                    'check_in_date' => $parentCheckIn,
                                    'check_out_date' => $parentCheckOut,
                                ]
                            );
                        } else {
                            foreach ($booking->bookingRooms as $bookingRoom) {
                                $currentRate = $bookingRoom->room?->price_per_night
                                    ?? $bookingRoom->room?->roomType?->base_price;

                                if ($currentRate !== null && (float) $currentRate > 0) {
                                    $bookingRoom->price_per_night = (float) $currentRate;
                                }

                                if (!$bookingRoom->check_in_date && $parentCheckIn) {
                                    $bookingRoom->check_in_date = $parentCheckIn;
                                }

                                // Checkout is shared by the booking and is
                                // authoritative over stale room-row dates.
                                if ($parentCheckOut) {
                                    $bookingRoom->check_out_date = $parentCheckOut;
                                }

                                $bookingRoom->saveQuietly();
                            }
                        }

                        $booking->unsetRelation('bookingRooms');
                        $booking->unsetRelation('room');
                        $booking->total_amount = $booking->getCalculatedTotal();
                        $booking->vat_amount = $booking->getVatAmount();
                        $booking->remaining_payment = max(0, $booking->getCalculatedRemaining());
                        $booking->payment_status = $booking->getCalculatedPaymentStatus();
                        $booking->saveQuietly();
                    });
                }
            });
    }

    public function down(): void
    {
        // Historical date and billing repairs are intentionally not reversed.
    }
};

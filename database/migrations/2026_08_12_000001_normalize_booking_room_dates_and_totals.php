<?php

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalize historical room bookings after room-level dates became the
     * canonical source for nightly totals.
     */
    public function up(): void
    {
        if (!Schema::hasTable('booking_rooms')) {
            return;
        }

        Booking::with(['bookingRooms', 'payments'])
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    DB::transaction(function () use ($booking) {
                        if ($booking->bookingRooms->isEmpty() && $booking->room_id) {
                            $room = Room::with('roomType')->find($booking->room_id);

                            if ($room && $booking->check_in_date && $booking->check_out_date) {
                                $nights = max(1, $booking->check_in_date->diffInDays($booking->check_out_date));
                                $storedTotal = (float) ($booking->total_amount ?? 0);
                                $pricePerNight = $storedTotal > 0
                                    ? $storedTotal / $nights
                                    : (float) ($room->price_per_night ?? $room->roomType?->base_price ?? 0);

                                BookingRoom::firstOrCreate(
                                    ['booking_id' => $booking->id, 'room_id' => $booking->room_id],
                                    [
                                        'price_per_night' => $pricePerNight,
                                        'check_in_date' => $booking->check_in_date->toDateString(),
                                        'check_out_date' => $booking->check_out_date->toDateString(),
                                    ]
                                );
                            }
                        }

                        if ($booking->bookingRooms->isNotEmpty()) {
                            foreach ($booking->bookingRooms as $bookingRoom) {
                                // The booking's checkout is the shared stay
                                // end. This repairs extensions saved only on
                                // the parent booking by the older flow.
                                if ($booking->check_out_date) {
                                    $bookingRoom->check_out_date = $booking->check_out_date->toDateString();
                                }

                                // Preserve deliberately later room check-ins,
                                // but fill missing dates from the parent.
                                if (!$bookingRoom->check_in_date && $booking->check_in_date) {
                                    $bookingRoom->check_in_date = $booking->check_in_date->toDateString();
                                }

                                $bookingRoom->save();
                            }
                        }

                        // A newly created legacy row is not in the eager-loaded
                        // relation, so force a fresh canonical calculation.
                        $booking->unsetRelation('bookingRooms');
                        $correctTotal = (float) $booking->getCalculatedTotal();
                        $booking->total_amount = $correctTotal;
                        $booking->remaining_payment = max(0, (float) $booking->getCalculatedRemaining());
                        $booking->payment_status = $booking->getCalculatedPaymentStatus();
                        $booking->save();
                    });
                }
            });
    }

    public function down(): void
    {
        // Historical data normalization is intentionally not reversed.
    }
};

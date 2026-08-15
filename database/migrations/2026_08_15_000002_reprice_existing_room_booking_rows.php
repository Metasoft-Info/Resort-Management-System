<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair old booking room rows that captured an outdated nightly rate.
     * The room's current published price is the billing source for resort
     * bookings, so a 3-night stay at BDT 3,000 must be BDT 9,000.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bookings') || !Schema::hasTable('booking_rooms')) {
            return;
        }

        Booking::with(['bookingRooms.room.roomType', 'payments'])
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    DB::transaction(function () use ($booking) {
                        foreach ($booking->bookingRooms as $bookingRoom) {
                            $roomRate = $bookingRoom->room?->price_per_night
                                ?? $bookingRoom->room?->roomType?->base_price;

                            if ($roomRate !== null && (float) $roomRate > 0) {
                                $bookingRoom->price_per_night = (float) $roomRate;
                                $bookingRoom->saveQuietly();
                            }
                        }

                        $booking->unsetRelation('bookingRooms');
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
        // Repricing historical bookings is intentionally not reversed.
    }
};

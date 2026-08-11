<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Make stored totals and the legacy room pointer agree with booking_rooms.
     * This also repairs databases where the earlier incident cleanup removed
     * the extra booking_rooms row but left bookings.room_id pointing to it.
     */
    public function up(): void
    {
        Booking::with(['bookingRooms', 'payments'])
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    if ($booking->bookingRooms->isEmpty()) {
                        continue;
                    }

                    $canonicalRoomIds = $booking->bookingRooms
                        ->pluck('room_id')
                        ->filter()
                        ->map(fn ($roomId) => (int) $roomId)
                        ->unique()
                        ->values();

                    // Keep the old pointer only for a canonical single-room
                    // booking; multi-room bookings use booking_rooms only.
                    $canonicalLegacyRoomId = $canonicalRoomIds->count() === 1
                        ? $canonicalRoomIds->first()
                        : null;

                    $booking->room_id = $canonicalLegacyRoomId;
                    $correctTotal = (float) $booking->getCalculatedTotal();
                    $booking->total_amount = $correctTotal;
                    $booking->remaining_payment = max(0, (float) $booking->getCalculatedRemaining());
                    $booking->payment_status = $booking->getCalculatedPaymentStatus();
                    $booking->save();
                }
            });
    }

    public function down(): void
    {
        // Data synchronization is intentionally not reversed.
    }
};

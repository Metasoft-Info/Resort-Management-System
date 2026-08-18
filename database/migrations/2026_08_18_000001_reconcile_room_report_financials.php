<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rebuild stored room-booking balances after the report ledger was made
     * canonical. This is safe to run more than once and repairs old rows where
     * advance/payment history and remaining_payment had drifted apart.
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
                        $booking->total_amount = $booking->getCalculatedTotal();
                        $booking->vat_amount = $booking->getVatAmount();
                        $booking->remaining_payment = $booking->getCalculatedRemaining();
                        $booking->payment_status = $booking->getCalculatedPaymentStatus();
                        $booking->saveQuietly();
                    });
                }
            });
    }

    public function down(): void
    {
        // Financial reconciliation is intentionally not reversed.
    }
};

<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bring stored totals/balances in line with the canonical room rows.
     * This is intentionally idempotent so a deployment can safely run it
     * once on an existing production database.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bookings') || !Schema::hasTable('booking_rooms')) {
            return;
        }

        Booking::with(['bookingRooms', 'payments'])
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    DB::transaction(function () use ($booking) {
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
        // Financial normalization is intentionally not reversed.
    }
};

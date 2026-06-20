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
        $bookings = \App\Models\Booking::with(['bookingRooms', 'payments'])->get();

        foreach ($bookings as $booking) {
            $correctTotal = $booking->getCalculatedTotal();

            // Only update if we got a valid total different from stored
            if ($correctTotal > 0 && $correctTotal != $booking->total_amount) {
                $booking->total_amount = $correctTotal;

                // Recalculate remaining payment using model methods
                $booking->remaining_payment = max(0, $booking->getCalculatedRemaining());
                $booking->payment_status = $booking->remaining_payment <= 0 ? 'paid' : ($booking->getTotalDeposited() > 0 ? 'partial' : 'pending');

                $booking->save();
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

<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove exact duplicate payment rows created by a double submit.
     *
     * A duplicate is only considered safe to remove when every payment
     * field, including both timestamps, matches and the older row is kept.
     * This makes the repair idempotent and leaves legitimate separate
     * payments untouched.
     */
    public function up(): void
    {
        if (! Schema::hasTable('booking_payments')) {
            return;
        }

        $affectedBookingIds = [];

        DB::transaction(function () use (&$affectedBookingIds) {
            $seen = [];

            DB::table('booking_payments')
                ->where('type', 'payment')
                ->orderBy('id')
                ->get([
                    'id',
                    'booking_id',
                    'amount',
                    'method',
                    'type',
                    'note',
                    'recorded_by_id',
                    'created_at',
                    'updated_at',
                ])
                ->each(function ($payment) use (&$seen, &$affectedBookingIds) {
                    $fingerprint = implode("\x1f", [
                        $payment->booking_id,
                        number_format((float) $payment->amount, 2, '.', ''),
                        $payment->method ?? '',
                        $payment->type ?? '',
                        $payment->note ?? '<null>',
                        $payment->recorded_by_id ?? '<null>',
                        $payment->created_at ?? '<null>',
                        $payment->updated_at ?? '<null>',
                    ]);

                    if (isset($seen[$fingerprint])) {
                        DB::table('booking_payments')
                            ->where('id', $payment->id)
                            ->delete();

                        $affectedBookingIds[(int) $payment->booking_id] = true;

                        return;
                    }

                    $seen[$fingerprint] = (int) $payment->id;
                });

            foreach (array_keys($affectedBookingIds) as $bookingId) {
                $booking = Booking::with(['bookingRooms', 'payments'])->find($bookingId);

                if (! $booking) {
                    continue;
                }

                $booking->total_amount = $booking->getCalculatedTotal();
                $booking->vat_amount = $booking->getVatAmount();
                $booking->remaining_payment = max(0, $booking->getCalculatedRemaining());
                $booking->payment_status = $booking->getCalculatedPaymentStatus();
                $booking->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        // Deleted duplicate ledger rows cannot be reconstructed safely.
    }
};

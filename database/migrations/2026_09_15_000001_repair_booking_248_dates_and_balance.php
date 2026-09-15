<?php

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair the live booking shown in the September 15 incident report.
     *
     * This is intentionally idempotent: deployment may be retried, and a
     * second run must not create another room row or another payment.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        $booking = Booking::query()
            ->whereKey(248)
            ->first();

        if ($booking
            && ((string) $booking->customer_phone !== '01700760757'
                || (string) $booking->customer_name !== 'Syed Kamrul Islam')) {
            $booking = null;
        }

        // Keep the repair safe if a production export was re-numbered while
        // the customer identity stayed the same.
        if (! $booking) {
            $booking = Booking::query()
                ->where('customer_phone', '01700760757')
                ->where('customer_name', 'Syed Kamrul Islam')
                ->first();
        }

        if (! $booking) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $booking = Booking::query()
                ->with(['bookingRooms.room.roomType', 'room.roomType', 'payments'])
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->first();

            if (! $booking) {
                return;
            }

            $checkInDate = '2026-09-14';
            $checkOutDate = '2026-09-17';
            $checkInTime = $booking->check_in_time ?: '12:00';
            $checkOutTime = $booking->check_out_time ?: '12:00';

            // Normalize legacy single-room records before changing the stay.
            // This makes future edits and billing use the same room-level row.
            if ($booking->bookingRooms->isEmpty() && $booking->room_id) {
                $room = $booking->room;
                if ($room) {
                    $rate = (float) ($room->price_per_night
                        ?? $room->roomType?->base_price
                        ?? 0);

                    $booking->bookingRooms()->create([
                        'room_id' => $booking->room_id,
                        'price_per_night' => $rate,
                        'check_in_date' => $checkInDate,
                        'check_out_date' => $checkOutDate,
                    ]);
                }
            } else {
                foreach ($booking->bookingRooms as $bookingRoom) {
                    $bookingRoom->update([
                        'check_in_date' => $checkInDate,
                        'check_out_date' => $checkOutDate,
                    ]);
                }
            }

            $booking->check_in_date = $checkInDate;
            $booking->check_out_date = $checkOutDate;
            $booking->check_in_time = $checkInTime;
            $booking->check_out_time = $checkOutTime;

            // A checked-out booking whose corrected checkout is still in the
            // future is currently staying again. Keep room/status state in
            // agreement with the corrected dates.
            $correctedCheckout = Carbon::parse($checkOutDate, 'Asia/Dhaka')
                ->setTimeFromTimeString($checkOutTime);
            $recheckIn = $booking->status === 'checked_out'
                && Carbon::now('Asia/Dhaka')->lt($correctedCheckout);

            if ($recheckIn) {
                $booking->status = 'checked_in';
            }

            $booking->unsetRelation('bookingRooms');
            $booking->unsetRelation('payments');
            $booking->load(['bookingRooms.room.roomType', 'room.roomType', 'payments']);

            $financials = $booking->getFinancialBreakdown();
            $booking->total_amount = $financials['room_rent'];
            $booking->vat_amount = $financials['vat'];
            $booking->remaining_payment = $financials['remaining'];
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->saveQuietly();

            if ($recheckIn && Schema::hasTable('rooms')) {
                $roomIds = $booking->getAllRoomIds();
                if (! empty($roomIds)) {
                    DB::table('rooms')
                        ->whereIn('id', $roomIds)
                        ->update(['status' => 'occupied']);
                }
            }

            if (Schema::hasTable('booking_financial_snapshots')) {
                $booking->recordFinancialSnapshot('repair_booking_248_dates');
            }
        });
    }

    public function down(): void
    {
        // The previous dates are not reliably recoverable from the live row;
        // keep the repair irreversible rather than reintroducing bad data.
    }
};

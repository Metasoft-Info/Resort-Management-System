<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bookings') || !Schema::hasTable('booking_rooms')) {
            return;
        }

        DB::transaction(function () {
            $booking = Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'payments', 'roomShifts'])
                ->whereKey(366)->lockForUpdate()->first();
            if (!$booking) {
                return;
            }

            $assignment = $booking->bookingRooms->count() === 1 ? $booking->bookingRooms->first() : null;
            $matchesBackup = substr((string) $booking->getRawOriginal('created_at'), 0, 10) === '2026-10-14'
                && in_array($booking->check_in_date->toDateString(), ['2026-10-11', '2026-10-12'], true)
                && $booking->check_out_date->toDateString() === '2026-10-15'
                && !$booking->is_complimentary
                && $booking->roomShifts->isEmpty()
                && $assignment && $assignment->room?->room_number === '306'
                && (float) $assignment->price_per_night === 1000.0
                && (float) ($assignment->room->price_per_night ?? $assignment->room->roomType?->base_price) === 1000.0
                && in_array($booking->discount_type, ['none', 'flat'], true)
                && in_array((float) $booking->discount_amount, [0.0, 2000.0], true)
                && (float) $booking->discount_percentage === 0.0;

            if (!$matchesBackup) {
                Log::warning('Booking 366 correction skipped: live dates, room or discount differ from the supplied backup.');
                return;
            }

            $assignment->update(['check_in_date' => '2026-10-12', 'check_out_date' => '2026-10-15']);
            $booking->fill([
                'check_in_date' => '2026-10-12',
                'check_out_date' => '2026-10-15',
                'discount_type' => 'none',
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'discount_reference' => null,
                'discount_status' => null,
                'discount_requested_by' => null,
                'discount_approved_by' => null,
                'discount_approved_at' => null,
            ]);
            $booking->unsetRelation('bookingRooms');
            $booking->load('bookingRooms.room.roomType');
            $financials = $booking->getFinancialBreakdown();
            $booking->total_amount = $financials['room_rent'];
            $booking->vat_amount = $financials['vat'];
            $booking->remaining_payment = $financials['remaining'];
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->saveQuietly();

            if (Schema::hasTable('booking_financial_snapshots')) {
                $booking->recordFinancialSnapshot('repair_booking_366_dates_and_discount', '2026-10-15');
            }
        });
    }

    public function down(): void
    {
        // Keep the corrected bill and its audit snapshot on rollback.
    }
};

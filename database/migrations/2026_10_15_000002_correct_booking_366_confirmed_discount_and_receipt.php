<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PAYMENT_REQUEST_ID = '812da458-01c4-47aa-ad68-878da1b1a573';
    private const AUDIT_ACTION = 'Corrected booking 366 client-confirmed discount and receipt';

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_payments', 'booking_room_shifts'] as $table) {
            if (!Schema::hasTable($table)) {
                return;
            }
        }

        DB::transaction(function () {
            $booking = Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts'])
                ->whereKey(366)->lockForUpdate()->first();
            if (!$booking) {
                return;
            }

            $payments = $booking->payments()->lockForUpdate()->get();
            $booking->setRelation('payments', $payments);
            $assignment = $booking->bookingRooms->count() === 1 ? $booking->bookingRooms->first() : null;
            $payment = $payments->count() === 1 ? $payments->first() : null;
            $knownDiscount = ($booking->discount_type === 'none'
                    && (float) $booking->discount_amount === 0.0 && (float) $booking->discount_percentage === 0.0)
                || ($booking->discount_type === 'flat'
                    && in_array((float) $booking->discount_amount, [1500.0, 2000.0], true)
                    && (float) $booking->discount_percentage === 0.0)
                || ($booking->discount_type === 'percentage' && (float) $booking->discount_percentage === 50.0
                    && in_array((float) $booking->discount_amount, [0.0, 1500.0], true));

            $matchesBooking = substr((string) $booking->getRawOriginal('created_at'), 0, 10) === '2026-10-14'
                && in_array($booking->status, ['confirmed', 'checked_in', 'checked_out'], true)
                && $booking->check_in_date->toDateString() === '2026-10-12'
                && $booking->check_out_date->toDateString() === '2026-10-15'
                && !$booking->is_complimentary && !$booking->vat_enabled
                && (float) $booking->extra_charges === 0.0 && (float) $booking->advance_payment === 0.0
                && $booking->roomShifts->isEmpty() && $knownDiscount
                && $assignment && $assignment->room?->room_number === '306'
                && $assignment->check_in_date?->toDateString() === '2026-10-12'
                && $assignment->check_out_date?->toDateString() === '2026-10-15'
                && $booking->getCalculatedTotal() === 3000.0;
            $matchesReceipt = $payment && (int) $payment->id === 347
                && $payment->request_id === self::PAYMENT_REQUEST_ID
                && $payment->type === 'payment' && $payment->method === 'cash'
                && $payment->payment_date?->toDateString() === '2026-10-15'
                && in_array((float) $payment->amount, [1500.0, 2000.0], true);

            if (!$matchesBooking || !$matchesReceipt) {
                Log::warning('Booking 366 receipt correction skipped: the live bill or payment ledger differs from the confirmed record.');
                return;
            }

            $before = $booking->getFinancialBreakdown();
            $oldPaymentAmount = (float) $payment->amount;
            if ($oldPaymentAmount === 2000.0) {
                // The client confirmed that the original receipt was entered
                // incorrectly. Correct that same receipt, retaining its date,
                // method, operator and request ID, with the prior value noted.
                $payment->amount = 1500;
                $payment->note = trim(($payment->note ?? '')."\n[Booking 366 correction 2026-10-15] Actual receipt corrected from BDT 2,000.00 to BDT 1,500.00 per client confirmation.");
                $payment->save();
            }

            $booking->fill([
                'discount_type' => 'percentage',
                'discount_percentage' => 50,
                'discount_amount' => 1500,
                'discount_status' => 'approved',
                'discount_reference' => 'Client-confirmed 50% discount; booking 366 correction on 15 Oct 2026.',
                'discount_requested_by' => null,
                'discount_approved_by' => $booking->discount_status === 'approved' ? $booking->discount_approved_by : null,
                'discount_approved_at' => $booking->discount_status === 'approved' && $booking->discount_approved_at
                    ? $booking->discount_approved_at : now('Asia/Dhaka'),
            ]);
            $after = $booking->getFinancialBreakdown();
            $booking->total_amount = $after['room_rent'];
            $booking->vat_amount = $after['vat'];
            $booking->remaining_payment = $after['remaining'];
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->saveQuietly();

            if (Schema::hasTable('booking_financial_snapshots')) {
                $booking->recordFinancialSnapshot('correct_booking_366_confirmed_discount_and_receipt', '2026-10-15');
            }

            if (Schema::hasTable('activity_logs') && !DB::table('activity_logs')
                ->where('entity_type', 'Booking')->where('entity_id', 366)->where('action', self::AUDIT_ACTION)->exists()) {
                DB::table('activity_logs')->insert([
                    'user_id' => null,
                    'action' => self::AUDIT_ACTION,
                    'entity_type' => 'Booking',
                    'entity_id' => 366,
                    'changes' => json_encode([
                        'reason' => 'Client confirmed 3,000 room rent, 50% discount and 1,500 actual paid.',
                        'payment_id' => $payment->id,
                        'previous_payment_amount' => $oldPaymentAmount,
                        'corrected_payment_amount' => 1500,
                        'before' => $before,
                        'after' => $after,
                    ], JSON_THROW_ON_ERROR),
                    'created_at' => now('Asia/Dhaka'),
                    'updated_at' => now('Asia/Dhaka'),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Preserve the client-confirmed bill, receipt and correction trail.
    }
};

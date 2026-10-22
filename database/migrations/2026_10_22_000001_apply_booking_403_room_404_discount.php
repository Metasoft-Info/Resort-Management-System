<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AUDIT_ACTION = 'Corrected booking 403 BDT 200 discount and receipt';
    private const PAYMENT_REQUEST_ID = 'a2619262-c537-4710-9a03-9b70e3357b8b';

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_payments', 'booking_room_shifts'] as $table) {
            if (!Schema::hasTable($table)) {
                Log::warning('Booking 403 discount correction skipped: a required table is missing.', ['table' => $table]);
                return;
            }
        }

        DB::transaction(function () {
            $booking = Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts'])
                ->whereKey(403)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                Log::warning('Booking 403 discount correction skipped: booking not found.');
                return;
            }

            $payments = $booking->payments()->lockForUpdate()->get();
            $booking->setRelation('payments', $payments);
            $assignment = $booking->bookingRooms->count() === 1 ? $booking->bookingRooms->first() : null;
            $payment = $payments->count() === 1 ? $payments->first() : null;
            $hasKnownDiscount = ($booking->discount_type === 'none'
                    && (float) $booking->discount_amount === 0.0
                    && (float) $booking->discount_percentage === 0.0)
                || ($booking->discount_type === 'flat'
                    && (float) $booking->discount_amount === 200.0
                    && (float) $booking->discount_percentage === 0.0);

            $matchesPayment = $payment
                && (int) $payment->id === 377
                && $payment->request_id === self::PAYMENT_REQUEST_ID
                && $payment->type === 'payment'
                && $payment->method === 'cash'
                && in_array((float) $payment->amount, [1800.0, 2000.0], true)
                && $payment->payment_date?->toDateString() === '2026-10-22';

            $matchesBooking = substr((string) $booking->getRawOriginal('created_at'), 0, 10) === '2026-10-22'
                && $booking->customer_name === 'Saiful Islam'
                && $booking->customer_phone === '01963672304'
                && $booking->status === 'checked_out'
                && $booking->check_in_date->toDateString() === '2026-10-21'
                && $booking->check_out_date->toDateString() === '2026-10-22'
                && (float) $booking->total_amount === 2000.0
                && (float) $booking->advance_payment === 0.0
                && (float) $booking->extra_charges === 0.0
                && (float) $booking->food_package_cost === 0.0
                && (float) $booking->addons_cost === 0.0
                && (float) $booking->vat_amount === 0.0
                && !$booking->vat_enabled
                && !$booking->is_complimentary
                && $booking->booking_group_id === null
                && $booking->roomShifts->isEmpty()
                && $hasKnownDiscount
                && in_array($booking->discount_status, ['pending', 'approved', null], true)
                && $booking->bookingRooms->count() === 1
                && $assignment
                && (string) $assignment->room?->room_number === '404'
                && $assignment->check_in_date?->toDateString() === '2026-10-21'
                && $assignment->check_out_date?->toDateString() === '2026-10-22'
                && (float) $assignment->price_per_night === 2000.0
                && $booking->getCalculatedTotal() === 2000.0
                && $booking->payment_status === 'paid'
                && $payments->count() === 1
                && $matchesPayment;

            if (!$matchesBooking) {
                Log::warning('Booking 403 discount correction skipped: the guest, dates, room, bill, or payment ledger differs from the supplied backup.');
                return;
            }

            $before = $booking->getFinancialBreakdown();
            $oldPaymentAmount = (float) $payment->amount;
            if ($oldPaymentAmount === 2000.0) {
                $payment->amount = 1800;
                $payment->note = trim(($payment->note ?? '')."\n[Booking 403 correction 2026-10-22] Receipt corrected from BDT 2,000.00 to BDT 1,800.00 to reflect the client-confirmed BDT 200 room discount.");
                $payment->save();
                $booking->unsetRelation('payments');
                $booking->setRelation('payments', $booking->payments()->lockForUpdate()->get());
            }

            $reference = 'Client-confirmed BDT 200 room discount correction on 22 Oct 2026.';
            if (trim((string) $booking->discount_reference) !== '') {
                $reference .= ' Previous reference: '.trim((string) $booking->discount_reference);
            }

            $booking->fill([
                'discount_type' => 'flat',
                'discount_amount' => 200,
                'discount_percentage' => 0,
                'discount_status' => 'approved',
                'discount_reference' => substr($reference, 0, 255),
                'discount_requested_by' => null,
                'discount_approved_by' => $booking->discount_status === 'approved' ? $booking->discount_approved_by : null,
                'discount_approved_at' => $booking->discount_status === 'approved' && $booking->discount_approved_at
                    ? $booking->discount_approved_at
                    : now('Asia/Dhaka'),
            ]);

            $after = $booking->getFinancialBreakdown();
            if ($after['room_rent'] !== 2000.0 || $after['discount'] !== 200.0
                || $after['grand_total'] !== 1800.0 || $after['deposited'] !== 1800.0
                || $after['remaining'] !== 0.0) {
                throw new \RuntimeException('Booking 403 correction did not produce the client-confirmed BDT 1,800 paid bill.');
            }
            $booking->total_amount = $after['room_rent'];
            $booking->vat_amount = $after['vat'];
            $booking->remaining_payment = $after['remaining'];
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->saveQuietly();

            if (Schema::hasTable('booking_financial_snapshots')) {
                $booking->recordFinancialSnapshot('apply_booking_403_missed_room_discount', '2026-10-22');
            }

            if (Schema::hasTable('activity_logs') && !DB::table('activity_logs')
                ->where('entity_type', 'Booking')
                ->where('entity_id', 403)
                ->where('action', self::AUDIT_ACTION)
                ->exists()) {
                DB::table('activity_logs')->insert([
                    'user_id' => null,
                    'action' => self::AUDIT_ACTION,
                    'entity_type' => 'Booking',
                    'entity_id' => 403,
                    'changes' => json_encode([
                        'reason' => 'Operator omitted the client-confirmed BDT 200 discount; the same cash receipt was corrected to the actual BDT 1,800 paid amount.',
                        'payment_id' => $payment->id,
                        'previous_payment_amount' => $oldPaymentAmount,
                        'corrected_payment_amount' => 1800,
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
        // Preserve the client-confirmed discount and its audit history.
    }
};

<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AUDIT_ACTION = 'Corrected cash allocation for bookings 382 and 383';

    /** @var array<int, array{room: string, request: string}> */
    private const BOOKINGS = [
        382 => ['room' => '201', 'request' => '38200000-0000-4000-8000-000000000382'],
        383 => ['room' => '202', 'request' => '38300000-0000-4000-8000-000000000383'],
    ];

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_payments', 'booking_room_shifts'] as $table) {
            if (!Schema::hasTable($table)) {
                Log::warning('Booking 382/383 cash allocation correction skipped: a required table is missing.', ['table' => $table]);
                return;
            }
        }

        foreach (self::BOOKINGS as $bookingId => $details) {
            DB::transaction(fn () => $this->correctBooking($bookingId, $details['room'], $details['request']));
        }
    }

    private function correctBooking(int $bookingId, string $expectedRoom, string $requestId): void
    {
        $booking = Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts'])
            ->whereKey($bookingId)
            ->lockForUpdate()
            ->first();

        if (!$booking) {
            Log::warning("Booking {$bookingId} cash allocation correction skipped: booking not found.");
            return;
        }

        $payments = $booking->payments()->lockForUpdate()->get();
        $booking->setRelation('payments', $payments);
        $assignment = $booking->bookingRooms->count() === 1 ? $booking->bookingRooms->first() : null;
        $payment = $payments->count() === 1 ? $payments->first() : null;
        $knownDiscount = $booking->discount_type === 'flat'
            && in_array((float) $booking->discount_amount, [2500.0, 3250.0], true)
            && (float) $booking->discount_percentage === 0.0;
        $knownPayment = $payment
            && $payment->request_id === $requestId
            && $payment->type === 'payment'
            && $payment->method === 'cash'
            && in_array((float) $payment->amount, [750.0, 1500.0], true)
            && $payment->payment_date?->toDateString() === '2026-10-18';

        $matchesBooking = substr((string) $booking->getRawOriginal('created_at'), 0, 10) === '2026-10-18'
            && $booking->status === 'checked_out'
            && $booking->check_in_date->toDateString() === '2026-10-17'
            && $booking->check_out_date->toDateString() === '2026-10-18'
            && (float) $booking->total_amount === 4000.0
            && (float) $booking->advance_payment === 0.0
            && $booking->payment_method === 'cash'
            && (float) $booking->extra_charges === 0.0
            && (float) $booking->food_package_cost === 0.0
            && (float) $booking->addons_cost === 0.0
            && !$booking->vat_enabled
            && (float) $booking->vat_amount === 0.0
            && !$booking->is_complimentary
            && $booking->booking_group_id === null
            && $booking->roomShifts->isEmpty()
            && $knownDiscount
            && $booking->discount_status === 'approved'
            && $assignment
            && $booking->bookingRooms->count() === 1
            && (string) $assignment->room?->room_number === $expectedRoom
            && $assignment->check_in_date?->toDateString() === '2026-10-17'
            && $assignment->check_out_date?->toDateString() === '2026-10-18'
            && (float) $assignment->price_per_night === 4000.0
            && $booking->getCalculatedTotal() === 4000.0
            && $payments->count() === 1
            && $knownPayment;

        if (!$matchesBooking) {
            Log::warning("Booking {$bookingId} cash allocation correction skipped: the bill or payment ledger differs from the expected client-confirmed state.");
            return;
        }

        $before = $booking->getFinancialBreakdown();
        if ((float) $payment->amount === 1500.0) {
            $payment->amount = 750;
            $payment->note = trim(($payment->note ?? '')."\n[Correction 18 Oct 2026] Receipt corrected from BDT 1,500.00 per room to BDT 750.00 per room; BDT 1,500.00 is the combined cash total for bookings 382 and 383, per client confirmation.");
            $payment->save();
        }

        $previousReference = trim((string) $booking->discount_reference);
        $reference = 'Client-confirmed BDT 3,250 discount; BDT 750 cash allocated to this room on 18 Oct 2026.';
        if ($previousReference !== '') {
            $reference .= ' Previous reference: '.$previousReference;
        }

        $booking->fill([
            'discount_type' => 'flat',
            'discount_amount' => 3250,
            'discount_percentage' => 0,
            'discount_status' => 'approved',
            'discount_reference' => substr($reference, 0, 255),
            'discount_approved_at' => $booking->discount_approved_at ?: now('Asia/Dhaka'),
        ]);
        $booking->unsetRelation('payments');
        $booking->load('bookingRooms.room.roomType');
        $booking->setRelation('payments', $booking->payments()->get());

        $after = $booking->getFinancialBreakdown();
        $booking->total_amount = $after['room_rent'];
        $booking->vat_amount = $after['vat'];
        $booking->remaining_payment = $after['remaining'];
        $booking->payment_status = $booking->getCalculatedPaymentStatus();
        $booking->saveQuietly();

        if (Schema::hasTable('booking_financial_snapshots')) {
            $booking->recordFinancialSnapshot('correct_bookings_382_383_cash_allocation', '2026-10-18');
        }

        if (Schema::hasTable('activity_logs') && !DB::table('activity_logs')
            ->where('entity_type', 'Booking')
            ->where('entity_id', $bookingId)
            ->where('action', self::AUDIT_ACTION)
            ->exists()) {
            DB::table('activity_logs')->insert([
                'user_id' => null,
                'action' => self::AUDIT_ACTION,
                'entity_type' => 'Booking',
                'entity_id' => $bookingId,
                'changes' => json_encode([
                    'reason' => 'Client clarified BDT 1,500 total cash across two rooms: BDT 750 per room; remaining balance discounted.',
                    'payment_id' => $payment->id,
                    'previous_payment_amount' => $before['deposited'],
                    'corrected_payment_amount' => $after['deposited'],
                    'previous_discount' => $before['discount'],
                    'corrected_discount' => $after['discount'],
                    'before' => $before,
                    'after' => $after,
                ], JSON_THROW_ON_ERROR),
                'created_at' => now('Asia/Dhaka'),
                'updated_at' => now('Asia/Dhaka'),
            ]);
        }
    }

    public function down(): void
    {
        // Preserve the client-confirmed payment allocation and corrected bills.
    }
};

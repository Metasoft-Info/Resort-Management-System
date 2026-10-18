<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AUDIT_ACTION = 'Reconciled client-confirmed paid amount and discount';

    /** @var array<int, array{room: string, request: string}> */
    private const BOOKINGS = [
        382 => ['room' => '201', 'request' => '38200000-0000-4000-8000-000000000382'],
        383 => ['room' => '202', 'request' => '38300000-0000-4000-8000-000000000383'],
    ];

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_payments', 'booking_room_shifts', 'users'] as $table) {
            if (!Schema::hasTable($table)) {
                Log::warning('Booking 382/383 bill correction skipped: a required table is missing.', ['table' => $table]);
                return;
            }
        }

        foreach (self::BOOKINGS as $bookingId => $details) {
            DB::transaction(fn () => $this->reconcileBooking($bookingId, $details['room'], $details['request']));
        }
    }

    private function reconcileBooking(int $bookingId, string $expectedRoom, string $requestId): void
    {
        $booking = Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts'])
            ->whereKey($bookingId)
            ->lockForUpdate()
            ->first();

        if (!$booking) {
            Log::warning("Booking {$bookingId} bill correction skipped: booking not found.");
            return;
        }

        $payments = $booking->payments()->lockForUpdate()->get();
        $booking->setRelation('payments', $payments);
        $assignment = $booking->bookingRooms->count() === 1 ? $booking->bookingRooms->first() : null;
        $payment = $payments->count() === 1 ? $payments->first() : null;
        $updatedAt = substr((string) $booking->getRawOriginal('updated_at'), 0, 10);
        $knownDiscount = $booking->discount_type === 'flat'
            && in_array((float) $booking->discount_amount, [0.0, 2500.0, 4000.0], true)
            && (float) $booking->discount_percentage === 0.0;
        $hasExpectedPayment = $payments->isEmpty()
            || ($payment
                && $payment->request_id === $requestId
                && $payment->type === 'payment'
                && $payment->method === 'cash'
                && (float) $payment->amount === 1500.0
                && $payment->payment_date?->toDateString() === '2026-10-18');

        $matchesBackup = substr((string) $booking->getRawOriginal('created_at'), 0, 10) === '2026-10-18'
            && $updatedAt === '2026-10-18'
            && $booking->status === 'checked_out'
            && $booking->check_in_date->toDateString() === '2026-10-17'
            && $booking->check_out_date->toDateString() === '2026-10-18'
            && (float) $booking->total_amount === 4000.0
            && (float) $booking->advance_payment === 0.0
            && $booking->payment_method === 'cash'
            && (float) $booking->extra_charges === 0.0
            && (float) $booking->food_package_cost === 0.0
            && (float) $booking->addons_cost === 0.0
            && (float) $booking->vat_amount === 0.0
            && !$booking->vat_enabled
            && !$booking->is_complimentary
            && $booking->booking_group_id === null
            && $booking->roomShifts->isEmpty()
            && $knownDiscount
            && in_array($booking->discount_status, ['pending', 'approved', null], true)
            && $booking->bookingRooms->count() === 1
            && $assignment
            && (string) $assignment->room?->room_number === $expectedRoom
            && $assignment->check_in_date?->toDateString() === '2026-10-17'
            && $assignment->check_out_date?->toDateString() === '2026-10-18'
            && (float) $assignment->price_per_night === 4000.0
            && $booking->getCalculatedTotal() === 4000.0
            && $hasExpectedPayment;

        if (!$matchesBackup) {
            Log::warning("Booking {$bookingId} bill correction skipped: live booking, room assignment, or payment ledger differs from the supplied backup.");
            return;
        }

        $operatorId = (int) ($booking->updated_by_id ?? 0);
        if ($operatorId <= 0 || !DB::table('users')->where('id', $operatorId)->exists()) {
            Log::warning("Booking {$bookingId} bill correction skipped: the last booking updater cannot be resolved for the payment audit.");
            return;
        }

        if ($payments->isEmpty()) {
            if (DB::table('booking_payments')->where('request_id', $requestId)->exists()) {
                Log::warning("Booking {$bookingId} bill correction skipped: the reconciliation payment request ID is already in use.");
                return;
            }

            $now = now('Asia/Dhaka');
            DB::table('booking_payments')->insert([
                'booking_id' => $bookingId,
                'amount' => 1500,
                'method' => 'cash',
                'type' => 'payment',
                'note' => "Client-confirmed BDT 1,500.00 payment reconciled for booking #{$bookingId} from the supplied 18 Oct 2026 database backup.",
                'recorded_by_id' => $operatorId,
                'request_id' => $requestId,
                'payment_date' => $updatedAt,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $before = $booking->getFinancialBreakdown();
        $discountReference = "Client-confirmed BDT 2,500 discount correction on 18 Oct 2026. Previous reference: ".(string) $booking->discount_reference;
        $booking->fill([
            'discount_type' => 'flat',
            'discount_amount' => 2500,
            'discount_percentage' => 0,
            'discount_status' => 'approved',
            'discount_reference' => substr($discountReference, 0, 255),
            'discount_requested_by' => null,
            'discount_approved_by' => null,
            'discount_approved_at' => now('Asia/Dhaka'),
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
            $booking->recordFinancialSnapshot('reconcile_booking_paid_amount_and_discount', '2026-10-18');
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
                    'reason' => 'Client confirmed BDT 1,500 paid and the remaining BDT 2,500 discounted against BDT 4,000 room rent.',
                    'payment_request_id' => $requestId,
                    'recorded_by_id' => $operatorId,
                    'previous_discount' => $before['discount'],
                    'corrected_discount' => $after['discount'],
                    'previous_deposited' => $before['deposited'],
                    'corrected_deposited' => $after['deposited'],
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
        // Preserve the client-confirmed payments, discounts, and audit history.
    }
};

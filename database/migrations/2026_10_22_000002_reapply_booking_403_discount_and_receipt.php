<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const AUDIT_ACTION = 'Reapplied booking 403 BDT 200 discount and receipt correction';

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_payments', 'booking_room_shifts'] as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException("Booking 403 correction cannot run: required table '{$table}' is missing.");
            }
        }

        DB::transaction(function () {
            $booking = Booking::with(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts'])
                ->whereKey(403)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                throw new RuntimeException('Booking 403 correction cannot run: booking #403 was not found.');
            }

            $payments = $booking->payments()->lockForUpdate()->get();
            $booking->setRelation('payments', $payments);
            $assignment = $booking->bookingRooms->count() === 1 ? $booking->bookingRooms->first() : null;
            $payment = $payments->count() === 1 ? $payments->first() : null;
            $discountIsExpected = ($booking->discount_type === 'none'
                    && (float) $booking->discount_amount === 0.0
                    && (float) $booking->discount_percentage === 0.0)
                || ($booking->discount_type === 'flat'
                    && (float) $booking->discount_amount === 200.0
                    && (float) $booking->discount_percentage === 0.0);

            $bookingMatches = $booking->customer_name === 'Saiful Islam'
                && $booking->customer_phone === '01963672304'
                && $booking->status === 'checked_out'
                && $booking->check_in_date?->toDateString() === '2026-10-21'
                && $booking->check_out_date?->toDateString() === '2026-10-22'
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
                && $discountIsExpected
                && $booking->bookingRooms->count() === 1
                && $assignment
                && (string) $assignment->room?->room_number === '404'
                && $assignment->check_in_date?->toDateString() === '2026-10-21'
                && $assignment->check_out_date?->toDateString() === '2026-10-22'
                && (float) $assignment->price_per_night === 2000.0
                && $booking->getCalculatedTotal() === 2000.0;

            $paymentMatches = $payment
                && $payment->type === 'payment'
                && $payment->method === 'cash'
                && in_array((float) $payment->amount, [1800.0, 2000.0], true)
                && $payment->payment_date?->toDateString() === '2026-10-22';

            if (!$bookingMatches || $payments->count() !== 1 || !$paymentMatches) {
                Log::error('Booking 403 correction stopped: live booking or receipt does not match the confirmed Room 404 bill.', [
                    'booking_id' => $booking->id,
                    'customer_name' => $booking->customer_name,
                    'customer_phone' => $booking->customer_phone,
                    'booking_status' => $booking->status,
                    'check_in' => $booking->check_in_date?->toDateString(),
                    'check_out' => $booking->check_out_date?->toDateString(),
                    'assigned_room' => $assignment?->room?->room_number,
                    'payment_count' => $payments->count(),
                    'payment_amount' => $payment?->amount,
                ]);
                throw new RuntimeException('Booking 403 correction stopped because live booking/payment data differs from the confirmed Room 404 bill. Check laravel.log; no correction was committed.');
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

            $booking->fill([
                'discount_type' => 'flat',
                'discount_amount' => 200,
                'discount_percentage' => 0,
                'discount_status' => 'approved',
                'discount_reference' => 'Client-confirmed BDT 200 room discount; receipt corrected to BDT 1,800 on 22 Oct 2026.',
                'discount_requested_by' => null,
                'discount_approved_by' => $booking->discount_status === 'approved' ? $booking->discount_approved_by : null,
                'discount_approved_at' => $booking->discount_status === 'approved' && $booking->discount_approved_at
                    ? $booking->discount_approved_at
                    : now('Asia/Dhaka'),
            ]);

            $after = $booking->getFinancialBreakdown();
            if ((float) $after['room_rent'] !== 2000.0 || (float) $after['discount'] !== 200.0
                || (float) $after['grand_total'] !== 1800.0 || (float) $after['deposited'] !== 1800.0
                || (float) $after['remaining'] !== 0.0) {
                $diagnostics = [
                    'financials' => $after,
                    'discount_type' => $booking->discount_type,
                    'discount_amount' => $booking->discount_amount,
                    'discount_percentage' => $booking->discount_percentage,
                    'payment_rows' => $booking->payments->map(fn ($row) => [
                        'id' => $row->id,
                        'amount' => $row->amount,
                        'type' => $row->type,
                    ])->values()->all(),
                    'room_breakdown' => $booking->getRoomBreakdown()->map(fn ($room) => [
                        'room_id' => $room['room_id'] ?? null,
                        'nights' => $room['nights'] ?? null,
                        'rate' => $room['price_per_night'] ?? null,
                        'amount' => $room['amount'] ?? null,
                    ])->all(),
                ];
                Log::error('Booking 403 correction calculation mismatch; transaction will roll back.', $diagnostics);
                throw new RuntimeException('Booking 403 correction calculation mismatch; no data was committed. Details: '.json_encode($diagnostics, JSON_THROW_ON_ERROR));
            }

            $booking->total_amount = $after['room_rent'];
            $booking->vat_amount = $after['vat'];
            $booking->remaining_payment = $after['remaining'];
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->saveQuietly();

            if (Schema::hasTable('booking_financial_snapshots')) {
                $booking->recordFinancialSnapshot('reapply_booking_403_discount_and_receipt', '2026-10-22');
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
                        'reason' => 'Client confirmed BDT 200 discount and BDT 1,800 actual cash paid.',
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

            Log::info('Booking 403 discount and receipt correction applied.', [
                'room_rent' => 2000,
                'discount' => 200,
                'deposited' => 1800,
                'remaining' => 0,
            ]);
        });
    }

    public function down(): void
    {
        // Preserve the client-confirmed bill and receipt correction.
    }
};

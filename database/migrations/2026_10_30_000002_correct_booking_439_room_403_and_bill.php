<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BOOKING_ID = 439;
    private const OLD_ROOM_NUMBER = '201';
    private const NEW_ROOM_NUMBER = '403';
    private const PAYMENT_ID = 413;
    private const PAYMENT_REQUEST_ID = '462c26c8-2c6f-4183-b441-98384535ccee';
    private const AUDIT_ACTION = 'Corrected booking 439 room assignment and bill';
    private const SNAPSHOT_REASON = 'correct_booking_439_room_403_and_bill';

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_payments', 'booking_room_shifts', 'rooms'] as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException("Booking 439 correction cannot run: required table '{$table}' is missing.");
            }
        }

        DB::transaction(function (): void {
            $booking = Booking::query()
                ->whereKey(self::BOOKING_ID)
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                throw new RuntimeException('Booking 439 correction cannot run: booking #439 was not found.');
            }

            $oldRooms = DB::table('rooms')->where('room_number', self::OLD_ROOM_NUMBER)->lockForUpdate()->get();
            $newRooms = DB::table('rooms')->where('room_number', self::NEW_ROOM_NUMBER)->lockForUpdate()->get();
            $oldRoom = $oldRooms->count() === 1 ? $oldRooms->first() : null;
            $newRoom = $newRooms->count() === 1 ? $newRooms->first() : null;
            $assignments = DB::table('booking_rooms')->where('booking_id', self::BOOKING_ID)->lockForUpdate()->get();
            $assignment = $assignments->count() === 1 ? $assignments->first() : null;
            $payments = $booking->payments()->lockForUpdate()->get();
            $payment = $payments->count() === 1 ? $payments->first() : null;
            $shifts = DB::table('booking_room_shifts')->where('booking_id', self::BOOKING_ID)->lockForUpdate()->count();

            $notes = (string) ($booking->notes ?? '');
            $notesMatch = str_contains($notes, '[Rooms: 201]') || str_contains($notes, '[Rooms: 403]');
            $bookingRoomId = (int) ($booking->room_id ?? 0);
            $assignmentRoomId = (int) ($assignment->room_id ?? 0);
            $oldRoomId = (int) ($oldRoom->id ?? 0);
            $newRoomId = (int) ($newRoom->id ?? 0);
            $knownRoomAssignment = in_array($bookingRoomId, [$oldRoomId, $newRoomId], true)
                && in_array($assignmentRoomId, [$oldRoomId, $newRoomId], true)
                && in_array(round((float) ($assignment->price_per_night ?? 0), 2), [3000.00, 4000.00], true);

            $bookingMatches = $booking->customer_name === 'Md. Rezaul Haque'
                && $booking->customer_phone === '01716191386'
                && $booking->check_in_date?->toDateString() === '2026-10-27'
                && $booking->check_out_date?->toDateString() === '2026-10-28'
                && $booking->status === 'checked_out'
                && $booking->booking_group_id === null
                && !$booking->is_complimentary
                && $booking->discount_type === 'flat'
                && round((float) $booking->discount_amount, 2) === 1000.00
                && round((float) $booking->discount_percentage, 2) === 0.00
                && round((float) $booking->advance_payment, 2) === 0.00
                && round((float) $booking->extra_charges, 2) === 0.00
                && round((float) $booking->food_package_cost, 2) === 0.00
                && round((float) $booking->addons_cost, 2) === 0.00
                && round((float) $booking->vat_amount, 2) === 0.00
                && in_array(round((float) $booking->total_amount, 2), [3000.00, 4000.00], true)
                && in_array(round((float) $booking->remaining_payment, 2), [0.00, 1000.00], true)
                && in_array($booking->payment_status, ['partial', 'paid'], true)
                && $booking->food_package_id === null
                && !$booking->vat_enabled
                && $booking->bookingRooms()->count() === 1
                && $assignment
                && (string) $assignment->check_in_date === '2026-10-27'
                && (string) $assignment->check_out_date === '2026-10-28'
                && $knownRoomAssignment
                && $notesMatch
                && $shifts === 0;

            $paymentMatches = $payments->count() === 1
                && $payment
                && (int) $payment->id === self::PAYMENT_ID
                && $payment->request_id === self::PAYMENT_REQUEST_ID
                && $payment->type === 'payment'
                && $payment->method === 'mfs'
                && round((float) $payment->amount, 2) === 2000.00
                && $payment->payment_date?->toDateString() === '2026-10-28';

            if (!$oldRoom || !$newRoom
                || (float) $newRoom->price_per_night !== 3000.00
                || !$bookingMatches || !$paymentMatches) {
                Log::error('Booking 439 room correction stopped: production booking, room, or payment differs from the verified database backup.', [
                    'booking_id' => self::BOOKING_ID,
                    'guest' => $booking->customer_name,
                    'phone' => $booking->customer_phone,
                    'assigned_room_id' => $bookingRoomId,
                    'room_row_id' => $assignmentRoomId,
                    'payment_count' => $payments->count(),
                    'shift_count' => $shifts,
                ]);
                throw new RuntimeException('Booking 439 correction stopped because live booking data differs from the verified backup. No correction was committed. Check laravel.log.');
            }

            $this->assertRoomAvailableForCorrectedDates($newRoomId);

            $before = $booking->getFinancialBreakdown();
            $now = now('Asia/Dhaka');
            $updatedAssignments = DB::table('booking_rooms')
                ->where('id', $assignment->id)
                ->where('booking_id', self::BOOKING_ID)
                ->update([
                    'room_id' => $newRoomId,
                    'price_per_night' => $newRoom->price_per_night,
                    'updated_at' => $now,
                ]);

            if ($updatedAssignments !== 1) {
                throw new RuntimeException('Booking 439 correction stopped because its room assignment changed during deployment. No correction was committed.');
            }

            $booking->room_id = $newRoomId;
            $booking->notes = str_replace('[Rooms: 201]', '[Rooms: 403]', $notes);
            $booking->updated_at = $now;
            $booking->saveQuietly();

            // Re-read persisted relationships so this calculation matches the
            // code path used by invoices and reports.
            $booking->unsetRelation('bookingRooms');
            $booking->unsetRelation('room');
            $booking->unsetRelation('roomShifts');
            $booking->unsetRelation('payments');
            $booking->load(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts']);
            $booking->setRelation('payments', $booking->payments()->get());

            $after = $booking->getFinancialBreakdown();
            if ($after['room_rent'] !== 3000.0
                || $after['discount'] !== 1000.0
                || $after['grand_total'] !== 2000.0
                || $after['deposited'] !== 2000.0
                || round((float) $after['remaining'], 2) !== 0.0
                || $booking->getCalculatedPaymentStatus() !== 'paid') {
                $diagnostics = [
                    'financials' => $after,
                    'payment_status' => $booking->getCalculatedPaymentStatus(),
                    'room_breakdown' => $booking->getRoomBreakdown()->map(fn (array $room): array => [
                        'room_id' => $room['room_id'] ?? null,
                        'room_number' => $room['room_number'] ?? $room['room']?->room_number,
                        'nights' => $room['nights'] ?? null,
                        'rate' => $room['price_per_night'] ?? null,
                        'amount' => $room['amount'] ?? null,
                    ])->all(),
                    'payments' => $booking->payments->map(fn ($row): array => [
                        'id' => $row->id,
                        'type' => $row->type,
                        'method' => $row->method,
                        'amount' => $row->amount,
                    ])->all(),
                ];
                Log::error('Booking 439 correction calculation mismatch; transaction will roll back.', $diagnostics);
                throw new RuntimeException('Booking 439 correction did not calculate the confirmed BDT 2,000 bill and zero balance. No correction was committed.');
            }

            $booking->total_amount = $after['room_rent'];
            $booking->vat_amount = $after['vat'];
            $booking->remaining_payment = $after['remaining'];
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->updated_at = $now;
            $booking->saveQuietly();

            $booking->refresh();
            $booking->load(['bookingRooms.room.roomType', 'room.roomType', 'roomShifts']);
            $booking->setRelation('payments', $booking->payments()->get());

            $this->ensureCorrectionSnapshot($booking, '2026-10-27', $now);
            $this->ensureCorrectionSnapshot($booking, '2026-10-28', $now);
            $this->writeAuditLog($before, $after, $payment->id, $oldRoom->id, $newRoom->id, $now);

            Log::info('Booking 439 room assignment and bill corrected from verified production backup.', [
                'room' => self::NEW_ROOM_NUMBER,
                'room_rent' => $after['room_rent'],
                'discount' => $after['discount'],
                'deposited' => $after['deposited'],
                'remaining' => $after['remaining'],
            ]);
        });
    }

    private function assertRoomAvailableForCorrectedDates(int $newRoomId): void
    {
        $normalizedConflict = DB::table('booking_rooms as br')
            ->join('bookings as b', 'b.id', '=', 'br.booking_id')
            ->where('br.room_id', $newRoomId)
            ->where('br.booking_id', '!=', self::BOOKING_ID)
            ->whereNotIn('b.status', ['cancelled'])
            ->whereDate('br.check_in_date', '<', '2026-10-28')
            ->whereDate('br.check_out_date', '>', '2026-10-27')
            ->exists();

        $legacyConflict = DB::table('bookings as b')
            ->where('b.room_id', $newRoomId)
            ->where('b.id', '!=', self::BOOKING_ID)
            ->whereNotIn('b.status', ['cancelled'])
            ->whereDate('b.check_in_date', '<', '2026-10-28')
            ->whereDate('b.check_out_date', '>', '2026-10-27')
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('booking_rooms as assigned_room')
                    ->whereColumn('assigned_room.booking_id', 'b.id');
            })
            ->exists();

        if ($normalizedConflict || $legacyConflict) {
            throw new RuntimeException('Booking 439 correction stopped: room 403 has another booking overlapping 27-28 Oct 2026. No correction was committed.');
        }
    }

    private function ensureCorrectionSnapshot(Booking $booking, string $effectiveDate, $now): void
    {
        if (!Schema::hasTable('booking_financial_snapshots')) {
            return;
        }

        $exists = DB::table('booking_financial_snapshots')
            ->where('booking_id', self::BOOKING_ID)
            ->whereDate('effective_date', $effectiveDate)
            ->where('reason', self::SNAPSHOT_REASON)
            ->where('room_rent', 3000)
            ->where('discount', 1000)
            ->where('grand_total', 2000)
            ->exists();

        if (!$exists) {
            $booking->recordFinancialSnapshot(self::SNAPSHOT_REASON, $effectiveDate);
        }
    }

    private function writeAuditLog(array $before, array $after, int $paymentId, int $oldRoomId, int $newRoomId, $now): void
    {
        if (!Schema::hasTable('activity_logs') || DB::table('activity_logs')
            ->where('entity_type', 'Booking')
            ->where('entity_id', self::BOOKING_ID)
            ->where('action', self::AUDIT_ACTION)
            ->exists()) {
            return;
        }

        DB::table('activity_logs')->insert([
            'user_id' => null,
            'action' => self::AUDIT_ACTION,
            'entity_type' => 'Booking',
            'entity_id' => self::BOOKING_ID,
            'changes' => json_encode([
                'reason' => 'Client confirmed that room 403 was assigned to booking #439 instead of room 201; rent recalculated at room 403 published rate.',
                'from_room_id' => $oldRoomId,
                'from_room_number' => self::OLD_ROOM_NUMBER,
                'to_room_id' => $newRoomId,
                'to_room_number' => self::NEW_ROOM_NUMBER,
                'payment_id' => $paymentId,
                'payment_ledger_preserved' => true,
                'before' => $before,
                'after' => $after,
            ], JSON_THROW_ON_ERROR),
            'ip_address' => null,
            'user_agent' => 'Automatic verified correction migration',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // Keep the verified room correction, recalculated bill, and audit trail.
    }
};

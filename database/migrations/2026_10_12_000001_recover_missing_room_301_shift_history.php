<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Recover the room 301 -> 204 segment for the single booking verified in the
 * supplied production dump. Every identifying date/rate/assignment is checked
 * before writing, so unrelated bookings are never inferred or rewritten.
 */
return new class extends Migration {
    private const BOOKING_ID = 359;

    public function up(): void
    {
        foreach (['bookings', 'booking_rooms', 'booking_room_shifts', 'booking_financial_snapshots', 'rooms'] as $table) {
            if (!Schema::hasTable($table)) {
                Log::warning('Room shift history recovery skipped: required table is missing.', ['table' => $table]);
                return;
            }
        }

        $booking = DB::table('bookings')->where('id', self::BOOKING_ID)->first();
        if (!$booking) {
            return;
        }

        $oldRoom = DB::table('rooms')->where('room_number', '301')->first();
        $newRoom = DB::table('rooms')->where('room_number', '204')->first();
        $assignments = DB::table('booking_rooms')->where('booking_id', self::BOOKING_ID)->get();
        $activeAssignment = $assignments->count() === 1 ? $assignments->first() : null;

        $matchesBooking = $booking->status === 'checked_in'
            && (string) $booking->check_in_date === '2026-10-11'
            && (string) $booking->check_out_date === '2026-10-15'
            && round((float) $booking->total_amount, 2) === 12000.00;
        $matchesActiveAssignment = $activeAssignment
            && (int) $activeAssignment->room_id === (int) ($newRoom->id ?? 0)
            && in_array((string) $activeAssignment->check_in_date, ['2026-10-11', '2026-10-12'], true)
            && (string) $activeAssignment->check_out_date === '2026-10-15'
            && round((float) $activeAssignment->price_per_night, 2) === 3000.00;
        $hasPreviousNightSnapshot = DB::table('booking_financial_snapshots')
            ->where('booking_id', self::BOOKING_ID)
            ->where('effective_date', '2026-10-11')
            ->where('check_in_date', '2026-10-11')
            ->where('check_out_date', '2026-10-12')
            ->where('room_rent', 3000)
            ->where('grand_total', 3000)
            ->exists();

        if (!$oldRoom || !$newRoom || $oldRoom->room_number !== '301' || $newRoom->room_number !== '204'
            || !$matchesBooking || !$matchesActiveAssignment || !$hasPreviousNightSnapshot) {
            Log::warning('Room shift history recovery skipped because booking data did not match the verified backup.', [
                'booking_id' => self::BOOKING_ID,
            ]);
            return;
        }

        $existingShifts = DB::table('booking_room_shifts')->where('booking_id', self::BOOKING_ID)->get();
        if ($existingShifts->isNotEmpty()) {
            $recoveredShiftExists = $existingShifts->contains(fn ($shift) =>
                (int) $shift->from_room_id === (int) $oldRoom->id
                && (int) $shift->to_room_id === (int) $newRoom->id
                && $shift->from_room_number === '301'
                && $shift->to_room_number === '204'
                && (string) $shift->occupied_from === '2026-10-11'
                && (string) $shift->shift_date === '2026-10-12'
                && round((float) $shift->billed_amount, 2) === 3000.00
            );

            if ($recoveredShiftExists) {
                if ((string) $activeAssignment->check_in_date === '2026-10-11') {
                    DB::table('booking_rooms')->where('id', $activeAssignment->id)
                        ->where('booking_id', self::BOOKING_ID)
                        ->update(['check_in_date' => '2026-10-12']);
                }
                $this->synchronizeCurrentRoomStatuses($booking, (int) $oldRoom->id, (int) $newRoom->id);
            }

            return;
        }

        $eventAt = $activeAssignment->created_at ?: now('Asia/Dhaka')->toDateTimeString();
        $shiftedById = $booking->updated_by_id ?? $booking->created_by_id;
        if ($shiftedById && Schema::hasTable('users') && !DB::table('users')->where('id', $shiftedById)->exists()) {
            $shiftedById = null;
        }

        // Insert the immutable historical segment first. If the later room-row
        // update is interrupted on a non-transactional host table, rerunning
        // this migration recognizes the segment and completes the row update.
        DB::table('booking_room_shifts')->insert([
            'booking_id' => self::BOOKING_ID,
            'from_room_id' => $oldRoom->id,
            'to_room_id' => $newRoom->id,
            'from_room_number' => '301',
            'to_room_number' => '204',
            'occupied_from' => '2026-10-11',
            'shift_date' => '2026-10-12',
            'previous_rate' => 3000,
            'new_rate' => 3000,
            'billed_nights' => 1,
            'billed_amount' => 3000,
            'reason' => 'Recovered from the verified financial snapshot and current room assignment.',
            'shifted_by_id' => $shiftedById,
            'created_at' => $eventAt,
            'updated_at' => $eventAt,
        ]);

        DB::table('booking_rooms')->where('id', $activeAssignment->id)
            ->where('booking_id', self::BOOKING_ID)
            ->where('room_id', $newRoom->id)
            ->update(['check_in_date' => '2026-10-12']);

        $this->synchronizeCurrentRoomStatuses($booking, (int) $oldRoom->id, (int) $newRoom->id);
    }

    private function synchronizeCurrentRoomStatuses(object $booking, int $oldRoomId, int $newRoomId): void
    {
        $today = Carbon::today('Asia/Dhaka')->toDateString();
        if (!in_array($booking->status, ['confirmed', 'checked_in'], true)
            || (string) $booking->check_in_date > $today
            || (string) $booking->check_out_date <= $today) {
            return;
        }

        DB::table('rooms')->where('id', $newRoomId)
            ->whereNotIn('status', ['maintenance', 'unavailable'])
            ->update(['status' => 'occupied']);

        $normalizedOccupantExists = DB::table('booking_rooms as br')
            ->join('bookings as b', 'b.id', '=', 'br.booking_id')
            ->where('br.room_id', $oldRoomId)
            ->where('b.id', '!=', self::BOOKING_ID)
            ->whereNotIn('b.status', ['cancelled', 'checked_out'])
            ->whereDate('b.check_in_date', '<=', $today)
            ->whereDate('b.check_out_date', '>', $today)
            ->exists();

        $legacyOccupantExists = DB::table('bookings as b')
            ->where('b.room_id', $oldRoomId)
            ->where('b.id', '!=', self::BOOKING_ID)
            ->whereNotIn('b.status', ['cancelled', 'checked_out'])
            ->whereDate('b.check_in_date', '<=', $today)
            ->whereDate('b.check_out_date', '>', $today)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('booking_rooms as br')->whereColumn('br.booking_id', 'b.id');
            })
            ->exists();

        if (!$normalizedOccupantExists && !$legacyOccupantExists) {
            DB::table('rooms')->where('id', $oldRoomId)
                ->whereNotIn('status', ['maintenance', 'unavailable'])
                ->update(['status' => 'available']);
        }
    }

    public function down(): void
    {
        // This is a verified production-data recovery; do not remove recovered
        // billing history on rollback.
    }
};

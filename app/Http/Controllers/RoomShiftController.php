<?php

namespace App\Http\Controllers;

use App\Models\{Booking, Room, ActivityLog};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomShiftController extends Controller
{
    private function ensureShiftable(Booking $booking): void
    {
        $today = today('Asia/Dhaka')->toDateString();
        if (!in_array($booking->status, ['confirmed', 'checked_in']) ||
            $booking->check_in_date->toDateString() > $today || $booking->check_out_date->toDateString() <= $today) {
            throw ValidationException::withMessages(['booking' => 'Room shift is available during an active stay, before the checkout day.']);
        }
    }

    private function hasConflict(Booking $booking, int $roomId): bool
    {
        $start = today('Asia/Dhaka');
        $end = $booking->check_out_date;
        return Booking::with('bookingRooms')->where('id', '!=', $booking->id)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->whereDate('check_in_date', '<', $end)->whereDate('check_out_date', '>', $start)
            ->get()->contains(function ($other) use ($roomId, $start, $end) {
                if ($other->bookingRooms->isEmpty()) return (int) $other->room_id === $roomId;
                return $other->bookingRooms->contains(fn ($assignment) => (int) $assignment->room_id === $roomId
                    && Carbon::parse($assignment->check_in_date ?? $other->check_in_date)->lt($end)
                    && Carbon::parse($assignment->check_out_date ?? $other->check_out_date)->gt($start));
            });
    }

    public function available(Booking $booking)
    {
        $this->ensureShiftable($booking);
        $rooms = Room::with('roomType')->whereNotIn('id', $booking->getAllRoomIds())
            ->whereNotIn('status', ['maintenance', 'unavailable'])->orderBy('room_number')->get()
            ->reject(fn ($room) => $this->hasConflict($booking, $room->id))->values();
        return response()->json(['rooms' => $rooms, 'from' => today('Asia/Dhaka')->toDateString(), 'until' => $booking->check_out_date->toDateString()]);
    }

    public function store(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'from_room_id' => 'required|integer|exists:rooms,id',
            'to_room_id' => 'required|integer|different:from_room_id|exists:rooms,id',
            'reason' => 'required|string|max:1000',
        ]);
        DB::transaction(function () use ($booking, $data) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $this->ensureShiftable($booking);
            $rooms = Room::with('roomType')->whereIn('id', [$data['from_room_id'], $data['to_room_id']])
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $booking->load('bookingRooms.room.roomType', 'payments');
            $old = $rooms->get($data['from_room_id']);
            $new = $rooms->get($data['to_room_id']);
            if (!in_array((int) $old->id, $booking->getAllRoomIds(), true) || in_array((int) $new->id, $booking->getAllRoomIds(), true)) {
                throw ValidationException::withMessages(['room' => 'The room assignment changed. Refresh the booking and select again.']);
            }
            if (in_array($new->status, ['maintenance', 'unavailable']) || $this->hasConflict($booking, $new->id)) {
                throw ValidationException::withMessages(['room' => 'The selected room is no longer available for the rest of this stay.']);
            }
            $line = $booking->getRoomBreakdown()->first(fn ($line) => $line['room_id'] === (int) $old->id && empty($line['shifted']));
            $today = today('Asia/Dhaka');
            $occupiedFrom = Carbon::parse($line['check_in_date']);
            $nights = max(0, (int) $occupiedFrom->diffInDays($today, false));
            $rate = (float) ($new->price_per_night ?? $new->roomType?->base_price ?? 0);
            $booking->roomShifts()->create([
                'from_room_id' => $old->id, 'to_room_id' => $new->id,
                'from_room_number' => $old->room_number, 'to_room_number' => $new->room_number,
                'occupied_from' => $occupiedFrom->toDateString(), 'shift_date' => $today->toDateString(),
                'previous_rate' => $line['price_per_night'], 'new_rate' => $rate,
                'billed_nights' => $nights, 'billed_amount' => round($nights * $line['price_per_night'], 2),
                'reason' => $data['reason'], 'shifted_by_id' => auth()->id(),
            ]);
            $booking->bookingRooms()->where('room_id', $old->id)->delete();
            $booking->bookingRooms()->create([
                'room_id' => $new->id, 'price_per_night' => $rate,
                'check_in_date' => $today->toDateString(), 'check_out_date' => $booking->check_out_date->toDateString(),
            ]);
            if ((int) $booking->room_id === (int) $old->id) $booking->room_id = $new->id;
            $booking->unsetRelation('bookingRooms')->unsetRelation('roomShifts');
            $booking->total_amount = $booking->getCalculatedTotal();
            $booking->remaining_payment = $booking->getCalculatedRemaining();
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->vat_amount = $booking->getVatAmount();
            $booking->updated_by_id = auth()->id();
            $booking->save();
            ActivityLog::log('Room shifted', 'Booking', $booking->id, $data);
        });
        return response()->json(['message' => 'Room shifted. Previous stay, bill and payment history have been retained.']);
    }
}

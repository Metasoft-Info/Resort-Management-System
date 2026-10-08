<?php

namespace App\Services;

use App\Models\{Booking, BookingGroup};
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class GroupRoomBookingService
{
    /** Allocate money in whole paisa, with no lost or duplicated remainder. */
    public static function allocate(float $amount, array $weights): array
    {
        $cents = (int) round($amount * 100);
        $total = array_sum($weights);
        $parts = [];
        $fractions = [];
        foreach ($weights as $key => $weight) {
            $exact = $total > 0 ? $cents * $weight / $total : $cents / count($weights);
            $parts[$key] = (int) floor($exact);
            $fractions[$key] = $exact - $parts[$key];
        }
        arsort($fractions);
        $remaining = $cents - array_sum($parts);
        foreach (array_keys($fractions) as $key) {
            if ($remaining-- <= 0) break;
            $parts[$key]++;
        }
        return array_map(fn ($part) => $part / 100, $parts);
    }

    /** Caller holds a transaction and the selected room locks. */
    public function create(array $data, array $rooms, array $guests, string $groupId, array $additionalGuests = [])
    {
        $nights = (new Booking($data))->getNights();
        $weights = array_map(fn ($room) => $room['pricePerNight'] * $nights, $rooms);
        if (($data['discount_type'] ?? 'none') === 'flat' && $data['discount_amount'] > array_sum($weights)) {
            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed the room rent.']);
        }
        $discounts = self::allocate((float) ($data['discount_amount'] ?? 0), $weights);
        $extras = self::allocate((float) ($data['extra_charges'] ?? 0), $weights);
        $group = BookingGroup::create(['id' => $groupId, 'created_by_id' => auth()->id()]);
        $bookings = collect();

        foreach ($rooms as $index => $room) {
            $guest = $guests[$room['roomId']] ?? [];
            $child = Arr::except($data, ['room_guests', 'billing_mode', 'group_request_id', 'additional_guests']);
            foreach (['customer_name', 'customer_phone', 'customer_nid', 'customer_email', 'customer_address', 'company_name'] as $field) {
                if (!empty($guest[$field])) $child[$field] = $guest[$field];
            }
            // Identity documents belong to the main guest, not a different room's guest.
            if (($child['customer_name'] ?? '') !== $data['customer_name'] || ($child['customer_phone'] ?? '') !== $data['customer_phone']) {
                foreach (['customer_photo', 'customer_nid_document', 'passport_document', 'visiting_card', 'passport_number', 'customer_nid'] as $field) {
                    unset($child[$field]);
                }
                $child['customer_nid'] = $guest['customer_nid'] ?? null;
            }
            $child = array_merge($child, [
                'booking_group_id' => $group->id,
                'room_id' => $room['roomId'],
                'number_of_guests' => $guest['number_of_guests'] ?? 1,
                'total_amount' => $weights[$index],
                'discount_amount' => $discounts[$index],
                'extra_charges' => $extras[$index],
                'extra_charges_data' => null,
                'advance_payment' => 0,
                'notes' => trim(preg_replace('/\s*\[Rooms:.*?\]/', '', $data['notes'] ?? '')) . ' [Room: ' . $room['roomNumber'] . ']',
            ]);
            $booking = Booking::create($child);
            $booking->bookingRooms()->create([
                'room_id' => $room['roomId'], 'price_per_night' => $room['pricePerNight'],
                'check_in_date' => $data['check_in_date'], 'check_out_date' => $data['check_out_date'],
            ]);
            $booking->unsetRelation('bookingRooms');
            $bookings->push($booking);
        }
        $totals = $bookings->map(fn ($booking) => $booking->getGrandTotal())->all();
        if (round((float) $data['advance_payment'], 2) > round(array_sum($totals), 2)) {
            throw ValidationException::withMessages(['advance_payment' => 'Advance cannot exceed the total of the room bills.']);
        }
        $advances = self::allocate((float) $data['advance_payment'], $totals);
        foreach ($bookings as $index => $booking) {
            $booking->advance_payment = $advances[$index];
            if ($advances[$index] > 0) {
                $booking->payments()->create([
                    'amount' => $advances[$index], 'method' => $data['payment_method'], 'type' => 'advance',
                    'note' => 'Group booking advance allocated to this room', 'recorded_by_id' => auth()->id(),
                ]);
            }
            if ($index === 0) {
                foreach ($additionalGuests as $guest) $booking->additionalGuests()->create($guest);
            }
            $booking->unsetRelation('payments');
            $booking->vat_amount = $booking->getVatAmount();
            $booking->remaining_payment = $booking->getCalculatedRemaining();
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->save();
        }
        return $bookings;
    }
}

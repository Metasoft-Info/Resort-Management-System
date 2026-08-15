<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'room_id', 'customer_name', 'customer_nid', 'customer_photo', 'customer_nid_document',
        'passport_number', 'passport_document', 'visiting_card', 'customer_phone', 'reference_name',
        'reference_phone', 'customer_whatsapp', 'customer_email', 'customer_address', 'company_name',
        'booking_purpose', 'check_in_date', 'check_in_time', 'check_out_date', 'check_out_time', 'number_of_guests', 
        'total_amount', 'advance_payment', 'remaining_payment', 'payment_method', 'payment_status', 
        'status', 'extra_charges', 'extra_charges_description', 'extra_charges_data',
        'discount_amount', 'discount_percentage', 'discount_type', 'discount_reference',
        'discount_status', 'discount_requested_by', 'discount_approved_by', 'discount_approved_at',
        'food_package_id', 'food_package_guests', 'food_package_cost', 'selected_addons', 'addons_cost', 
        'extras', 'additional_guests', 'notes', 'ac_preference', 'vat_enabled', 'vat_amount', 
        'bkash_number', 'bank_name', 'created_by_id', 'updated_by_id',
    ];

    /**
     * Boot the model - handle cascade deletes
     */
    protected static function boot()
    {
        parent::boot();

        // When a booking is deleted, also delete its booking_rooms, payments, and additional_guests
        static::deleting(function ($booking) {
            $booking->bookingRooms()->delete();
            $booking->payments()->delete();
            $booking->additionalGuests()->delete();
            \Log::info('Cascade deleted related records for booking', ['booking_id' => $booking->id]);
        });
    }

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'selected_addons' => 'array',
            'extras' => 'array',
            'additional_guests' => 'array',
            'extra_charges_data' => 'array',
            'vat_enabled' => 'boolean',
            'customer_photo' => 'array',
            'customer_nid_document' => 'array',
            'passport_document' => 'array',
            'visiting_card' => 'array',
        ];
    }

    /**
     * Get documents as array (handles old string data and new array data)
     */
    public function getDocuments(string $field): array
    {
        $rawValue = $this->getAttributes()[$field] ?? null;

        if (is_string($rawValue) && !empty($rawValue)) {
            $decoded = json_decode($rawValue, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }
            // Handle double-encoded JSON (string that is itself a JSON string)
            if (is_string($decoded)) {
                $innerDecoded = json_decode($decoded, true);
                if (is_array($innerDecoded)) {
                    return array_values(array_filter($innerDecoded));
                }
                // Single path stored as JSON string
                return [$decoded];
            }
            // Old single-path string (not valid JSON)
            return [$rawValue];
        }

        if (is_array($rawValue)) {
            return array_values(array_filter($rawValue));
        }

        return [];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Multiple rooms relationship (new)
    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    // Default hotel check-in/check-out time
    const DEFAULT_CHECK_TIME = '12:00';

    public function getCheckInDateTime()
    {
        $date = $this->check_in_date ? \Carbon\Carbon::parse($this->check_in_date) : null;
        if (!$date) return null;
        $time = $this->check_in_time ?: self::DEFAULT_CHECK_TIME;
        return $date->copy()->setTimeFromTimeString($time);
    }

    public function getCheckOutDateTime()
    {
        $date = $this->check_out_date ? \Carbon\Carbon::parse($this->check_out_date) : null;
        if (!$date) return null;
        $time = $this->check_out_time ?: self::DEFAULT_CHECK_TIME;
        return $date->copy()->setTimeFromTimeString($time);
    }

    // Booking is confirmed and check-in time has passed
    public function shouldBeCheckedIn($at = null)
    {
        if ($this->status !== 'confirmed') return false;
        $at = $at ?: \Carbon\Carbon::now('Asia/Dhaka');
        $checkIn = $this->getCheckInDateTime();
        return $checkIn && $at->gte($checkIn);
    }

    // Booking is checked-in and check-out time has passed
    public function shouldBeCheckedOut($at = null)
    {
        if ($this->status !== 'checked_in') return false;
        $at = $at ?: \Carbon\Carbon::now('Asia/Dhaka');
        $checkOut = $this->getCheckOutDateTime();
        return $checkOut && $at->gte($checkOut);
    }

    // Booking is currently active (checked in, not checked out) regardless of time
    public function isCurrentlyStaying()
    {
        return $this->status === 'checked_in';
    }

    // Booking occupies a room at a given time
    public function isOccupyingAt($at = null)
    {
        $at = $at ?: \Carbon\Carbon::now('Asia/Dhaka');
        $checkIn = $this->getCheckInDateTime();
        $checkOut = $this->getCheckOutDateTime();
        if (!$checkIn || !$checkOut) return false;

        return in_array($this->status, ['confirmed', 'checked_in'])
            && $at->gte($checkIn)
            && $at->lt($checkOut);
    }

    // Get all room IDs for this booking. booking_rooms is authoritative when
    // present; room_id is only a fallback for legacy-only bookings.
    public function getAllRoomIds()
    {
        $bookingRoomIds = $this->bookingRooms->pluck('room_id')
            ->filter()
            ->map(fn ($roomId) => (int) $roomId)
            ->unique()
            ->values();

        // booking_rooms is authoritative whenever it has rows. The legacy
        // room_id column may contain a stale assignment left by older flows.
        if ($bookingRoomIds->isNotEmpty()) {
            return $bookingRoomIds->all();
        }

        return $this->room_id ? [(int) $this->room_id] : [];
    }

    // Get all rooms for this booking (handles both legacy single room and new multi-room)
    public function getAllRooms()
    {
        $bookingRooms = $this->relationLoaded('bookingRooms')
            ? $this->bookingRooms
            : $this->bookingRooms()->with('room.roomType')->get();
        $bookingRooms->loadMissing('room.roomType');
        $rooms = collect();
        $seenRoomIds = [];

        foreach ($bookingRooms as $bookingRoom) {
            if (!$bookingRoom->room || in_array((int) $bookingRoom->room_id, $seenRoomIds, true)) {
                continue;
            }

            $room = $bookingRoom->room;
            $room->booking_price = $bookingRoom->price_per_night;
            $rooms->push($room);
            $seenRoomIds[] = (int) $bookingRoom->room_id;
        }

        // Legacy-only bookings still use the old room_id column. Do not merge
        // it into a booking that already has booking_rooms rows: that column
        // can be stale and would make the visible rooms and total disagree.
        if ($rooms->isEmpty() && $this->room_id) {
            $legacyRoom = $this->relationLoaded('room')
                ? $this->room
                : $this->room()->with('roomType')->first();

            if ($legacyRoom) {
                $legacyRoom->booking_price = $legacyRoom->price_per_night
                    ?? $legacyRoom->roomType?->base_price
                    ?? 0;
                $rooms->push($legacyRoom);
            }
        }

        return $rooms;
    }

    /**
     * Calculate billable nights using the hotel's checkout-exclusive rule.
     *
     * A stay from 14 Aug to 17 Aug is three nights (14, 15 and 16). The
     * checkout date is not another night unless it is moved to 18 Aug.
     */
    public function getNights($checkIn = null, $checkOut = null): int
    {
        $checkIn = $checkIn ?: $this->check_in_date;
        $checkOut = $checkOut ?: $this->check_out_date;

        if (!$checkIn || !$checkOut) {
            return 1;
        }

        $start = \Carbon\Carbon::parse($checkIn);
        $end = \Carbon\Carbon::parse($checkOut);

        return max(1, (int) $start->diffInDays($end));
    }

    /**
     * Return the canonical room-level billing breakdown.
     *
     * booking_rooms is authoritative for new bookings. Legacy bookings keep
     * their stored total so changing today's room rate cannot rewrite an old
     * customer's agreed price.
     */
    public function getRoomBreakdown()
    {
        $bookingRooms = $this->relationLoaded('bookingRooms')
            ? $this->bookingRooms
            : $this->bookingRooms()->with('room.roomType')->get();

        $breakdown = collect();
        $seenRoomIds = [];

        foreach ($bookingRooms as $bookingRoom) {
            $roomId = (int) ($bookingRoom->room_id ?? 0);
            if (!$roomId || in_array($roomId, $seenRoomIds, true)) {
                continue;
            }

            $seenRoomIds[] = $roomId;
            $roomCheckIn = $bookingRoom->check_in_date ?? $this->check_in_date;
            $roomCheckOut = $bookingRoom->check_out_date ?? $this->check_out_date;
            $nights = $this->getNights($roomCheckIn, $roomCheckOut);

            $storedRate = $bookingRoom->price_per_night;
            $room = method_exists($bookingRoom, 'relationLoaded') && $bookingRoom->relationLoaded('room')
                ? $bookingRoom->getRelation('room')
                : null;
            $rate = $storedRate !== null
                ? (float) $storedRate
                : (float) ($room?->price_per_night ?? $room?->roomType?->base_price ?? 0);

            $breakdown->push([
                'booking_room_id' => $bookingRoom->id ?? null,
                'room_id' => $roomId,
                'room' => $room,
                'check_in_date' => $roomCheckIn,
                'check_out_date' => $roomCheckOut,
                'nights' => $nights,
                'price_per_night' => $rate,
                'amount' => round($rate * $nights, 2),
            ]);
        }

        // Legacy single-room bookings do not have a normalized room row.
        if ($breakdown->isEmpty() && $this->room_id) {
            $nights = $this->getNights();
            $storedTotal = (float) ($this->total_amount ?? 0);
            $room = $this->relationLoaded('room')
                ? $this->getRelation('room')
                : ($this->exists ? $this->room()->with('roomType')->first() : null);
            $rate = $storedTotal > 0
                ? $storedTotal / $nights
                : (float) ($room?->price_per_night ?? $room?->roomType?->base_price ?? 0);

            $breakdown->push([
                'booking_room_id' => null,
                'room_id' => (int) $this->room_id,
                'room' => $room,
                'check_in_date' => $this->check_in_date,
                'check_out_date' => $this->check_out_date,
                'nights' => $nights,
                'price_per_night' => $rate,
                'amount' => $storedTotal > 0 ? round($storedTotal, 2) : round($rate * $nights, 2),
            ]);
        }

        return $breakdown;
    }

    // Calculate actual room rent from the canonical room breakdown.
    public function getCalculatedTotal(): float
    {
        $breakdown = $this->getRoomBreakdown();

        if ($breakdown->isNotEmpty()) {
            return round((float) $breakdown->sum('amount'), 2);
        }

        return round((float) ($this->total_amount ?? 0), 2);
    }

    public function getDiscountAmount(): float
    {
        $baseAmount = $this->getCalculatedTotal();
        $discountType = $this->discount_type ?? 'none';

        if ($discountType === 'percentage') {
            $percentage = min(100, max(0, (float) ($this->discount_percentage ?? 0)));
            return round(min($baseAmount, $baseAmount * $percentage / 100), 2);
        }

        if ($discountType === 'flat') {
            return round(min($baseAmount, max(0, (float) ($this->discount_amount ?? 0))), 2);
        }

        return 0.0;
    }

    public function getVatAmount(): float
    {
        $afterDiscount = max(0, $this->getCalculatedTotal() - $this->getDiscountAmount());

        // VAT is calculated on the amount after discount everywhere.
        return $this->vat_enabled ? round($afterDiscount * 0.15, 2) : 0.0;
    }

    // Get total deposited amount (advance + all payment history)
    public function getTotalDeposited()
    {
        $payments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();

        $advanceRecord = $payments->first(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceRecordAmount = $advanceRecord ? (float) $advanceRecord->amount : 0;
        $advanceInDb = (float) ($this->advance_payment ?? 0);

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') !== 'advance' && ($p->type ?? 'payment') !== 'refund')
            ->sum('amount');
        $refunds = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'refund')
            ->sum('amount');

        $deposited = $advanceRecord && $advanceRecordAmount == $advanceInDb
            ? $advanceRecordAmount + (float) $extraPayments
            : $advanceInDb + (float) $extraPayments;

        // If advance payment record exists but doesn't match bookings.advance_payment,
        // there's a data inconsistency (e.g., booking was edited but payment record wasn't updated).
        // Trust bookings.advance_payment as ground truth and add extra payments.
        return max(0, round($deposited - (float) $refunds, 2));
    }

    // Get calculated remaining payment
    public function getCalculatedRemaining()
    {
        $grandTotal = $this->getGrandTotal();
        return round($grandTotal - $this->getTotalDeposited(), 2);
    }

    // Get dynamically calculated payment status based on actual payments
    public function getCalculatedPaymentStatus()
    {
        $remaining = $this->getCalculatedRemaining();
        $deposited = $this->getTotalDeposited();

        if ($remaining <= 0) {
            return 'paid';
        }
        if ($deposited > 0) {
            return 'partial';
        }
        return 'pending';
    }

    // Get grand total (Room Rent + Extra - Discount + VAT)
    public function getGrandTotal()
    {
        $baseAmount = $this->getCalculatedTotal();
        $afterDiscount = max(0, $baseAmount - $this->getDiscountAmount());
        $extraCharges = max(0, (float) ($this->extra_charges ?? 0));

        return round($afterDiscount + $extraCharges + $this->getVatAmount(), 2);
    }

    // Check if booking has multiple rooms
    public function hasMultipleRooms()
    {
        return $this->bookingRooms()->count() > 1;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function discountRequestedBy()
    {
        return $this->belongsTo(User::class, 'discount_requested_by');
    }

    public function discountApprovedBy()
    {
        return $this->belongsTo(User::class, 'discount_approved_by');
    }

    public function additionalGuests()
    {
        return $this->hasMany(AdditionalGuest::class);
    }

    public function foodPackage()
    {
        return $this->belongsTo(FoodPackage::class, 'food_package_id');
    }

    public function payments()
    {
        return $this->hasMany(BookingPayment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the status of this booking as of a specific date.
     * If the booking was checked_out after the filter date, show checked_in.
     * If the booking was checked_in after the filter date, show confirmed/pending.
     */
    public function getStatusAsOfDate($date)
    {
        $currentStatus = $this->status;

        // If cancelled, check if it was cancelled before or after the filter date
        if ($currentStatus === 'cancelled') {
            return 'cancelled';
        }

        // If currently checked_out and check_out_date is after the filter date,
        // the booking was still checked_in at that point
        $checkOutDate = \Carbon\Carbon::parse($this->check_out_date);
        $filterDate = \Carbon\Carbon::parse($date);

        if ($currentStatus === 'checked_out' && $checkOutDate->gt($filterDate)) {
            // Check if check-in date is also after filter date
            $checkInDate = \Carbon\Carbon::parse($this->check_in_date);
            if ($checkInDate->gt($filterDate)) {
                // Booking hadn't even started yet
                return $this->status === 'confirmed' ? 'confirmed' : 'pending';
            }
            return 'checked_in';
        }

        // If currently checked_in and check_in_date is after filter date
        if ($currentStatus === 'checked_in') {
            $checkInDate = \Carbon\Carbon::parse($this->check_in_date);
            if ($checkInDate->gt($filterDate)) {
                return $this->status === 'confirmed' ? 'confirmed' : 'pending';
            }
        }

        return $currentStatus;
    }

    /**
     * Get total deposited amount up to a specific date.
     * Only counts payments made on or before the given date.
     */
    public function getTotalDepositedUpToDate($date)
    {
        $payments = $this->payments()
            ->whereDate('created_at', '<=', $date)
            ->get();

        $advanceRecord = $payments->first(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceRecordAmount = $advanceRecord ? (float) $advanceRecord->amount : 0;
        $advanceInDb = (float) ($this->advance_payment ?? 0);

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') !== 'advance' && ($p->type ?? 'payment') !== 'refund')
            ->sum('amount');
        $refunds = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'refund')
            ->sum('amount');

        $deposited = null;

        if ($advanceRecord && $advanceRecordAmount != $advanceInDb) {
            $deposited = $advanceInDb + (float) $extraPayments;
        } elseif ($advanceRecord) {
            $deposited = $advanceRecordAmount + (float) $extraPayments;
        }

        // If no advance record in booking_payments but advance_payment is set,
        // check if the booking was created before the filter date
        $bookingDate = \Carbon\Carbon::parse($this->created_at);
        $filterDate = \Carbon\Carbon::parse($date);

        if ($bookingDate->lte($filterDate)) {
            $deposited = $advanceInDb + (float) $extraPayments;
        } elseif ($deposited === null) {
            $deposited = (float) $extraPayments;
        }

        return max(0, round($deposited - (float) $refunds, 2));
    }

    /**
     * Get calculated remaining payment as of a specific date.
     */
    public function getCalculatedRemainingUpToDate($date)
    {
        $grandTotal = $this->getGrandTotal();
        return $grandTotal - $this->getTotalDepositedUpToDate($date);
    }

    /**
     * Get advance payment amount within a date range.
     * Only counts advance payments made between start and end date (inclusive).
     */
    public function getAdvanceDepositedInRange($startDate, $endDate)
    {
        $advanceRecord = $this->payments()
            ->where('type', 'advance')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->first();

        if ($advanceRecord) {
            return (float) $advanceRecord->amount;
        }

        // Legacy/no advance record: include stored advance only if booking was created within range
        $bookingDate = \Carbon\Carbon::parse($this->created_at)->startOfDay();
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        if ($bookingDate->between($start, $end)) {
            return (float) ($this->advance_payment ?? 0);
        }

        return 0;
    }

    /**
     * Get total deposited amount within a date range.
     * Only counts payments made between start and end date (inclusive).
     */
    public function getTotalDepositedInRange($startDate, $endDate)
    {
        $payments = $this->payments()
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $advanceRecord = $payments->first(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceRecordAmount = $advanceRecord ? (float) $advanceRecord->amount : 0;
        $advanceInDb = (float) ($this->advance_payment ?? 0);

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') !== 'advance' && ($p->type ?? 'payment') !== 'refund')
            ->sum('amount');
        $refunds = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'refund')
            ->sum('amount');

        $hasAdvanceInRange = $advanceRecord !== null;

        if (!$hasAdvanceInRange) {
            // Legacy/no advance record: include stored advance only if booking was created within range
            $bookingDate = \Carbon\Carbon::parse($this->created_at)->startOfDay();
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
            $hasAdvanceInRange = $bookingDate->between($start, $end);
        }

        if (!$hasAdvanceInRange) {
            return max(0, round((float) $extraPayments - (float) $refunds, 2));
        }

        if ($advanceRecord && $advanceRecordAmount != $advanceInDb) {
            return max(0, round($advanceInDb + (float) $extraPayments - (float) $refunds, 2));
        }

        if ($advanceRecord) {
            return max(0, round($advanceRecordAmount + (float) $extraPayments - (float) $refunds, 2));
        }

        return max(0, round($advanceInDb + (float) $extraPayments - (float) $refunds, 2));
    }
}

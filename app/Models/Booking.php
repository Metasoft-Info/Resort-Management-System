<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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

        // Keep a dated bill snapshot whenever a booking's financial inputs or
        // stay dates are saved. Reports can then reproduce the bill as it was
        // on the selected date instead of applying today's discount/extension
        // to yesterday's activity.
        static::saved(function ($booking) {
            $booking->recordFinancialSnapshot('booking_saved');
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
            $room->booking_price = $room->price_per_night
                ?? $room->roomType?->base_price
                ?? $bookingRoom->price_per_night
                ?? 0;
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
     * booking_rooms is authoritative for room assignment, while the current
     * published room rate is authoritative for the resort's bill. Legacy
     * rows fall back to their stored snapshot only when no current room rate
     * can be resolved.
     */
    public function getRoomBreakdown()
    {
        $bookingRooms = $this->relationLoaded('bookingRooms')
            ? $this->bookingRooms
            : $this->bookingRooms()->with('room.roomType')->get();

        // Some callers eager-load only bookingRooms. Load the current room
        // price as well so a stale historical snapshot cannot undercharge.
        // Avoid touching the database when a caller already supplied a room
        // with a usable published price (important for non-database callers).
        $needsRoomDetails = $bookingRooms->contains(function ($bookingRoom) {
            if (!method_exists($bookingRoom, 'relationLoaded')) {
                return true;
            }

            if (!$bookingRoom->relationLoaded('room')) {
                return true;
            }

            $room = $bookingRoom->getRelation('room');
            if (!$room) {
                return false;
            }

            $publishedRate = $room->price_per_night ?? null;
            if ($publishedRate !== null && (float) $publishedRate > 0) {
                return false;
            }

            return !method_exists($room, 'relationLoaded') || !$room->relationLoaded('roomType');
        });

        if ($needsRoomDetails && method_exists($bookingRooms, 'loadMissing')) {
            $bookingRooms->loadMissing('room.roomType');
        }

        $breakdown = collect();
        $seenRoomIds = [];

        foreach ($bookingRooms as $bookingRoom) {
            $roomId = (int) ($bookingRoom->room_id ?? 0);
            if (!$roomId || in_array($roomId, $seenRoomIds, true)) {
                continue;
            }

            $seenRoomIds[] = $roomId;
            $roomCheckIn = $bookingRoom->check_in_date ?? $this->check_in_date;
            // A booking has one shared checkout. Older room rows can retain a
            // stale checkout date after the parent booking was extended; use
            // the parent checkout so the visible nights and bill cannot
            // disagree.
            $roomCheckOut = $this->check_out_date ?? $bookingRoom->check_out_date;
            $nights = $this->getNights($roomCheckIn, $roomCheckOut);

            $room = method_exists($bookingRoom, 'relationLoaded') && $bookingRoom->relationLoaded('room')
                ? $bookingRoom->getRelation('room')
                : null;
            $currentPublishedRate = $room?->price_per_night ?? $room?->roomType?->base_price;
            $snapshotRate = $bookingRoom->price_per_night;
            $rate = $currentPublishedRate !== null && (float) $currentPublishedRate > 0
                ? (float) $currentPublishedRate
                : (float) ($snapshotRate ?? 0);

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
            $currentPublishedRate = $room?->price_per_night ?? $room?->roomType?->base_price;
            $rate = $currentPublishedRate !== null && (float) $currentPublishedRate > 0
                ? (float) $currentPublishedRate
                : ($storedTotal > 0 ? $storedTotal / $nights : 0);

            $breakdown->push([
                'booking_room_id' => null,
                'room_id' => (int) $this->room_id,
                'room' => $room,
                'check_in_date' => $this->check_in_date,
                'check_out_date' => $this->check_out_date,
                'nights' => $nights,
                'price_per_night' => $rate,
                'amount' => round($rate * $nights, 2),
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

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'payment')
            ->sum('amount');
        $refunds = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'refund')
            ->sum('amount');

        return max(0, round(
            $this->getAdvancePaymentTotal() + (float) $extraPayments - (float) $refunds,
            2
        ));
    }

    // Get calculated remaining payment
    public function getCalculatedRemaining()
    {
        $grandTotal = $this->getGrandTotal();
        return max(0, round($grandTotal - $this->getTotalDeposited(), 2));
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

    /**
     * Return the initial advance amount without double-counting the optional
     * advance payment history row. The booking column is the current opening
     * balance; the history row is used as a fallback for legacy records where
     * that column was not populated.
     */
    public function getAdvancePaymentTotal(): float
    {
        $payments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();

        $recordedAdvance = (float) $payments
            ->filter(fn ($payment) => ($payment->type ?? 'payment') === 'advance')
            ->sum('amount');
        $storedAdvance = max(0, (float) ($this->advance_payment ?? 0));

        return round($storedAdvance > 0 ? $storedAdvance : $recordedAdvance, 2);
    }

    /**
     * Read timestamps without forcing Eloquent to resolve a database
     * connection. This also keeps the calculation helpers safe for imported
     * legacy rows and isolated unit tests.
     */
    private function rawCreatedAt($model): mixed
    {
        if (method_exists($model, 'getRawOriginal')) {
            return $model->getRawOriginal('created_at');
        }

        return $model->created_at ?? null;
    }

    /**
     * Read the cashier's explicit business date and fall back to the legacy
     * timestamp for old rows and isolated tests.
     */
    private function paymentBusinessDate($payment): ?string
    {
        if (method_exists($payment, 'getRawOriginal')) {
            $paymentDate = $payment->getRawOriginal('payment_date');
            if ($paymentDate) {
                return \Carbon\Carbon::parse($paymentDate)->toDateString();
            }

            $createdAt = $payment->getRawOriginal('created_at');
        } else {
            $paymentDate = $payment->payment_date ?? null;
            $createdAt = $payment->created_at ?? null;
        }

        return $createdAt
            ? \Carbon\Carbon::parse($createdAt)->toDateString()
            : null;
    }

    /**
     * Return the single financial source used by room reports and invoices.
     * Every displayed amount is derived from this same snapshot so summary,
     * row and remaining-balance values cannot drift apart.
     */
    public function getFinancialBreakdown(): array
    {
        $roomRent = $this->getCalculatedTotal();
        $discount = $this->getDiscountAmount();
        $extraCharges = max(0, round((float) ($this->extra_charges ?? 0), 2));
        $afterDiscount = max(0, $roomRent - $discount);
        $vat = $this->getVatAmount();
        $grandTotal = round($afterDiscount + $extraCharges + $vat, 2);
        $deposited = $this->getTotalDeposited();

        return [
            'room_rent' => $roomRent,
            'discount' => $discount,
            'extra_charges' => $extraCharges,
            'vat' => $vat,
            'grand_total' => $grandTotal,
            'advance' => $this->getAdvancePaymentTotal(),
            'deposited' => $deposited,
            'remaining' => max(0, round($grandTotal - $deposited, 2)),
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
        ];
    }

    /**
     * Return the bill that was effective at the end of a report date.
     * Deposits are deliberately not read from the snapshot; they come from
     * the payment ledger and are calculated separately below.
     */
    public function getFinancialBreakdownAt(?string $date = null): array
    {
        if (!$date || !$this->exists || !Schema::hasTable('booking_financial_snapshots')) {
            return $this->getFinancialBreakdown();
        }

        if ($this->relationLoaded('financialSnapshots')) {
            $snapshot = $this->financialSnapshots
                ->filter(function ($item) use ($date) {
                    $effectiveDate = $item->effective_date instanceof \Carbon\CarbonInterface
                        ? $item->effective_date->toDateString()
                        : (string) $item->effective_date;

                    return $effectiveDate <= $date;
                })
                ->sortByDesc(function ($item) {
                    $dateKey = $item->effective_date instanceof \Carbon\CarbonInterface
                        ? $item->effective_date->toDateString()
                        : (string) $item->effective_date;

                    return $dateKey . '|' . str_pad((string) $item->id, 20, '0', STR_PAD_LEFT);
                })
                ->first();
        } else {
            $snapshot = $this->financialSnapshots()
                ->whereDate('effective_date', '<=', $date)
                ->orderByDesc('effective_date')
                ->orderByDesc('id')
                ->first();
        }

        if (!$snapshot) {
            return $this->getFinancialBreakdown();
        }

        $deposited = $this->getTotalDeposited();

        return [
            'room_rent' => round((float) $snapshot->room_rent, 2),
            'discount' => round((float) $snapshot->discount, 2),
            'extra_charges' => round((float) $snapshot->extra_charges, 2),
            'vat' => round((float) $snapshot->vat, 2),
            'grand_total' => round((float) $snapshot->grand_total, 2),
            'advance' => $this->getAdvancePaymentTotal(),
            'deposited' => $deposited,
            'remaining' => max(0, round((float) $snapshot->grand_total - $deposited, 2)),
            'check_in_date' => $snapshot->check_in_date?->toDateString(),
            'check_out_date' => $snapshot->check_out_date?->toDateString(),
        ];
    }

    public function getNightsAt(?string $date = null): int
    {
        $financials = $this->getFinancialBreakdownAt($date);

        return $this->getNights(
            $financials['check_in_date'] ?? $this->check_in_date,
            $financials['check_out_date'] ?? $this->check_out_date
        );
    }

    /**
     * Return report amounts for either the complete current balance (due
     * reports) or the selected historical business date (activity reports).
     * Historical bills come from dated snapshots; deposits come from the
     * payment ledger.
     */
    public function getReportFinancials(string $startDate, string $endDate, bool $includeAllDeposits = false): array
    {
        $financials = $includeAllDeposits
            ? $this->getFinancialBreakdown()
            : $this->getFinancialBreakdownAt($endDate);

        if ($includeAllDeposits) {
            return $financials;
        }

        $financials['advance'] = $this->getAdvanceDepositedInRange($startDate, $endDate);
        $financials['deposited'] = $this->getTotalDepositedInRange($startDate, $endDate);
        $asOfDeposited = $this->getTotalDepositedUpToDate($endDate);
        $financials['remaining'] = max(0, round($financials['grand_total'] - $asOfDeposited, 2));

        return $financials;
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

    public function financialSnapshots()
    {
        return $this->hasMany(BookingFinancialSnapshot::class)
            ->orderBy('effective_date')
            ->orderBy('id');
    }

    /**
     * Save the bill state that became effective on a business date.
     * Duplicate snapshots for payment-only saves are intentionally avoided;
     * payments have their own payment_date ledger.
     */
    public function recordFinancialSnapshot(?string $reason = null, ?string $effectiveDate = null): ?BookingFinancialSnapshot
    {
        if (!$this->exists || !Schema::hasTable('booking_financial_snapshots')) {
            return null;
        }

        $financials = $this->getFinancialBreakdown();
        $effectiveDate = $effectiveDate ?: now('Asia/Dhaka')->toDateString();
        $effectiveAt = $this->updated_at ?: now('Asia/Dhaka');

        $payload = [
            'effective_date' => $effectiveDate,
            'effective_at' => $effectiveAt,
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'room_rent' => $financials['room_rent'],
            'discount' => $financials['discount'],
            'extra_charges' => $financials['extra_charges'],
            'vat' => $financials['vat'],
            'grand_total' => $financials['grand_total'],
            'reason' => $reason,
            'recorded_by_id' => \Illuminate\Support\Facades\Auth::id(),
        ];

        $latest = $this->financialSnapshots()->latest('id')->first();
        if ($latest && $this->snapshotMatches($latest, $payload)) {
            return $latest;
        }

        return $this->financialSnapshots()->create($payload);
    }

    private function snapshotMatches(BookingFinancialSnapshot $snapshot, array $payload): bool
    {
        foreach (['effective_date', 'check_in_date', 'check_out_date'] as $dateField) {
            $stored = $snapshot->{$dateField};
            $stored = $stored instanceof \Carbon\CarbonInterface ? $stored->toDateString() : (string) $stored;
            if ($stored !== (string) ($payload[$dateField] ?? '')) {
                return false;
            }
        }

        foreach (['room_rent', 'discount', 'extra_charges', 'vat', 'grand_total'] as $amountField) {
            if (round((float) $snapshot->{$amountField}, 2) !== round((float) ($payload[$amountField] ?? 0), 2)) {
                return false;
            }
        }

        return true;
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
        $allPayments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();
        $filterDate = \Carbon\Carbon::parse($date)->toDateString();

        $payments = $allPayments->filter(function ($payment) use ($filterDate) {
            $paymentDate = $this->paymentBusinessDate($payment);

            return !$paymentDate || $paymentDate <= $filterDate;
        });
        $advanceRecords = $allPayments->filter(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceRecordsUpToDate = $payments->filter(fn($p) => ($p->type ?? 'payment') === 'advance');

        if ($advanceRecordsUpToDate->isNotEmpty()) {
            $advance = (float) $advanceRecordsUpToDate->sum('amount');
        } elseif ($advanceRecords->isNotEmpty()) {
            // A recorded advance exists, but it was posted after this report date.
            $advance = 0;
        } else {
            $createdAt = $this->rawCreatedAt($this);
            $bookingDate = $createdAt
                ? \Carbon\Carbon::parse($createdAt)
                : null;
            $advance = $bookingDate && $bookingDate->lte($filterDate)
                ? max(0, (float) ($this->advance_payment ?? 0))
                : 0;
        }

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'payment')
            ->sum('amount');
        $refunds = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') === 'refund')
            ->sum('amount');

        return max(0, round($advance + (float) $extraPayments - (float) $refunds, 2));
    }

    /**
     * Get calculated remaining payment as of a specific date.
     */
    public function getCalculatedRemainingUpToDate($date)
    {
        $grandTotal = $this->getFinancialBreakdownAt($date)['grand_total'];
        return max(0, round($grandTotal - $this->getTotalDepositedUpToDate($date), 2));
    }

    /**
     * Get advance payment amount within a date range.
     * Only counts advance payments made between start and end date (inclusive).
     */
    public function getAdvanceDepositedInRange($startDate, $endDate)
    {
        $payments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        $advanceRecords = $payments->filter(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceInRange = $advanceRecords->filter(function ($payment) use ($start, $end) {
            $paymentDate = $this->paymentBusinessDate($payment);
            if (!$paymentDate) {
                return false;
            }

            return $paymentDate >= $start->toDateString()
                && $paymentDate <= $end->toDateString();
        });

        if ($advanceInRange->isNotEmpty()) {
            return round((float) $advanceInRange->sum('amount'), 2);
        }

        if ($advanceRecords->isNotEmpty()) {
            return 0.0;
        }

        $createdAt = $this->rawCreatedAt($this);
        $bookingDate = $createdAt
            ? \Carbon\Carbon::parse($createdAt)
            : null;

        return $bookingDate && $bookingDate->between($start, $end)
            ? max(0, round((float) ($this->advance_payment ?? 0), 2))
            : 0.0;
    }

    /**
     * Get total deposited amount within a date range.
     * Only counts payments made between start and end date (inclusive).
     */
    public function getTotalDepositedInRange($startDate, $endDate)
    {
        $payments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        $inRange = $payments->filter(function ($payment) use ($start, $end) {
            $paymentDate = $this->paymentBusinessDate($payment);
            if (!$paymentDate) {
                return false;
            }

            return $paymentDate >= $start->toDateString()
                && $paymentDate <= $end->toDateString();
        });

        $advanceRecords = $payments->filter(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceInRange = $inRange->filter(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advance = (float) $advanceInRange->sum('amount');

        if ($advanceInRange->isEmpty() && $advanceRecords->isEmpty()) {
            $createdAt = $this->rawCreatedAt($this);
            $bookingDate = $createdAt
                ? \Carbon\Carbon::parse($createdAt)
                : null;
            if ($bookingDate && $bookingDate->between($start, $end)) {
                $advance = max(0, (float) ($this->advance_payment ?? 0));
            }
        }

        $extraPayments = $inRange
            ->filter(fn($p) => ($p->type ?? 'payment') === 'payment')
            ->sum('amount');
        $refunds = $inRange
            ->filter(fn($p) => ($p->type ?? 'payment') === 'refund')
            ->sum('amount');

        return max(0, round($advance + (float) $extraPayments - (float) $refunds, 2));
    }
}

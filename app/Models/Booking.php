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

    // Get all room IDs for this booking (uses bookingRooms if exist, else legacy room_id)
    public function getAllRoomIds()
    {
        $bookingRoomIds = $this->bookingRooms->pluck('room_id')->toArray();
        if (count($bookingRoomIds) > 0) {
            return $bookingRoomIds;
        }
        return $this->room_id ? [$this->room_id] : [];
    }

    // Get all rooms for this booking (handles both legacy single room and new multi-room)
    public function getAllRooms()
    {
        // Check if booking has rooms in booking_rooms table
        $rooms = $this->bookingRooms()->with('room.roomType')->get();
        
        if ($rooms->count() > 0) {
            return $rooms->map(function($br) {
                $room = $br->room;
                $room->booking_price = $br->price_per_night;
                return $room;
            });
        }
        
        // Fallback to legacy single room
        if ($this->room_id && $this->room) {
            $this->room->booking_price = $this->room->price_per_night ?? $this->room->roomType->base_price ?? 0;
            return collect([$this->room]);
        }
        
        return collect([]);
    }

    // Calculate actual total from rooms (use stored prices first, never recalculate from current room rates)
    public function getCalculatedTotal()
    {
        $nights = \Carbon\Carbon::parse($this->check_in_date)->diffInDays(\Carbon\Carbon::parse($this->check_out_date));
        $nights = max(1, $nights);

        $bookingRooms = $this->bookingRooms;

        // If bookingRooms exist with stored prices, use those (premium/multi-room bookings)
        if ($bookingRooms && $bookingRooms->count() > 0) {
            $baseAmount = 0;
            foreach ($bookingRooms as $br) {
                $baseAmount += ($br->price_per_night ?? 0) * $nights;
            }
            return $baseAmount > 0 ? $baseAmount : $this->total_amount;
        }

        // For legacy single-room bookings, always use stored total_amount
        // (room prices may change after booking - we must preserve agreed price)
        return $this->total_amount ?? 0;
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

        // If advance payment record exists but doesn't match bookings.advance_payment,
        // there's a data inconsistency (e.g., booking was edited but payment record wasn't updated).
        // Trust bookings.advance_payment as ground truth and add extra payments.
        if ($advanceRecord && $advanceRecordAmount != $advanceInDb) {
            return $advanceInDb + (float) $extraPayments;
        }

        if ($advanceRecord) {
            // All payments (including advance) properly tracked in booking_payments table
            return $advanceRecordAmount + (float) $extraPayments;
        }

        // Advance is only in bookings table; add any additional payments
        return $advanceInDb + (float) $extraPayments;
    }

    // Get calculated remaining payment
    public function getCalculatedRemaining()
    {
        $grandTotal = $this->getGrandTotal();
        return $grandTotal - $this->getTotalDeposited();
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
        
        $discountAmount = 0;
        if($this->discount_type === 'percentage' && $this->discount_percentage > 0) {
            $discountAmount = ($baseAmount * $this->discount_percentage) / 100;
        } elseif($this->discount_type === 'flat' && $this->discount_amount > 0) {
            $discountAmount = $this->discount_amount;
        }
        
        $afterDiscount = $baseAmount - $discountAmount;
        $extraCharges = $this->extra_charges ?? 0;
        $vatAmount = $this->vat_enabled ? ($afterDiscount * 0.15) : 0;
        
        return $afterDiscount + $extraCharges + $vatAmount;
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
            ->where('type', '!=', 'refund')
            ->whereDate('created_at', '<=', $date)
            ->get();

        $advanceRecord = $payments->first(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceRecordAmount = $advanceRecord ? (float) $advanceRecord->amount : 0;
        $advanceInDb = (float) ($this->advance_payment ?? 0);

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') !== 'advance')
            ->sum('amount');

        if ($advanceRecord && $advanceRecordAmount != $advanceInDb) {
            return $advanceInDb + (float) $extraPayments;
        }

        if ($advanceRecord) {
            return $advanceRecordAmount + (float) $extraPayments;
        }

        // If no advance record in booking_payments but advance_payment is set,
        // check if the booking was created before the filter date
        $bookingDate = \Carbon\Carbon::parse($this->created_at);
        $filterDate = \Carbon\Carbon::parse($date);

        if ($bookingDate->lte($filterDate)) {
            return $advanceInDb + (float) $extraPayments;
        }

        return (float) $extraPayments;
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
     * Get total deposited amount within a date range.
     * Only counts payments made between start and end date (inclusive).
     */
    public function getTotalDepositedInRange($startDate, $endDate)
    {
        $payments = $this->payments()
            ->where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $advanceRecord = $payments->first(fn($p) => ($p->type ?? 'payment') === 'advance');
        $advanceRecordAmount = $advanceRecord ? (float) $advanceRecord->amount : 0;
        $advanceInDb = (float) ($this->advance_payment ?? 0);

        $extraPayments = $payments
            ->filter(fn($p) => ($p->type ?? 'payment') !== 'advance')
            ->sum('amount');

        if ($advanceRecord && $advanceRecordAmount != $advanceInDb) {
            return $advanceInDb + (float) $extraPayments;
        }

        if ($advanceRecord) {
            return $advanceRecordAmount + (float) $extraPayments;
        }

        return $advanceInDb + (float) $extraPayments;
    }
}

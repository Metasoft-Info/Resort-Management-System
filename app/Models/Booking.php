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
        'food_package_id', 'food_package_guests', 'food_package_cost', 'selected_addons', 'addons_cost', 
        'extras', 'additional_guests', 'notes', 'ac_preference', 'vat_enabled', 'vat_amount', 
        'bkash_number', 'bank_name', 'created_by_id',
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
        ];
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

    // Calculate actual total from rooms (not stored total_amount)
    public function getCalculatedTotal()
    {
        $nights = \Carbon\Carbon::parse($this->check_in_date)->diffInDays(\Carbon\Carbon::parse($this->check_out_date));
        $nights = max(1, $nights);
        
        $allRooms = $this->getAllRooms();
        $bookingRooms = $this->bookingRooms;
        
        $baseAmount = 0;
        foreach($allRooms as $room) {
            $bookingRoom = $bookingRooms->where('room_id', $room->id)->first();
            $roomPrice = $bookingRoom ? $bookingRoom->price_per_night : ($room->roomType->price_per_night ?? $room->price_per_night ?? 0);
            $baseAmount += $roomPrice * $nights;
        }
        
        // If no rooms found, fallback to stored total_amount
        if ($baseAmount == 0) {
            $baseAmount = $this->total_amount;
        }
        
        return $baseAmount;
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
}

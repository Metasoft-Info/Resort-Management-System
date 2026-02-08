<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'room_id', 'customer_name', 'customer_nid', 'customer_photo', 'customer_nid_document',
        'passport_number', 'passport_document', 'visiting_card', 'customer_phone', 'reference_name',
        'reference_phone', 'customer_whatsapp', 'customer_email', 'customer_address', 'company_name',
        'check_in_date', 'check_in_time', 'check_out_date', 'check_out_time', 'number_of_guests', 
        'total_amount', 'advance_payment', 'remaining_payment', 'payment_method', 'payment_status', 
        'status', 'extra_charges', 'extra_charges_description', 'extra_charges_data',
        'discount_amount', 'discount_percentage', 'discount_type', 'discount_reference',
        'food_package_id', 'food_package_guests', 'food_package_cost', 'selected_addons', 'addons_cost', 
        'extras', 'additional_guests', 'notes', 'ac_preference', 'vat_enabled', 'vat_amount', 
        'bkash_number', 'bank_name', 'created_by_id',
    ];

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

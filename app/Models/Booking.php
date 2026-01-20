<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'room_id', 'customer_name', 'customer_nid', 'customer_photo', 'customer_nid_document',
        'passport_number', 'passport_document', 'visiting_card', 'customer_phone', 'reference_name',
        'reference_phone', 'customer_whatsapp', 'customer_email', 'customer_address', 'check_in_date',
        'check_in_time', 'check_out_date', 'check_out_time', 'number_of_guests', 'total_amount',
        'advance_payment', 'remaining_payment', 'payment_method', 'payment_status', 'status',
        'extra_charges', 'extra_charges_description', 'discount_amount', 'discount_percentage',
        'discount_type', 'food_package_id', 'food_package_guests', 'food_package_cost',
        'selected_addons', 'addons_cost', 'extras', 'additional_guests', 'notes', 'ac_preference',
        'vat_enabled', 'vat_amount', 'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'selected_addons' => 'array',
            'extras' => 'array',
            'additional_guests' => 'array',
            'vat_enabled' => 'boolean',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
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

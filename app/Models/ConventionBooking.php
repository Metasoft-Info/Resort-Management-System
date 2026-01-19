<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConventionBooking extends Model
{
    protected $fillable = [
        'hall_id', 'customer_name', 'customer_nid', 'customer_phone', 'customer_whatsapp',
        'customer_email', 'customer_address', 'event_date', 'start_time', 'end_time',
        'time_slot', 'event_type', 'organization_name', 'event_description', 'number_of_guests',
        'food_package_id', 'food_cost', 'selected_addons', 'addon_quantities', 'addons_cost',
        'hall_rent', 'discount', 'discount_type', 'discount_value', 'vat_amount', 'vat_percentage',
        'total_amount', 'advance_payment', 'remaining_payment', 'payment_method', 'payment_status',
        'status', 'program_status', 'notes', 'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'selected_addons' => 'array',
            'addon_quantities' => 'array',
        ];
    }

    public function hall()
    {
        return $this->belongsTo(ConventionHall::class, 'hall_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function payments()
    {
        return $this->hasMany(ConventionPayment::class, 'convention_booking_id');
    }
}

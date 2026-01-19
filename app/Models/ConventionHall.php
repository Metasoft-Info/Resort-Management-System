<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConventionHall extends Model
{
    protected $fillable = [
        'name', 'description', 'dimensions', 'max_capacity', 'price_per_day',
        'is_available', 'amenities', 'images', 'event_types', 'time_slots',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'images' => 'array',
            'event_types' => 'array',
            'time_slots' => 'array',
            'is_available' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(ConventionBooking::class, 'hall_id');
    }
}

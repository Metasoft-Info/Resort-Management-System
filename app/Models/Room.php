<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'name',
        'type',
        'room_type_id',
        'description',
        'price_per_night',
        'has_ac',
        'ac_price',
        'non_ac_price',
        'max_guests',
        'number_of_beds',
        'amenities',
        'images',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'images' => 'array',
            'has_ac' => 'boolean',
            'price_per_night' => 'decimal:2',
            'ac_price' => 'decimal:2',
            'non_ac_price' => 'decimal:2',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }
}

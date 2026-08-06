<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'room_id',
        'price_per_night',
        'check_in_date',
        'check_out_date',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}

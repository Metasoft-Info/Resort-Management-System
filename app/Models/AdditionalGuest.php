<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'name',
        'nid',
        'phone',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

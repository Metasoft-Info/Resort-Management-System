<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGroup extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'created_by_id'];

    public function bookings() { return $this->hasMany(Booking::class); }
}

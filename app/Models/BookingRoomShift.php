<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoomShift extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['occupied_from' => 'date', 'shift_date' => 'date'];

    public function fromRoom() { return $this->belongsTo(Room::class, 'from_room_id'); }
    public function shiftedBy() { return $this->belongsTo(User::class, 'shifted_by_id'); }
}

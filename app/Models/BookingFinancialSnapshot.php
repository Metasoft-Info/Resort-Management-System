<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFinancialSnapshot extends Model
{
    protected $fillable = [
        'booking_id',
        'effective_date',
        'effective_at',
        'check_in_date',
        'check_out_date',
        'room_rent',
        'discount',
        'extra_charges',
        'vat',
        'grand_total',
        'reason',
        'recorded_by_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'effective_at' => 'datetime',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'room_rent' => 'decimal:2',
        'discount' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'vat' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}

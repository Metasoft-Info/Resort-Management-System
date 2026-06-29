<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConventionPayment extends Model
{
    protected $fillable = [
        'convention_booking_id', 'amount', 'payment_method', 'method', 'bkash_number', 'bank_name',
        'payment_date', 'notes', 'note', 'received_by_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(ConventionBooking::class, 'convention_booking_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }
}

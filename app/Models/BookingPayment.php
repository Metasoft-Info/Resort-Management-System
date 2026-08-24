<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'type',
        'note',
        'recorded_by_id',
        'request_id',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (BookingPayment $payment) {
            // Store the cashier's local business date explicitly. This keeps
            // reports correct even when the database timestamp is UTC.
            if (!$payment->payment_date && Schema::hasColumn('booking_payments', 'payment_date')) {
                $payment->payment_date = now('Asia/Dhaka')->toDateString();
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}

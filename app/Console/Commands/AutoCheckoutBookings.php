<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCheckoutBookings extends Command
{
    protected $signature = 'bookings:auto-checkout';
    protected $description = 'Automatically check-in and check-out bookings based on date+time';

    public function handle()
    {
        $now = Carbon::now('Asia/Dhaka');

        // ---- AUTO CHECK-IN ----
        // Bookings with status 'confirmed' whose check_in_date+time has passed
        $checkInBookings = Booking::where('status', 'confirmed')
            ->whereDate('check_in_date', '<=', $now)
            ->get();

        $checkInCount = 0;
        foreach ($checkInBookings as $booking) {
            $checkInDateTime = $this->parseDateTime($booking->check_in_date, $booking->check_in_time);
            if ($checkInDateTime && $now->gte($checkInDateTime)) {
                $booking->update(['status' => 'checked_in']);
                $checkInCount++;
                $this->info("Auto checked-in booking #{$booking->id} - {$booking->customer_name}");
            }
        }
        $this->info("Total bookings auto checked-in: {$checkInCount}");

        // ---- AUTO CHECK-OUT ----
        // Bookings with status 'checked_in' whose check_out_date+time has passed
        $checkOutBookings = Booking::where('status', 'checked_in')
            ->whereDate('check_out_date', '<=', $now)
            ->get();

        $checkOutCount = 0;
        foreach ($checkOutBookings as $booking) {
            $checkOutDateTime = $this->parseDateTime($booking->check_out_date, $booking->check_out_time);
            if ($checkOutDateTime && $now->gte($checkOutDateTime)) {
                $booking->update(['status' => 'checked_out']);
                $checkOutCount++;
                $this->info("Auto checked-out booking #{$booking->id} - {$booking->customer_name}");
            }
        }
        $this->info("Total bookings auto checked-out: {$checkOutCount}");

        return Command::SUCCESS;
    }

    private function parseDateTime($date, $time)
    {
        if (!$date) return null;
        $timeStr = $time ?: '12:00';
        try {
            return Carbon::parse("{$date} {$timeStr}", 'Asia/Dhaka');
        } catch (\Exception $e) {
            return Carbon::parse($date, 'Asia/Dhaka');
        }
    }
}

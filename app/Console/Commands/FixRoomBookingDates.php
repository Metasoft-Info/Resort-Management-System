<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Carbon\Carbon;

class FixRoomBookingDates extends Command
{
    protected $signature = 'booking:fix-room-dates {booking_id} {check_in_date} {room_numbers*}';
    protected $description = 'Fix check-in dates for specific rooms in an existing booking';

    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        $roomNumbers = $this->argument('room_numbers');
        $checkInDate = $this->argument('check_in_date');

        $booking = Booking::find($bookingId);
        if (!$booking) {
            $this->error("Booking #{$bookingId} not found!");
            return 1;
        }

        $this->info("Booking #{$bookingId} found:");
        $this->info("  Customer: {$booking->customer_name}");
        $this->info("  Booking dates: {$booking->check_in_date} to {$booking->check_out_date}");

        // Validate check_in_date is within booking range
        $bookingCheckIn = Carbon::parse($booking->check_in_date);
        $bookingCheckOut = Carbon::parse($booking->check_out_date);
        $newCheckIn = Carbon::parse($checkInDate);

        if ($newCheckIn->lt($bookingCheckIn)) {
            $this->error("Check-in date {$checkInDate} is before booking start date {$booking->check_in_date}!");
            return 1;
        }
        if ($newCheckIn->gt($bookingCheckOut)) {
            $this->error("Check-in date {$checkInDate} is after booking end date {$booking->check_out_date}!");
            return 1;
        }

        $rooms = Room::whereIn('room_number', $roomNumbers)->get();
        if ($rooms->isEmpty()) {
            $this->error("No rooms found with numbers: " . implode(', ', $roomNumbers));
            return 1;
        }

        // Update room dates
        foreach ($rooms as $room) {
            $br = BookingRoom::where('booking_id', $bookingId)
                ->where('room_id', $room->id)
                ->first();

            if (!$br) {
                $this->warn("  Room {$room->room_number} (ID: {$room->id}) is not in this booking. Skipping.");
                continue;
            }

            $oldDateDisplay = $br->check_in_date ?? $booking->check_in_date;
            $this->line("  Room {$room->room_number}: check_in {$oldDateDisplay} -> {$checkInDate}");

            $br->check_in_date = $checkInDate;
            $br->check_out_date = $booking->check_out_date;
            $br->save();
        }

        // Recalculate total_amount from scratch using getCalculatedTotal
        $booking->refresh();
        $newCalculatedTotal = $booking->getCalculatedTotal();
        $newGrandTotal = $booking->getGrandTotal();
        $totalDeposited = $booking->getTotalDeposited();

        $this->info("  Recalculated base total: {$newCalculatedTotal}");
        $this->info("  Recalculated grand total: {$newGrandTotal}");
        $this->info("  Total deposited: {$totalDeposited}");

        $booking->total_amount = $newCalculatedTotal;
        $booking->remaining_payment = $newGrandTotal - $totalDeposited;
        $booking->payment_status = $booking->getCalculatedPaymentStatus();
        $booking->save();

        $this->info("  Updated total_amount: {$booking->total_amount}");
        $this->info("  Updated remaining_payment: {$booking->remaining_payment}");
        $this->info("Done! Room dates fixed for booking #{$bookingId}");
        return 0;
    }
}

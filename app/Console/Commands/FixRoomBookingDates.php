<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Carbon\Carbon;

class FixRoomBookingDates extends Command
{
    protected $signature = 'booking:fix-room-dates {booking_id} {room_numbers*} {check_in_date}';
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

        $rooms = Room::whereIn('room_number', $roomNumbers)->get();
        if ($rooms->isEmpty()) {
            $this->error("No rooms found with numbers: " . implode(', ', $roomNumbers));
            return 1;
        }

        $bookingCheckOut = $booking->check_out_date;
        $newNights = Carbon::parse($checkInDate)->diffInDays(Carbon::parse($bookingCheckOut));
        $newNights = max(1, $newNights);

        $oldNights = Carbon::parse($booking->check_in_date)->diffInDays(Carbon::parse($bookingCheckOut));
        $oldNights = max(1, $oldNights);

        $totalAdjustment = 0;

        foreach ($rooms as $room) {
            $br = BookingRoom::where('booking_id', $bookingId)
                ->where('room_id', $room->id)
                ->first();

            if (!$br) {
                $this->warn("  Room {$room->room_number} (ID: {$room->id}) is not in this booking. Skipping.");
                continue;
            }

            $oldNightsForRoom = $br->check_in_date
                ? max(1, Carbon::parse($br->check_in_date)->diffInDays(Carbon::parse($br->check_out_date ?? $bookingCheckOut)))
                : $oldNights;

            $oldAmount = ($br->price_per_night ?? 0) * $oldNightsForRoom;
            $newAmount = ($br->price_per_night ?? 0) * $newNights;
            $adjustment = $newAmount - $oldAmount;
            $totalAdjustment += $adjustment;

            $this->line("  Room {$room->room_number}:");
            $oldDateDisplay = $br->check_in_date ?? $booking->check_in_date;
            $this->line("    Old: check_in={$oldDateDisplay}, nights={$oldNightsForRoom}, amount={$oldAmount}");
            $this->line("    New: check_in={$checkInDate}, nights={$newNights}, amount={$newAmount}");
            $this->line("    Adjustment: {$adjustment}");

            $br->check_in_date = $checkInDate;
            $br->check_out_date = $bookingCheckOut;
            $br->save();
        }

        if ($totalAdjustment != 0) {
            $booking->total_amount += $totalAdjustment;
            $booking->remaining_payment += $totalAdjustment;
            $booking->payment_status = $booking->getCalculatedPaymentStatus();
            $booking->save();

            $this->info("  Total amount adjusted by: {$totalAdjustment}");
            $this->info("  New total_amount: {$booking->total_amount}");
            $this->info("  New remaining_payment: {$booking->remaining_payment}");
        }

        $this->info("Done! Room dates fixed for booking #{$bookingId}");
        return 0;
    }
}

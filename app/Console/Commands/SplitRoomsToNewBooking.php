<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Carbon\Carbon;

class SplitRoomsToNewBooking extends Command
{
    protected $signature = 'booking:split-rooms {booking_id} {check_in_date} {room_numbers*}';
    protected $description = 'Remove rooms from existing booking and create a new booking with those rooms';

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

        $bookingCheckOut = $booking->check_out_date;
        $newCheckIn = Carbon::parse($checkInDate);

        if ($newCheckIn->gt(Carbon::parse($bookingCheckOut))) {
            $this->error("New check-in date {$checkInDate} is after booking checkout {$bookingCheckOut}!");
            return 1;
        }

        $rooms = Room::whereIn('room_number', $roomNumbers)->get();
        if ($rooms->isEmpty()) {
            $this->error("No rooms found: " . implode(', ', $roomNumbers));
            return 1;
        }

        // Collect booking_room data before removing
        $roomsToMove = [];
        foreach ($rooms as $room) {
            $br = BookingRoom::where('booking_id', $bookingId)
                ->where('room_id', $room->id)
                ->first();

            if (!$br) {
                $this->warn("  Room {$room->room_number} not in booking #{$bookingId}. Skipping.");
                continue;
            }

            $roomsToMove[] = [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'price_per_night' => $br->price_per_night,
            ];

            $this->line("  Removing room {$room->room_number} from booking #{$bookingId}");
            $br->delete();
        }

        if (empty($roomsToMove)) {
            $this->error("No rooms to move!");
            return 1;
        }

        // Recalculate original booking total
        $booking->refresh();
        $newCalculatedTotal = $booking->getCalculatedTotal();
        $newGrandTotal = $booking->getGrandTotal();
        $totalDeposited = $booking->getTotalDeposited();

        $booking->total_amount = $newCalculatedTotal;
        $booking->remaining_payment = max(0, $newGrandTotal - $totalDeposited);
        $booking->payment_status = $booking->getCalculatedPaymentStatus();

        // Update notes
        $allRoomNumbers = $booking->getAllRooms()->pluck('room_number')->implode(', ');
        $booking->notes = preg_replace('/\[Rooms:.*?\]/', '', $booking->notes ?? '');
        $booking->notes = trim($booking->notes) . " [Rooms: {$allRoomNumbers}]";
        $booking->save();

        $this->info("  Booking #{$bookingId} updated:");
        $this->info("    Rooms remaining: {$allRoomNumbers}");
        $this->info("    New total: {$booking->total_amount}");
        $this->info("    New remaining: {$booking->remaining_payment}");

        // Create new booking with same customer info
        $newBookingData = $booking->only([
            'customer_name', 'customer_nid', 'customer_phone', 'customer_whatsapp',
            'customer_email', 'passport_number', 'customer_address', 'company_name',
            'reference_name', 'reference_phone', 'number_of_guests', 'ac_preference',
            'check_in_time', 'check_out_time', 'payment_method', 'bkash_number',
            'bank_name', 'customer_photo', 'customer_nid_document', 'passport_document',
            'visiting_card', 'vat_enabled', 'vat_amount', 'discount_type',
            'discount_percentage', 'discount_amount', 'extra_charges',
            'extra_charges_description', 'food_package_guests', 'food_package_cost',
            'addons_cost', 'discount_status', 'discount_approved_by',
            'discount_approved_at', 'discount_requested_by',
        ]);

        $newBookingData['check_in_date'] = $checkInDate;
        $newBookingData['check_out_date'] = $bookingCheckOut;
        $newBookingData['status'] = 'confirmed';
        $newBookingData['advance_payment'] = 0;
        $newBookingData['remaining_payment'] = 0;
        $newBookingData['payment_status'] = 'pending';
        $newBookingData['created_by_id'] = $booking->created_by_id;
        $newBookingData['updated_by_id'] = $booking->updated_by_id;
        $newBookingData['notes'] = "Split from booking #{$bookingId} [Rooms: " . implode(', ', array_column($roomsToMove, 'room_number')) . "]";

        $newBooking = Booking::create($newBookingData);

        // Add rooms to new booking
        foreach ($roomsToMove as $roomData) {
            BookingRoom::create([
                'booking_id' => $newBooking->id,
                'room_id' => $roomData['room_id'],
                'price_per_night' => $roomData['price_per_night'],
                'check_in_date' => $checkInDate,
                'check_out_date' => $bookingCheckOut,
            ]);
        }

        // Calculate new booking total
        $newBooking->refresh();
        $newTotal = $newBooking->getCalculatedTotal();
        $newBooking->total_amount = $newTotal;
        $newBooking->remaining_payment = $newTotal;
        $newBooking->save();

        $this->info("");
        $this->info("  New booking #{$newBooking->id} created:");
        $this->info("    Customer: {$newBooking->customer_name}");
        $this->info("    Rooms: " . implode(', ', array_column($roomsToMove, 'room_number')));
        $this->info("    Check-in: {$checkInDate}");
        $this->info("    Check-out: {$bookingCheckOut}");
        $this->info("    Total: {$newTotal}");
        $this->info("");
        $this->info("Done! Rooms split to new booking #{$newBooking->id}");
        return 0;
    }
}

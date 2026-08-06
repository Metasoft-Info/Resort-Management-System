<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Carbon\Carbon;

class CreateRoomsBookingFromExisting extends Command
{
    protected $signature = 'booking:create-rooms-from-existing {source_booking_id} {check_in_date} {room_numbers*}';
    protected $description = 'Create a new booking for specific rooms by copying customer details from an existing booking';

    public function handle()
    {
        $sourceBookingId = $this->argument('source_booking_id');
        $roomNumbers = $this->argument('room_numbers');
        $checkInDate = $this->argument('check_in_date');

        $sourceBooking = Booking::find($sourceBookingId);
        if (!$sourceBooking) {
            $this->error("Booking #{$sourceBookingId} not found!");
            return 1;
        }

        $checkOutDate = $sourceBooking->check_out_date;
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);

        if ($checkIn->gt($checkOut)) {
            $this->error("Check-in date {$checkInDate} is after checkout {$checkOutDate}!");
            return 1;
        }

        $rooms = Room::whereIn('room_number', $roomNumbers)->get();
        if ($rooms->isEmpty()) {
            $this->error("No rooms found: " . implode(', ', $roomNumbers));
            return 1;
        }

        $roomList = [];
        $totalAmount = 0;

        foreach ($rooms as $room) {
            // Check conflict
            $hasConflict = Booking::with('bookingRooms')
                ->whereNotIn('status', ['cancelled', 'checked_out'])
                ->where('check_in_date', '<', $checkOut->toDateString())
                ->where('check_out_date', '>', $checkIn->toDateString())
                ->get()
                ->some(function ($booking) use ($checkIn, $checkOut, $room) {
                    $bookingRooms = $booking->bookingRooms;
                    if ($bookingRooms && $bookingRooms->count() > 0) {
                        foreach ($bookingRooms as $br) {
                            if ((int)$br->room_id !== (int)$room->id) continue;
                            $brCheckIn = $br->check_in_date
                                ? Carbon::parse($br->check_in_date)->setTimeFromTimeString($booking->check_in_time ?? '12:00')
                                : $booking->getCheckInDateTime();
                            $brCheckOut = $br->check_out_date
                                ? Carbon::parse($br->check_out_date)->setTimeFromTimeString($booking->check_out_time ?? '12:00')
                                : $booking->getCheckOutDateTime();
                            if ($brCheckIn->lt($checkOut) && $brCheckOut->gt($checkIn)) {
                                return true;
                            }
                        }
                        return false;
                    }
                    if (!in_array($room->id, array_map('intval', $booking->getAllRoomIds()))) return false;
                    return $booking->getCheckInDateTime()->lt($checkOut) && $booking->getCheckOutDateTime()->gt($checkIn);
                });

            if ($hasConflict) {
                $this->error("Room {$room->room_number} is already booked for {$checkInDate} to {$checkOutDate}!");
                return 1;
            }

            // Determine price - try current room price or room type base
            $price = $room->price_per_night ?? $room->roomType->base_price ?? 0;
            $nights = $checkIn->diffInDays($checkOut);
            $nights = max(1, $nights);
            $roomTotal = $price * $nights;
            $totalAmount += $roomTotal;

            $roomList[] = [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'price_per_night' => $price,
            ];

            $this->line("  Room {$room->room_number}: {$price}/night × {$nights} = {$roomTotal}");
        }

        // Copy customer info from source booking
        $newBookingData = $sourceBooking->only([
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
        $newBookingData['check_out_date'] = $checkOutDate;
        $newBookingData['status'] = 'confirmed';
        $newBookingData['total_amount'] = $totalAmount;
        $newBookingData['advance_payment'] = 0;
        $newBookingData['remaining_payment'] = $totalAmount;
        $newBookingData['payment_status'] = 'pending';
        $newBookingData['created_by_id'] = $sourceBooking->created_by_id;
        $newBookingData['updated_by_id'] = $sourceBooking->updated_by_id;
        $newBookingData['notes'] = "Created from booking #{$sourceBookingId} [Rooms: " . implode(', ', array_column($roomList, 'room_number')) . "]";

        $newBooking = Booking::create($newBookingData);

        foreach ($roomList as $roomData) {
            BookingRoom::create([
                'booking_id' => $newBooking->id,
                'room_id' => $roomData['room_id'],
                'price_per_night' => $roomData['price_per_night'],
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
            ]);
        }

        $this->info("");
        $this->info("  New booking #{$newBooking->id} created:");
        $this->info("    Customer: {$newBooking->customer_name}");
        $this->info("    Phone: {$newBooking->customer_phone}");
        $this->info("    Rooms: " . implode(', ', array_column($roomList, 'room_number')));
        $this->info("    Check-in: {$checkInDate}");
        $this->info("    Check-out: {$checkOutDate}");
        $this->info("    Total: {$totalAmount}");
        $this->info("");
        $this->info("Done!");
        return 0;
    }
}

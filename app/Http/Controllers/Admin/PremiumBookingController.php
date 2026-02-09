<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use App\Models\RoomType;
use App\Mail\BookingConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PremiumBookingController extends Controller
{
    public function index(Request $request)
    {
        $roomTypes = RoomType::all();
        $existingBooking = null;
        
        // If booking_id is provided, load existing booking for adding rooms
        if ($request->has('booking_id')) {
            $existingBooking = Booking::with(['room', 'room.roomType', 'bookingRooms.room.roomType'])
                ->find($request->booking_id);
        }
        
        return view('admin.premium-booking.index', compact('roomTypes', 'existingBooking'));
    }

    /**
     * Search for customer by phone and return the most recently updated record
     */
    public function searchCustomer(Request $request)
    {
        $phone = $request->input('phone');
        
        if (!$phone) {
            return response()->json(['success' => false, 'message' => 'Phone required']);
        }
        
        // Get the most recently updated booking for this phone
        $booking = Booking::where('customer_phone', $phone)
            ->orderBy('updated_at', 'desc')
            ->first();
        
        if ($booking) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'customer_name' => $booking->customer_name,
                    'customer_nid' => $booking->customer_nid,
                    'customer_phone' => $booking->customer_phone,
                    'customer_whatsapp' => $booking->customer_whatsapp,
                    'customer_email' => $booking->customer_email,
                    'passport_number' => $booking->passport_number,
                    'customer_address' => $booking->customer_address,
                    'company_name' => $booking->company_name,
                    'reference_name' => $booking->reference_name,
                    'reference_phone' => $booking->reference_phone,
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'No customer found']);
    }

    public function search(Request $request)
    {
        $checkIn = Carbon::parse($request->checkIn);
        $checkOut = Carbon::parse($request->checkOut);
        $roomTypeId = $request->roomTypeId;
        $excludeBookingId = $request->excludeBookingId; // Exclude this booking from conflict check
        $nights = $checkIn->diffInDays($checkOut);

        // Get all rooms
        $query = Room::with('roomType')->where('status', 'available');
        
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }

        $rooms = $query->get();

        // Filter out rooms that are already booked for these dates
        // Check both legacy room_id and booking_rooms table
        $availableRooms = $rooms->filter(function ($room) use ($checkIn, $checkOut, $excludeBookingId) {
            // Check legacy bookings
            $legacyQuery = Booking::where('room_id', $room->id)
                ->where('status', '!=', 'cancelled');
            
            // Exclude the current booking if adding rooms
            if ($excludeBookingId) {
                $legacyQuery->where('id', '!=', $excludeBookingId);
            }
            
            $hasLegacyConflict = $legacyQuery->where(function ($query) use ($checkIn, $checkOut) {
                    $query->where(function($q) use ($checkIn, $checkOut) {
                        // Overlap: existing.start < new.end AND existing.end > new.start
                        $q->where('check_in_date', '<', $checkOut)
                          ->where('check_out_date', '>', $checkIn);
                    });
                })
                ->exists();

            if ($hasLegacyConflict) return false;

            // Check booking_rooms table
            $multiRoomQuery = BookingRoom::where('room_id', $room->id)
                ->whereHas('booking', function($q) use ($checkIn, $checkOut, $excludeBookingId) {
                    $q->where('status', '!=', 'cancelled');
                    
                    if ($excludeBookingId) {
                        $q->where('id', '!=', $excludeBookingId);
                    }
                    
                    $q->where(function ($query) use ($checkIn, $checkOut) {
                        // Overlap: existing.start < new.end AND existing.end > new.start
                        $query->where('check_in_date', '<', $checkOut)
                              ->where('check_out_date', '>', $checkIn);
                    });
                });
                
            $hasMultiRoomConflict = $multiRoomQuery->exists();

            return !$hasMultiRoomConflict;
        });

        return response()->json([
            'availableRooms' => $availableRooms->values(),
            'nights' => $nights
        ]);
    }

    public function book(Request $request)
    {
        try {
            // Parse additional_guests if it's a JSON string
            $additionalGuestsRaw = $request->input('additional_guests');
            if (is_string($additionalGuestsRaw) && !empty($additionalGuestsRaw)) {
                $request->merge(['additional_guests' => json_decode($additionalGuestsRaw, true)]);
            }

            // Parse rooms data if it's a JSON string
            $roomsDataRaw = $request->input('rooms_data');
            $roomsData = [];
            if (is_string($roomsDataRaw) && !empty($roomsDataRaw)) {
                $roomsData = json_decode($roomsDataRaw, true);
            }

            // Check if we're adding rooms to an existing booking
            $existingBookingId = $request->input('existing_booking_id');
            if ($existingBookingId) {
                return $this->addRoomsToExistingBooking($existingBookingId, $roomsData);
            }

            // Determine if this is multi-room or single room booking
            $isMultiRoom = !empty($roomsData) && count($roomsData) > 1;
            $singleRoomId = $request->input('room_id');
            
            // For single room, validate room_id
            if (!$isMultiRoom && empty($roomsData)) {
                $request->validate(['room_id' => 'required|exists:rooms,id']);
            }

            $validated = $request->validate([
                'room_id' => 'nullable|exists:rooms,id',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'check_in_time' => 'nullable|string',
                'check_out_time' => 'nullable|string',
                'customer_name' => 'required|string',
                'customer_nid' => 'nullable|string',
                'customer_phone' => 'required|string',
                'customer_whatsapp' => 'nullable|string',
                'customer_email' => 'nullable|email',
                'passport_number' => 'nullable|string',
                'customer_address' => 'nullable|string',
                'company_name' => 'nullable|string',
                'reference_name' => 'nullable|string',
                'reference_phone' => 'nullable|string',
                'number_of_guests' => 'required|integer|min:1',
                'ac_preference' => 'required|in:ac,non-ac',
                'status' => 'required|in:confirmed,pending',
                'notes' => 'nullable|string',
                'total_amount' => 'required|numeric|min:0',
                'vat_enabled' => 'nullable|boolean',
                'vat_amount' => 'nullable|numeric',
                'discount_type' => 'nullable|in:none,percentage,flat',
                'discount_percentage' => 'nullable|numeric',
                'discount_amount' => 'nullable|numeric',
                'extra_charges' => 'nullable|numeric',
                'extra_charges_description' => 'nullable|string',
                'extra_charges_data' => 'nullable|array',
                'advance_payment' => 'nullable|numeric|min:0',
                'remaining_payment' => 'nullable|numeric',
                'payment_method' => 'required|in:cash,card,bkash',
                'bkash_number' => 'nullable|string',
                'bank_name' => 'nullable|string',
                'customer_photo' => 'nullable|image|max:2048',
                'customer_nid_document' => 'nullable|file|max:2048',
                'passport_document' => 'nullable|file|max:2048',
                'visiting_card' => 'nullable|image|max:2048',
                'additional_guests' => 'nullable|array',
                'additional_guests.*.name' => 'nullable|string',
                'additional_guests.*.nid' => 'nullable|string',
                'additional_guests.*.phone' => 'nullable|string',
                'additional_guests.*.company_name' => 'nullable|string',
            ]);

            // Handle file uploads
            if ($request->hasFile('customer_photo')) {
                $validated['customer_photo'] = $request->file('customer_photo')->store('bookings', 'public');
            }
            if ($request->hasFile('customer_nid_document')) {
                $validated['customer_nid_document'] = $request->file('customer_nid_document')->store('bookings', 'public');
            }
            if ($request->hasFile('passport_document')) {
                $validated['passport_document'] = $request->file('passport_document')->store('bookings', 'public');
            }
            if ($request->hasFile('visiting_card')) {
                $validated['visiting_card'] = $request->file('visiting_card')->store('bookings', 'public');
            }

            // Set default values for advance/remaining
            $validated['advance_payment'] = $validated['advance_payment'] ?? 0;
            $validated['remaining_payment'] = $validated['remaining_payment'] ?? $validated['total_amount'];
            
            // Set default values for optional fields that DB requires
            $validated['extra_charges'] = $validated['extra_charges'] ?? 0;
            $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
            $validated['discount_percentage'] = $validated['discount_percentage'] ?? 0;
            $validated['discount_type'] = $validated['discount_type'] ?? 'none';
            $validated['vat_enabled'] = $validated['vat_enabled'] ?? false;
            $validated['vat_amount'] = $validated['vat_amount'] ?? 0;
            $validated['food_package_guests'] = 0;
            $validated['food_package_cost'] = 0;
            $validated['addons_cost'] = 0;

            // Set payment status
            $validated['payment_status'] = $validated['advance_payment'] >= $validated['total_amount'] ? 'paid' : 
                                           ($validated['advance_payment'] > 0 ? 'partial' : 'pending');
            $validated['created_by_id'] = Auth::id();

            // Extract additional guests data before creating booking
            $additionalGuestsData = $request->input('additional_guests', []);
            if (isset($validated['additional_guests'])) {
                unset($validated['additional_guests']);
            }

            // For multi-room booking, set room_id to null (use booking_rooms table)
            if (!empty($roomsData) && count($roomsData) > 0) {
                $validated['room_id'] = null;
                // Add note about multiple rooms
                $roomNumbers = collect($roomsData)->pluck('roomNumber')->implode(', ');
                $validated['notes'] = ($validated['notes'] ?? '') . " [Rooms: {$roomNumbers}]";
            }

            DB::beginTransaction();

            $booking = Booking::create($validated);

            // Add rooms to booking_rooms table
            if (!empty($roomsData) && count($roomsData) > 0) {
                foreach ($roomsData as $roomData) {
                    BookingRoom::create([
                        'booking_id' => $booking->id,
                        'room_id' => $roomData['roomId'],
                        'price_per_night' => $roomData['pricePerNight'] ?? 0,
                    ]);
                }
            } elseif ($singleRoomId) {
                // Single room - still add to booking_rooms for consistency
                $room = Room::find($singleRoomId);
                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $singleRoomId,
                    'price_per_night' => $room->price_per_night ?? $room->roomType->base_price ?? 0,
                ]);
            }

            // Add additional guests
            if (!empty($additionalGuestsData)) {
                foreach ($additionalGuestsData as $guestData) {
                    $booking->additionalGuests()->create([
                        'name' => $guestData['name'] ?? null,
                        'nid' => $guestData['nid'] ?? null,
                        'phone' => $guestData['phone'] ?? null,
                        'company_name' => $guestData['company_name'] ?? null,
                    ]);
                }
            }

            // Record initial advance payment in payment history
            if ($validated['advance_payment'] > 0) {
                $booking->payments()->create([
                    'amount' => $validated['advance_payment'],
                    'method' => $validated['payment_method'],
                    'type' => 'advance',
                    'note' => 'Initial advance payment during booking creation',
                    'recorded_by_id' => Auth::id(),
                ]);
            }

            DB::commit();

            // Send confirmation email if booking is confirmed and customer has email
            $emailSent = false;
            if ($validated['status'] === 'confirmed' && !empty($validated['customer_email'])) {
                try {
                    Mail::to($validated['customer_email'])->send(new BookingConfirmationMail($booking));
                    $emailSent = true;
                    Log::info("Booking confirmation email sent to {$validated['customer_email']} for booking #{$booking->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to send confirmation email for booking #{$booking->id}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully' . ($emailSent ? '. Confirmation email sent.' : ''),
                'booking' => $booking,
                'email_sent' => $emailSent
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add rooms to an existing booking
     */
    private function addRoomsToExistingBooking($bookingId, $roomsData)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            
            DB::beginTransaction();
            
            // Add new rooms to booking_rooms table
            $addedRooms = [];
            $totalAdditionalAmount = 0;
            $nights = Carbon::parse($booking->check_in_date)->diffInDays(Carbon::parse($booking->check_out_date));
            $nights = max(1, $nights);
            
            foreach ($roomsData as $roomData) {
                // Check if room is already in this booking
                $exists = BookingRoom::where('booking_id', $bookingId)
                    ->where('room_id', $roomData['roomId'])
                    ->exists();
                    
                if (!$exists) {
                    BookingRoom::create([
                        'booking_id' => $bookingId,
                        'room_id' => $roomData['roomId'],
                        'price_per_night' => $roomData['pricePerNight'] ?? 0,
                    ]);
                    $addedRooms[] = $roomData['roomNumber'];
                    $totalAdditionalAmount += ($roomData['pricePerNight'] ?? 0) * $nights;
                }
            }
            
            // Update booking total amount and remaining payment
            if ($totalAdditionalAmount > 0) {
                $booking->total_amount += $totalAdditionalAmount;
                $booking->remaining_payment += $totalAdditionalAmount;
                
                // Update notes with new rooms
                $allRoomNumbers = $booking->getAllRooms()->pluck('room_number')->implode(', ');
                $booking->notes = preg_replace('/\[Rooms:.*?\]/', '', $booking->notes);
                $booking->notes = trim($booking->notes) . " [Rooms: {$allRoomNumbers}]";
                
                // Update payment status
                $booking->payment_status = $booking->advance_payment >= $booking->total_amount ? 'paid' : 
                                          ($booking->advance_payment > 0 ? 'partial' : 'pending');
                $booking->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($addedRooms) > 0 
                    ? 'Added rooms: ' . implode(', ', $addedRooms) . ' to existing booking'
                    : 'No new rooms were added (already exist in booking)',
                'booking' => $booking->fresh()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add rooms to booking failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add rooms: ' . $e->getMessage()
            ], 500);
        }
    }
}

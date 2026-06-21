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
        $preselectedRoom = null;
        
        // If booking_id is provided, load existing booking for adding rooms
        if ($request->has('booking_id')) {
            $existingBooking = Booking::with(['room', 'room.roomType', 'bookingRooms.room.roomType'])
                ->find($request->booking_id);
        }
        
        // If room is provided, load room details for pre-selection
        if ($request->has('room')) {
            $preselectedRoom = Room::with('roomType')->find($request->room);
        }
        
        return view('admin.premium-booking.index', compact('roomTypes', 'existingBooking', 'preselectedRoom'));
    }

    /**
     * Pre-upload documents before booking creation (auto-upload on file select)
     */
    public function uploadDoc(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'field' => 'required|string|in:customer_photo,customer_nid_document,passport_document,visiting_card',
        ]);

        $path = $request->file('file')->store('bookings', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'field' => $request->input('field'),
        ]);
    }

    /**
     * Search for customer by phone, NID, or passport and return the most recently updated record
     */
    public function searchCustomer(Request $request)
    {
        $query = $request->input('query') ?? $request->input('phone');
        
        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Search query required']);
        }
        
        // Get the most recently updated booking matching phone, NID, or passport
        $booking = Booking::where('customer_phone', $query)
            ->orWhere('customer_nid', $query)
            ->orWhere('passport_number', $query)
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
        $checkIn = Carbon::parse($request->checkIn)->setTimeFromTimeString('12:00');
        $checkOut = Carbon::parse($request->checkOut)->setTimeFromTimeString('12:00');
        $roomTypeId = $request->roomTypeId;
        $excludeBookingId = $request->excludeBookingId; // Exclude this booking from conflict check
        $nights = $checkIn->diffInDays($checkOut);

        // Get all rooms
        $query = Room::with('roomType');
        
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }

        $rooms = $query->get();

        // Filter out rooms that are already booked for these dates/times
        $availableRooms = $rooms->filter(function ($room) use ($checkIn, $checkOut, $excludeBookingId) {
            $query = Booking::with('bookingRooms')
                ->whereNotIn('status', ['cancelled', 'checked_out'])
                ->where('check_in_date', '<', $checkOut->toDateString())
                ->where('check_out_date', '>', $checkIn->toDateString());

            if ($excludeBookingId) {
                $query->where('id', '!=', $excludeBookingId);
            }

            $hasConflict = $query->get()
                ->some(function ($booking) use ($checkIn, $checkOut, $room) {
                    if (!in_array($room->id, array_map('intval', $booking->getAllRoomIds()))) return false;
                    $existingCheckIn = $booking->getCheckInDateTime();
                    $existingCheckOut = $booking->getCheckOutDateTime();
                    return $existingCheckIn->lt($checkOut) && $existingCheckOut->gt($checkIn);
                });

            return !$hasConflict;
        });

        return response()->json([
            'availableRooms' => $availableRooms->values(),
            'nights' => $nights
        ]);
    }

    public function book(Request $request)
    {
        try {
            // DEBUG: Log all incoming request data
            \Log::info('=== BOOKING REQUEST START ===');
            \Log::info('All request data:', $request->all());
            \Log::info('rooms_data raw:', ['raw' => $request->input('rooms_data')]);
            \Log::info('room_id raw:', ['raw' => $request->input('room_id')]);
            
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
                
                // IMPORTANT: Strictly deduplicate rooms by roomId to prevent duplicates
                if (!empty($roomsData)) {
                    $uniqueRooms = [];
                    $seenRoomIds = [];
                    foreach ($roomsData as $room) {
                        $roomId = isset($room['roomId']) ? (int)$room['roomId'] : null;
                        if ($roomId && !in_array($roomId, $seenRoomIds, true)) {
                            $seenRoomIds[] = $roomId;
                            $room['roomId'] = $roomId; // Ensure it's integer
                            $uniqueRooms[] = $room;
                        } else {
                            \Log::warning('Duplicate room filtered on backend', ['room_id' => $roomId]);
                        }
                    }
                    $originalCount = count(json_decode($roomsDataRaw, true));
                    $roomsData = $uniqueRooms;
                    \Log::info('Room deduplication complete', [
                        'original_count' => $originalCount, 
                        'final_count' => count($roomsData),
                        'room_ids' => $seenRoomIds
                    ]);
                }
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
                'customer_photo' => 'nullable',
                'customer_photo.*' => 'nullable|image|max:5120',
                'customer_nid_document' => 'nullable',
                'customer_nid_document.*' => 'nullable|file|max:5120',
                'passport_document' => 'nullable',
                'passport_document.*' => 'nullable|file|max:5120',
                'visiting_card' => 'nullable',
                'visiting_card.*' => 'nullable|image|max:5120',
                'additional_guests' => 'nullable|array',
                'additional_guests.*.name' => 'nullable|string',
                'additional_guests.*.nid' => 'nullable|string',
                'additional_guests.*.phone' => 'nullable|string',
                'additional_guests.*.company_name' => 'nullable|string',
            ]);

            // Handle file uploads - support pre-uploaded paths and multiple files
            $docFields = ['customer_photo', 'customer_nid_document', 'passport_document', 'visiting_card'];
            foreach ($docFields as $field) {
                $paths = [];

                // Pre-uploaded paths from auto-upload (JSON array string)
                $prePaths = $request->input($field . '_paths');
                if ($prePaths) {
                    $decoded = json_decode($prePaths, true);
                    if (is_array($decoded)) {
                        $paths = array_merge($paths, $decoded);
                    }
                }

                // Direct file uploads (fallback - multiple files)
                if ($request->hasFile($field)) {
                    $files = $request->file($field);
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    foreach ($files as $f) {
                        if ($f && $f->isValid()) {
                            $paths[] = $f->store('bookings', 'public');
                        }
                    }
                }

                if (!empty($paths)) {
                    $validated[$field] = json_encode(array_values(array_unique($paths)));
                }
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

            // Set discount approval status
            $hasDiscount = ($validated['discount_type'] !== 'none' && ($validated['discount_amount'] > 0 || $validated['discount_percentage'] > 0));
            if ($hasDiscount) {
                if (Auth::user()->canApproveDiscounts()) {
                    $validated['discount_status'] = 'approved';
                    $validated['discount_approved_by'] = Auth::id();
                    $validated['discount_approved_at'] = now();
                } else {
                    $validated['discount_status'] = 'pending';
                    $validated['discount_requested_by'] = Auth::id();
                }
            }

            // Set payment status
            $validated['payment_status'] = $validated['advance_payment'] >= $validated['total_amount'] ? 'paid' : 
                                           ($validated['advance_payment'] > 0 ? 'partial' : 'pending');
            $validated['check_in_time'] = $validated['check_in_time'] ?? '12:00';
            $validated['check_out_time'] = $validated['check_out_time'] ?? '12:00';
            $validated['created_by_id'] = Auth::id();
            $validated['updated_by_id'] = Auth::id();

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

            // IMPORTANT: Validate rooms are actually available before creating booking
            $checkIn = Carbon::parse($validated['check_in_date'])->setTimeFromTimeString($validated['check_in_time']);
            $checkOut = Carbon::parse($validated['check_out_date'])->setTimeFromTimeString($validated['check_out_time']);
            $unavailableRooms = [];
            
            $roomIds = !empty($roomsData) ? collect($roomsData)->pluck('roomId')->toArray() : ($singleRoomId ? [$singleRoomId] : []);
            
            foreach ($roomIds as $roomId) {
                // Check if room is already booked for these dates/times
                $hasConflict = Booking::with('bookingRooms')
                    ->whereNotIn('status', ['cancelled', 'checked_out'])
                    ->where('check_in_date', '<', $checkOut->toDateString())
                    ->where('check_out_date', '>', $checkIn->toDateString())
                    ->get()
                    ->some(function ($booking) use ($checkIn, $checkOut, $roomId) {
                        if (!in_array((int)$roomId, array_map('intval', $booking->getAllRoomIds()))) return false;
                        $existingCheckIn = $booking->getCheckInDateTime();
                        $existingCheckOut = $booking->getCheckOutDateTime();
                        return $existingCheckIn && $existingCheckOut && $existingCheckIn->lt($checkOut) && $existingCheckOut->gt($checkIn);
                    });

                if ($hasConflict) {
                    $room = Room::find($roomId);
                    $unavailableRooms[] = $room ? $room->room_number : $roomId;
                }
            }
            
            if (!empty($unavailableRooms)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Room(s) ' . implode(', ', $unavailableRooms) . ' already booked for these dates. Please select different rooms or dates.'
                ], 409);
            }

            $booking = Booking::create($validated);

            // CRITICAL: Clear any orphaned booking_rooms that might exist for this booking ID
            // This can happen if an old booking with this ID was deleted but its booking_rooms weren't
            $orphanedCount = BookingRoom::where('booking_id', $booking->id)->count();
            if ($orphanedCount > 0) {
                \Log::warning('Found orphaned booking_rooms, deleting them', [
                    'booking_id' => $booking->id,
                    'orphaned_count' => $orphanedCount
                ]);
                BookingRoom::where('booking_id', $booking->id)->delete();
            }

            // Add rooms to booking_rooms table
            if (!empty($roomsData) && count($roomsData) > 0) {
                \Log::info('Adding rooms to booking', [
                    'booking_id' => $booking->id,
                    'room_count' => count($roomsData),
                    'room_ids' => collect($roomsData)->pluck('roomId')->toArray()
                ]);
                
                $addedRooms = [];
                foreach ($roomsData as $roomData) {
                    $roomId = (int)$roomData['roomId'];
                    // Skip if already added (extra safety)
                    if (in_array($roomId, $addedRooms, true)) {
                        \Log::warning('Skipping duplicate room in booking', ['booking_id' => $booking->id, 'room_id' => $roomId]);
                        continue;
                    }
                    BookingRoom::firstOrCreate(
                        ['booking_id' => $booking->id, 'room_id' => $roomId],
                        ['price_per_night' => $roomData['pricePerNight'] ?? 0]
                    );
                    $addedRooms[] = $roomId;
                }
                \Log::info('Rooms added to booking', ['booking_id' => $booking->id, 'rooms' => $addedRooms]);
            } elseif ($singleRoomId) {
                // Single room - still add to booking_rooms for consistency
                $room = Room::find($singleRoomId);
                BookingRoom::firstOrCreate(
                    ['booking_id' => $booking->id, 'room_id' => $singleRoomId],
                    ['price_per_night' => $room->price_per_night ?? $room->roomType->base_price ?? 0]
                );
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
            
            $checkIn = $booking->getCheckInDateTime();
            $checkOut = $booking->getCheckOutDateTime();
            $unavailableRooms = [];
            
            foreach ($roomsData as $roomData) {
                $roomId = $roomData['roomId'];
                
                // Check if room is already in this booking
                $exists = BookingRoom::where('booking_id', $bookingId)
                    ->where('room_id', $roomId)
                    ->exists();
                    
                if ($exists) continue;
                
                // Check conflict using datetime
                $hasConflict = Booking::with('bookingRooms')
                    ->where('id', '!=', $bookingId)
                    ->whereNotIn('status', ['cancelled', 'checked_out'])
                    ->where('check_in_date', '<', $checkOut->toDateString())
                    ->where('check_out_date', '>', $checkIn->toDateString())
                    ->get()
                    ->some(function ($other) use ($checkIn, $checkOut, $roomId) {
                        if (!in_array((int)$roomId, array_map('intval', $other->getAllRoomIds()))) return false;
                        $existingCheckIn = $other->getCheckInDateTime();
                        $existingCheckOut = $other->getCheckOutDateTime();
                        return $existingCheckIn && $existingCheckOut && $existingCheckIn->lt($checkOut) && $existingCheckOut->gt($checkIn);
                    });
                
                if ($hasConflict) {
                    $room = Room::find($roomId);
                    $unavailableRooms[] = $room ? $room->room_number : $roomId;
                }
            }
            
            if (!empty($unavailableRooms)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Room(s) ' . implode(', ', $unavailableRooms) . ' already booked for this booking period.'
                ], 409);
            }
            
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

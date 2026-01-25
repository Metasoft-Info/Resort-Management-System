<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PremiumBookingController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::all();
        return view('admin.premium-booking.index', compact('roomTypes'));
    }

    public function search(Request $request)
    {
        $checkIn = Carbon::parse($request->checkIn);
        $checkOut = Carbon::parse($request->checkOut);
        $roomTypeId = $request->roomTypeId;
        $nights = $checkIn->diffInDays($checkOut);

        // Get all rooms
        $query = Room::with('roomType')->where('status', 'available');
        
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        }

        $rooms = $query->get();

        // Filter out rooms that are already booked for these dates
        $availableRooms = $rooms->filter(function ($room) use ($checkIn, $checkOut) {
            $hasConflict = Booking::where('room_id', $room->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                              ->where('check_out_date', '>=', $checkOut);
                        });
                })
                ->exists();

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
            // Parse additional_guests if it's a JSON string
            $additionalGuestsRaw = $request->input('additional_guests');
            if (is_string($additionalGuestsRaw) && !empty($additionalGuestsRaw)) {
                $request->merge(['additional_guests' => json_decode($additionalGuestsRaw, true)]);
            }

            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'check_in_time' => 'nullable|string',
                'check_out_time' => 'nullable|string',
                'customer_name' => 'required|string',
                'customer_nid' => 'required|string',
                'customer_phone' => 'required|string',
                'customer_whatsapp' => 'nullable|string',
                'customer_email' => 'required|email',
                'passport_number' => 'nullable|string',
                'customer_address' => 'nullable|string',
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
                'advance_payment' => 'nullable|numeric|min:0',
                'remaining_payment' => 'nullable|numeric',
                'payment_method' => 'required|in:cash,card,mfs',
                'customer_photo' => 'nullable|image|max:2048',
                'customer_nid_document' => 'nullable|file|max:2048',
                'passport_document' => 'nullable|file|max:2048',
                'visiting_card' => 'nullable|image|max:2048',
                'additional_guests' => 'nullable|array',
                'additional_guests.*.name' => 'nullable|string',
                'additional_guests.*.nid' => 'nullable|string',
                'additional_guests.*.phone' => 'nullable|string',
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

            // Set payment status
            $validated['payment_status'] = $validated['advance_payment'] >= $validated['total_amount'] ? 'paid' : 
                                           ($validated['advance_payment'] > 0 ? 'partial' : 'pending');
            $validated['created_by_id'] = Auth::id();

            // Extract additional guests data before creating booking
            $additionalGuestsData = $request->input('additional_guests', []);
            if (isset($validated['additional_guests'])) {
                unset($validated['additional_guests']);
            }

            $booking = Booking::create($validated);

            // Add additional guests
            if (!empty($additionalGuestsData)) {
                foreach ($additionalGuestsData as $guestData) {
                    $booking->additionalGuests()->create([
                        'name' => $guestData['name'],
                        'nid' => $guestData['nid'],
                        'phone' => $guestData['phone'],
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

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'booking' => $booking
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Booking creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

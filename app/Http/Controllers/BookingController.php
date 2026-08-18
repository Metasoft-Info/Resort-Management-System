<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\AdditionalGuest;
use App\Models\BookingPayment;
use App\Models\ActivityLog;
use App\Mail\BookingConfirmationMail;
use App\Mail\CheckoutInvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with([
            'room.roomType',
            'bookingRooms.room.roomType',
            'payments',
            'createdBy',
            'discountApprovedBy',
            'discountRequestedBy',
        ]);
        
        // Status filter - hide checked_out by default
        if ($request->filled('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
            // If 'all' is selected, show all statuses including checked_out
        } else {
            // By default, hide checked_out bookings
            $query->where('status', '!=', 'checked_out');
        }
        
        // Payment status filter
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Discount approval status filter
        if ($request->filled('discount_status') && $request->discount_status !== 'all') {
            if ($request->discount_status === 'has_discount') {
                $query->where(function($q) {
                    $q->whereNotNull('discount_status')
                      ->orWhere('discount_amount', '>', 0)
                      ->orWhere(function($sq) {
                          $sq->where('discount_type', 'percentage')->where('discount_percentage', '>', 0);
                      });
                });
            } else {
                $query->where('discount_status', $request->discount_status);
            }
        }
        
        // Guest search functionality
        if ($request->filled('search')) {
            $searchValue = $request->input('search');
            $searchType = $request->input('type', 'name');
            
            switch ($searchType) {
                case 'phone':
                    $query->where(function($q) use ($searchValue) {
                        $q->where('customer_phone', 'like', "%{$searchValue}%")
                          ->orWhere('customer_nid', 'like', "%{$searchValue}%")
                          ->orWhere('passport_number', 'like', "%{$searchValue}%");
                    });
                    break;
                case 'email':
                    $query->where('customer_email', 'like', "%{$searchValue}%");
                    break;
                case 'name':
                    $query->where('customer_name', 'like', "%{$searchValue}%");
                    break;
            }
        }
        
        // Date range filters
        if ($request->filled('check_in_from')) {
            $query->whereDate('check_in_date', '>=', $request->check_in_from);
        }
        if ($request->filled('check_in_to')) {
            $query->whereDate('check_in_date', '<=', $request->check_in_to);
        }
        if ($request->filled('check_out_from')) {
            $query->whereDate('check_out_date', '>=', $request->check_out_from);
        }
        if ($request->filled('check_out_to')) {
            $query->whereDate('check_out_date', '<=', $request->check_out_to);
        }
        if ($request->filled('booking_from')) {
            $query->whereDate('created_at', '>=', $request->booking_from);
        }
        if ($request->filled('booking_to')) {
            $query->whereDate('created_at', '<=', $request->booking_to);
        }
        
        $bookings = $query->latest()->paginate(15);
        
        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($bookings);
        }
        
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('admin.bookings.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_name' => 'required',
            'customer_nid' => 'required',
            'customer_phone' => 'required',
            'customer_email' => 'required|email',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'advance_payment' => 'required|numeric',
            'payment_method' => 'required|in:cash,card,mfs,bkash',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'booking_purpose' => 'nullable|string|max:50',
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['updated_by_id'] = Auth::id();
        $validated['check_in_time'] = $validated['check_in_time'] ?? '12:00';
        $validated['check_out_time'] = $validated['check_out_time'] ?? '12:00';
        $validated['status'] = $validated['status'] ?? 'confirmed';

        // The amount typed by the browser is only a preview. Always snapshot
        // the room's published rate and calculate the room rent on the server.
        $room = Room::with('roomType')->findOrFail($validated['room_id']);
        $roomRate = $this->resolveRoomRate($room);
        $nights = max(1, Carbon::parse($validated['check_in_date'])
            ->diffInDays(Carbon::parse($validated['check_out_date'])));
        $validated['total_amount'] = round($roomRate * $nights, 2);

        // Check room availability at given time
        $checkIn = Carbon::parse($validated['check_in_date'])->setTimeFromTimeString($validated['check_in_time']);
        $checkOut = Carbon::parse($validated['check_out_date'])->setTimeFromTimeString($validated['check_out_time']);

        $hasConflict = Booking::with('bookingRooms')
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where('check_in_date', '<', $checkOut->toDateString())
            ->where('check_out_date', '>', $checkIn->toDateString())
            ->get()
            ->some(function ($booking) use ($checkIn, $checkOut, $validated) {
                if (!in_array((int)$validated['room_id'], array_map('intval', $booking->getAllRoomIds()))) return false;
                $existingCheckIn = $booking->getCheckInDateTime();
                $existingCheckOut = $booking->getCheckOutDateTime();
                return $existingCheckIn && $existingCheckOut && $existingCheckIn->lt($checkOut) && $existingCheckOut->gt($checkIn);
            });

        if ($hasConflict) {
            return back()
                ->withErrors(['check_in_date' => 'This room is already booked for the selected time. Please choose another room/date.'])
                ->withInput()
                ->with('error', 'This room is already booked.');
        }

        // Calculate discount at creation time for correct remaining/payment_status
        $discountAmount = 0;
        if (($validated['discount_type'] ?? 'none') === 'percentage' && ($validated['discount_percentage'] ?? 0) > 0) {
            $discountAmount = ($validated['total_amount'] * $validated['discount_percentage']) / 100;
        } elseif (($validated['discount_type'] ?? 'none') === 'flat' && ($validated['discount_amount'] ?? 0) > 0) {
            $discountAmount = $validated['discount_amount'];
        }

        $validated['remaining_payment'] = max(0, $validated['total_amount'] - $discountAmount - $validated['advance_payment']);
        $validated['payment_status'] = $validated['remaining_payment'] <= 0 ? 'paid' : ($validated['advance_payment'] > 0 ? 'partial' : 'pending');

        $booking = Booking::create($validated);

        $booking->bookingRooms()->create([
            'room_id' => $room->id,
            'price_per_night' => $roomRate,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
        ]);

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

        $booking->load('bookingRooms', 'payments');
        $booking->total_amount = $booking->getCalculatedTotal();
        $booking->vat_amount = $booking->getVatAmount();
        $booking->remaining_payment = max(0, $booking->getCalculatedRemaining());
        $booking->payment_status = $booking->getCalculatedPaymentStatus();
        $booking->save();
        
        ActivityLog::log('Created booking', 'Booking', $booking->id, [
            'customer_name' => $booking->customer_name,
            'room_id' => $booking->room_id,
            'total_amount' => $booking->total_amount
        ]);
        
        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully');
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'room.roomType', 
            'createdBy', 
            'additionalGuests', 
            'payments.recordedBy',
            'foodPackage',
            'bookingRooms.room.roomType'
        ]);
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->has('ajax')) {
            return response()->json([
                'success' => true,
                'booking' => $booking,
                'financials' => $booking->getFinancialBreakdown(),
            ]);
        }
        
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $oldStatus = $booking->status;
        $newStatus = $validated['status'];
        $today = Carbon::now()->startOfDay();
        $checkInDate = Carbon::parse($booking->check_in_date)->startOfDay();
        $checkOutDate = Carbon::parse($booking->check_out_date)->startOfDay();

        // Prevent check-in before check-in date
        if ($newStatus === 'checked_in' && $checkInDate->gt($today)) {
            return response()->json(['message' => 'Check-in not allowed before ' . $checkInDate->format('d M Y')], 422);
        }

        // Prevent cancel after check-out
        if ($newStatus === 'cancelled' && $oldStatus === 'checked_out') {
            return response()->json(['message' => 'Cannot cancel a checked-out booking'], 422);
        }

        // Prevent any status change after check-out, EXCEPT re-check-in for extended stays
        // Extended stay = checked_out but checkout date is still in the future (date was extended)
        $isExtendedStay = $oldStatus === 'checked_out' && $checkOutDate->gt($today);
        if ($oldStatus === 'checked_out' && $newStatus !== 'checked_out' && !$isExtendedStay) {
            return response()->json(['message' => 'Cannot modify a checked-out booking'], 422);
        }
        if ($oldStatus === 'checked_out' && $isExtendedStay && $newStatus !== 'checked_in') {
            return response()->json(['message' => 'Cannot modify a checked-out booking. Use Re-Check In for extended stays.'], 422);
        }

        $booking->update(['status' => $newStatus, 'updated_by_id' => Auth::id()]);

        // Mark room(s) as occupied when checking in
        if ($validated['status'] === 'checked_in' && $oldStatus !== 'checked_in') {
            $roomIds = $booking->getAllRoomIds();
            if (!empty($roomIds)) {
                Room::whereIn('id', $roomIds)->update(['status' => 'occupied']);
            }
        }

        // Free up room(s) when checking out
        if ($validated['status'] === 'checked_out' && $oldStatus !== 'checked_out') {
            $roomIds = $booking->getAllRoomIds();
            if (!empty($roomIds)) {
                Room::whereIn('id', $roomIds)->update(['status' => 'available']);
            }
        }
        
        ActivityLog::log('Updated booking status', 'Booking', $booking->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status']
        ]);

        // Send email notifications based on status change
        $emailSent = false;
        $emailError = null;
        
        if ($booking->customer_email) {
            try {
                if ($validated['status'] === 'confirmed' && $oldStatus !== 'confirmed') {
                    // Send booking confirmation email
                    Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));
                    $emailSent = true;
                    Log::info("Booking confirmation email sent to {$booking->customer_email} for booking #{$booking->id}");
                } elseif ($validated['status'] === 'checked_out' && $oldStatus !== 'checked_out') {
                    // Send checkout invoice email
                    Mail::to($booking->customer_email)->send(new CheckoutInvoiceMail($booking));
                    $emailSent = true;
                    Log::info("Checkout invoice email sent to {$booking->customer_email} for booking #{$booking->id}");
                }
            } catch (\Exception $e) {
                $emailError = $e->getMessage();
                Log::error("Failed to send email for booking #{$booking->id}: " . $e->getMessage());
            }
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            $response = ['message' => 'Status updated successfully'];
            if ($emailSent) {
                $response['email_sent'] = true;
                $response['message'] .= '. Email notification sent.';
            } elseif ($emailError) {
                $response['email_error'] = $emailError;
            }
            return response()->json($response);
        }
        
        $successMessage = 'Status updated successfully';
        if ($emailSent) {
            $successMessage .= '. Email sent.';
        }
        
        return back()->with('success', $successMessage);
    }

    public function updateTime(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'check_in_date' => 'nullable|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
        ]);

        // Determine new date/time values
        $newCheckInDate = $validated['check_in_date'] ?? $booking->check_in_date?->format('Y-m-d');
        $newCheckOutDate = $validated['check_out_date'] ?? $booking->check_out_date?->format('Y-m-d');
        $newCheckInTime = $validated['check_in_time'] ?? $booking->check_in_time ?? '12:00';
        $newCheckOutTime = $validated['check_out_time'] ?? $booking->check_out_time ?? '12:00';

        $newCheckIn = Carbon::parse($newCheckInDate)->setTimeFromTimeString($newCheckInTime);
        $newCheckOut = Carbon::parse($newCheckOutDate)->setTimeFromTimeString($newCheckOutTime);

        $oldCheckIn = $booking->getCheckInDateTime();
        $oldCheckOut = $booking->getCheckOutDateTime();

        $datesChanged = !$newCheckIn->equalTo($oldCheckIn) || !$newCheckOut->equalTo($oldCheckOut);

        // Check room availability if dates changed
        if ($datesChanged) {
            $roomIds = $booking->getAllRoomIds();
            $conflicts = Booking::with(['room.roomType', 'bookingRooms.room'])
                ->where('id', '!=', $booking->id)
                ->whereNotIn('status', ['cancelled', 'checked_out'])
                ->where('check_in_date', '<', $newCheckOut->toDateString())
                ->where('check_out_date', '>', $newCheckIn->toDateString())
                ->get()
                ->filter(function ($other) use ($newCheckIn, $newCheckOut, $roomIds) {
                    $otherRoomIds = array_map('intval', $other->getAllRoomIds());
                    if (empty(array_intersect($roomIds, $otherRoomIds))) return false;
                    $otherCheckIn = $other->getCheckInDateTime();
                    $otherCheckOut = $other->getCheckOutDateTime();
                    return $otherCheckIn->lt($newCheckOut) && $otherCheckOut->gt($newCheckIn);
                })
                ->values();

            if ($conflicts->isNotEmpty()) {
                $conflictData = $conflicts->map(function ($c) {
                    $rooms = $c->getAllRooms()->pluck('room_number')->implode(', ');
                    return [
                        'id' => $c->id,
                        'customer_name' => $c->customer_name,
                        'customer_phone' => $c->customer_phone,
                        'rooms' => $rooms,
                        'check_in' => $c->check_in_date?->format('d M Y'),
                        'check_out' => $c->check_out_date?->format('d M Y'),
                        'status' => $c->status,
                    ];
                });
                return response()->json([
                    'message' => 'Room is already booked for this date.',
                    'conflicts' => $conflictData,
                ], 422);
            }
        }

        $validated['updated_by_id'] = Auth::id();

        DB::transaction(function () use ($booking, $validated, $oldCheckIn, $oldCheckOut, $newCheckIn, $newCheckOut, $datesChanged) {
            if ($datesChanged) {
                $this->ensureBookingRoomForLegacyBooking($booking, $oldCheckIn, $oldCheckOut);
            }

            $booking->update($validated);

            // The total is calculated from booking_rooms when those rows
            // exist. Keep their date range in sync with the parent booking;
            // otherwise extending a booking changes the displayed dates but
            // leaves the old number of nights in the room-level calculation.
            if ($datesChanged || $booking->bookingRooms()->exists()) {
                $this->syncBookingRoomDates($booking, $oldCheckIn, $newCheckIn, $newCheckOut);
                $this->syncBookingTotals($booking);
            }
        });

        // If booking was checked_out but checkout date/time is now in the future, re-check-in
        if ($booking->status === 'checked_out') {
            $now = Carbon::now('Asia/Dhaka');
            $checkOut = $booking->getCheckOutDateTime();
            if ($checkOut && $now->lt($checkOut)) {
                $booking->update(['status' => 'checked_in']);
                // Mark rooms as occupied again
                $roomIds = $booking->getAllRoomIds();
                if (!empty($roomIds)) {
                    \App\Models\Room::whereIn('id', $roomIds)->update(['status' => 'occupied']);
                }
                return response()->json(['message' => 'Date/Time updated. Booking re-checked-in as checkout time is in the future.']);
            }
        }

        return response()->json(['message' => 'Date/Time updated successfully']);
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'method' => 'required|in:cash,card,mfs,bkash',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'discount_type' => 'nullable|in:none,flat,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $paymentAmount = floatval($validated['amount'] ?? 0);

        // Recalculate current balance from the canonical room rows.
        $currentRemaining = max(0, $booking->getCalculatedRemaining());

        // Handle payment-level discount if provided
        $paymentDiscountAmount = 0;
        if (isset($validated['discount_type']) && $validated['discount_type'] !== 'none') {
            if ($validated['discount_type'] === 'flat') {
                $paymentDiscountAmount = floatval($validated['discount_amount'] ?? 0);
            } elseif ($validated['discount_type'] === 'percentage') {
                // Percentage discount on remaining amount
                $paymentDiscountAmount = ($currentRemaining * floatval($validated['discount_percentage'] ?? 0)) / 100;
            }
        }

        // At least one must be provided: payment or discount
        if ($paymentAmount <= 0 && $paymentDiscountAmount <= 0) {
            return response()->json(['message' => 'Please enter payment amount or discount'], 422);
        }

        // Validate: payment + discount cannot exceed remaining balance
        $totalDeduction = $paymentAmount + $paymentDiscountAmount;
        if ($totalDeduction > $currentRemaining) {
            return response()->json(['message' => 'Payment + discount cannot exceed remaining balance. Remaining: BDT ' . number_format($currentRemaining, 2)], 422);
        }

        // Update booking data
        $updateData = [
            'payment_method' => $validated['method'],
        ];
        
        if (!empty($validated['bkash_number'])) {
            $updateData['bkash_number'] = $validated['bkash_number'];
        }
        if (!empty($validated['bank_name'])) {
            $updateData['bank_name'] = $validated['bank_name'];
        }
        if (!empty($validated['discount_reference'])) {
            $updateData['discount_reference'] = $validated['discount_reference'];
        }
        
        // Update discount on booking if payment includes discount
        if ($paymentDiscountAmount > 0) {
            // Flatten the existing discount into one canonical amount before
            // adding a payment-level discount; otherwise an existing
            // percentage discount would be silently discarded.
            $updateData['discount_amount'] = $booking->getDiscountAmount() + $paymentDiscountAmount;
            $updateData['discount_type'] = 'flat';

            // Set discount approval status
            if (Auth::user()->canApproveDiscounts()) {
                $updateData['discount_status'] = 'approved';
                $updateData['discount_approved_by'] = Auth::id();
                $updateData['discount_approved_at'] = now();
            } else {
                $updateData['discount_status'] = 'pending';
                $updateData['discount_requested_by'] = Auth::id();
            }
        }

        // Apply a payment-level discount before recalculating the balance.
        // Do NOT update advance_payment; it remains the initial booking payment.
        $booking->update($updateData);

        // Record additional payment in booking_payments table.
        if ($paymentAmount > 0) {
            $booking->payments()->create([
                'amount' => $paymentAmount,
                'method' => $validated['method'],
                'type' => 'payment',
                'note' => $validated['note'] ?? null,
                'recorded_by_id' => Auth::id(),
            ]);
        }

        // Recalculate from the updated discount and all payment records.
        $booking->unsetRelation('payments');
        $this->syncBookingTotals($booking);

        $paymentNote = $validated['note'] ?? '';
        if ($paymentDiscountAmount > 0) {
            $paymentNote .= ' [Discount: BDT ' . number_format($paymentDiscountAmount, 2) . ' - Ref: ' . ($validated['discount_reference'] ?? 'N/A') . ']';
        }

        $message = 'Payment recorded successfully';
        if ($paymentDiscountAmount > 0 && $paymentAmount <= 0) {
            $message = 'Discount of BDT ' . number_format($paymentDiscountAmount, 2) . ' applied successfully';
        } elseif ($paymentDiscountAmount > 0) {
            $message = 'Payment of BDT ' . number_format($paymentAmount, 2) . ' with discount of BDT ' . number_format($paymentDiscountAmount, 2) . ' recorded';
        }

        return response()->json(['message' => $message]);
    }

    public function addExtraCharges(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'items' => 'nullable|array',
            'items.*.category_id' => 'nullable|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric',
        ]);

        $newExtraCharges = ($booking->extra_charges ?? 0) + $validated['amount'];
        $newDescription = $booking->extra_charges_description
            ? $booking->extra_charges_description . '; ' . $validated['description']
            : $validated['description'];

        // Store structured extra charge items
        $existingData = $booking->extra_charges_data ?? [];
        if (!empty($validated['items'])) {
            $existingData = array_merge($existingData, $validated['items']);
        }

        $booking->update([
            'extra_charges' => $newExtraCharges,
            'extra_charges_description' => $newDescription,
            'extra_charges_data' => $existingData,
            'updated_by_id' => Auth::id(),
        ]);

        $this->syncBookingTotals($booking);

        return response()->json(['message' => 'Extra charges added successfully']);
    }

    public function updateCustomer(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:20',
            'customer_nid'      => 'nullable|string|max:50',
            'customer_email'    => 'nullable|email|max:255',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address'  => 'nullable|string|max:500',
            'company_name'      => 'nullable|string|max:255',
            'passport_number'   => 'nullable|string|max:50',
            'reference_name'    => 'nullable|string|max:255',
            'reference_phone'   => 'nullable|string|max:20',
            'customer_photo.*'        => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'customer_nid_document.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'passport_document.*'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'visiting_card.*'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remove_customer_photo'        => 'nullable|array',
            'remove_customer_photo.*'      => 'string',
            'remove_customer_nid_document' => 'nullable|array',
            'remove_customer_nid_document.*' => 'string',
            'remove_passport_document'     => 'nullable|array',
            'remove_passport_document.*'   => 'string',
            'remove_visiting_card'         => 'nullable|array',
            'remove_visiting_card.*'     => 'string',
        ]);

        $updateData = collect($validated)->except([
            'customer_photo','customer_nid_document','passport_document','visiting_card',
            'remove_customer_photo','remove_customer_nid_document','remove_passport_document','remove_visiting_card'
        ])->toArray();

        // Handle document fields: merge kept existing + new uploads
        foreach (['customer_photo','customer_nid_document','passport_document','visiting_card'] as $field) {
            $docs = $booking->getDocuments($field);
            $removeKey = 'remove_' . $field;

            // Remove selected existing documents
            if ($request->has($removeKey)) {
                $toRemove = $request->input($removeKey, []);
                $docs = array_values(array_diff($docs, $toRemove));
            }

            // Add newly uploaded files
            if ($request->hasFile($field)) {
                foreach ($request->file($field) as $file) {
                    $path = $file->store('bookings/documents', 'public');
                    $docs[] = $path;
                }
            }

            $updateData[$field] = array_values(array_filter($docs));
        }

        $updateData['updated_by_id'] = Auth::id();
        $booking->update($updateData);

        ActivityLog::log('Updated customer info', 'Booking', $booking->id, ['booking_id' => $booking->id]);

        return response()->json(['message' => 'Customer information updated successfully']);
    }

    public function addGuest(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'nid' => 'required|string',
            'phone' => 'required|string',
        ]);

        $booking->additionalGuests()->create($validated);
        return response()->json(['message' => 'Guest added successfully']);
    }

    public function processRefund(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,mfs',
            'note' => 'nullable|string',
        ]);

        // Prevent refund after check-out
        if ($booking->status === 'checked_out') {
            return response()->json(['message' => 'Refund not allowed after check-out'], 422);
        }

        $totalDeposited = $booking->getTotalDeposited();
        if ($validated['amount'] > $totalDeposited) {
            return response()->json(['message' => 'Refund amount cannot exceed total deposited: ' . number_format($totalDeposited, 2)], 422);
        }

        // Record refund payment first
        $booking->payments()->create([
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'type' => 'refund',
            'note' => $validated['note'],
            'recorded_by_id' => Auth::id(),
        ]);

        // A refund reduces deposited money, so rebuild the balance and status
        // from all payment records (including the new refund row).
        $booking->unsetRelation('payments');
        $this->syncBookingTotals($booking);

        return response()->json(['message' => 'Refund processed successfully']);
    }

    public function updateVat(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'vat_enabled' => 'required|boolean',
        ]);

        $booking->update([
            'vat_enabled' => $validated['vat_enabled'],
        ]);

        $this->syncBookingTotals($booking);

        return response()->json(['message' => 'VAT updated successfully']);
    }

    private function calculateGrandTotal(Booking $booking): float
    {
        return $booking->getGrandTotal();
    }

    private function resolveRoomRate(Room $room): float
    {
        return (float) ($room->price_per_night !== null
            ? $room->price_per_night
            : ($room->roomType?->base_price ?? 0));
    }

    public function edit(Booking $booking)
    {
        $rooms = Room::all();
        return view('admin.bookings.edit', compact('booking', 'rooms'));
    }

    public function update(Request $request, Booking $booking)
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_nid' => 'nullable|string|max:50',
                'customer_address' => 'nullable|string',
                'company_name' => 'nullable|string|max:255',
                'booking_purpose' => 'nullable|string|max:50',
                'number_of_guests' => 'nullable|integer|min:1',
                'reference_name' => 'nullable|string|max:255',
                'reference_phone' => 'nullable|string|max:20',
                'total_amount' => 'nullable|numeric|min:0',
                'advance_payment' => 'nullable|numeric|min:0',
                'remaining_payment' => 'nullable|numeric|min:0',
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
                'payment_status' => 'sometimes|in:pending,partial,paid,refunded',
                'notes' => 'nullable|string',
            ]);

            $roomChanged = (int) $validated['room_id'] !== (int) $booking->room_id;
            $oldRoomId = $booking->room_id;
            $oldStatus = $booking->status;
            $oldCheckIn = $booking->getCheckInDateTime();
            $oldCheckOut = $booking->getCheckOutDateTime();
            $newCheckInTime = $validated['check_in_time'] ?? $booking->check_in_time ?? '12:00';
            $newCheckOutTime = $validated['check_out_time'] ?? $booking->check_out_time ?? '12:00';
            $newCheckIn = \Carbon\Carbon::parse($validated['check_in_date'])->setTimeFromTimeString($newCheckInTime);
            $newCheckOut = \Carbon\Carbon::parse($validated['check_out_date'])->setTimeFromTimeString($newCheckOutTime);

            $datesChanged = !$newCheckIn->equalTo($oldCheckIn) || !$newCheckOut->equalTo($oldCheckOut);
            $checkoutExtended = $newCheckOut->gt($oldCheckOut);

            $mustCheckAvailability = $roomChanged || !$newCheckIn->equalTo($oldCheckIn) || $checkoutExtended;

            if ($mustCheckAvailability) {
                $hasConflict = Booking::with('bookingRooms')
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where('check_in_date', '<', $newCheckOut->toDateString())
                    ->where('check_out_date', '>', $newCheckIn->toDateString())
                    ->get()
                    ->some(function ($other) use ($newCheckIn, $newCheckOut, $validated) {
                        if (!in_array((int)$validated['room_id'], array_map('intval', $other->getAllRoomIds()))) return false;
                        $otherCheckIn = $other->getCheckInDateTime();
                        $otherCheckOut = $other->getCheckOutDateTime();
                        return $otherCheckIn->lt($newCheckOut) && $otherCheckOut->gt($newCheckIn);
                    });

                if ($hasConflict) {
                    return back()
                        ->withErrors(['check_out_date' => 'This room is already booked for the selected date. Please choose another room/date.'])
                        ->withInput()
                        ->with('error', 'Room is already booked for this date.');
                }
            }

            if ($roomChanged || $datesChanged || !isset($validated['total_amount']) || $validated['total_amount'] === null || $validated['total_amount'] === '') {
                $room = \App\Models\Room::with('roomType')->find($validated['room_id']);

                if (!$room) {
                    return back()
                        ->withErrors(['room_id' => 'Selected room not found.'])
                        ->withInput();
                }

                $nights = max(1, $newCheckIn->diffInDays($newCheckOut));
                $roomPrice = $this->resolveRoomRate($room);
                $validated['total_amount'] = max(0, $roomPrice * $nights);
            } else {
                // Do not let an editable browser field overwrite the stored
                // agreed amount when the room and dates did not change.
                $validated['total_amount'] = (float) $booking->getCalculatedTotal();
            }

            $advancePayment = isset($validated['advance_payment']) ? (float) $validated['advance_payment'] : (float) ($booking->advance_payment ?? 0);
            $validated['advance_payment'] = max(0, $advancePayment);

            // Calculate discount from validated or existing booking
            $discountType = $validated['discount_type'] ?? $booking->discount_type ?? 'none';
            $discountAmount = $validated['discount_amount'] ?? $booking->discount_amount ?? 0;
            $discountPercentage = $validated['discount_percentage'] ?? $booking->discount_percentage ?? 0;
            $discountValue = 0;
            if ($discountType === 'percentage' && $discountPercentage > 0) {
                $discountValue = ((float) $validated['total_amount'] * $discountPercentage) / 100;
            } elseif ($discountType === 'flat' && $discountAmount > 0) {
                $discountValue = (float) $discountAmount;
            }

            $validated['remaining_payment'] = max(0, (float) $validated['total_amount'] - $discountValue - (float) $validated['advance_payment']);

            if (!isset($validated['payment_status']) || $validated['payment_status'] === null || $validated['payment_status'] === '') {
                $validated['payment_status'] = $validated['remaining_payment'] <= 0
                    ? 'paid'
                    : ($validated['advance_payment'] > 0 ? 'partial' : 'pending');
            }

            static $bookingColumns = null;
            if ($bookingColumns === null) {
                $bookingColumns = Schema::getColumnListing('bookings');
            }

            $filteredValidated = array_intersect_key($validated, array_flip($bookingColumns));
            $filteredValidated['updated_by_id'] = Auth::id();

            if (!array_key_exists('room_id', $filteredValidated)) {
                $filteredValidated['room_id'] = $booking->room_id;
            }

            $booking->update($filteredValidated);

            if ($datesChanged) {
                $this->ensureBookingRoomForLegacyBooking($booking, $oldCheckIn, $oldCheckOut);
            }

            // Update booking_rooms table when room is changed
            if ($roomChanged) {
                // Remove old room from booking_rooms
                \App\Models\BookingRoom::where('booking_id', $booking->id)
                    ->where('room_id', $oldRoomId)
                    ->delete();

                // Add new room to booking_rooms
                $room = \App\Models\Room::find($validated['room_id']);
                if ($room) {
                    \App\Models\BookingRoom::updateOrCreate(
                        ['booking_id' => $booking->id, 'room_id' => $validated['room_id']],
                        [
                            'price_per_night' => $room->price_per_night ?? $room->roomType->base_price ?? 0,
                            'check_in_date' => $validated['check_in_date'],
                            'check_out_date' => $validated['check_out_date'],
                        ]
                    );
                }

                // Update notes
                $allRoomNumbers = $booking->getAllRooms()->pluck('room_number')->implode(', ');
                $booking->notes = preg_replace('/\[Rooms:.*?\]/', '', $booking->notes ?? '');
                $booking->notes = trim($booking->notes) . " [Rooms: {$allRoomNumbers}]";
                $booking->save();
            }

            // Auto re-check-in if checkout date was extended and booking was checked_out
            if ($checkoutExtended && $oldStatus === 'checked_out') {
                $newCheckOutDate = \Carbon\Carbon::parse($validated['check_out_date'])->startOfDay();
                $today = Carbon::now()->startOfDay();
                if ($newCheckOutDate->gt($today)) {
                    $booking->update(['status' => 'checked_in', 'updated_by_id' => Auth::id()]);
                    // Mark room(s) as occupied
                    $roomIds = $booking->getAllRoomIds();
                    if (!empty($roomIds)) {
                        \App\Models\Room::whereIn('id', $roomIds)->update(['status' => 'occupied']);
                    }
                }
            }

            // Date changes must also update normalized room rows. Otherwise
            // getCalculatedTotal() continues using the old room-level dates.
            if ($datesChanged && !$roomChanged) {
                $this->syncBookingRoomDates($booking, $oldCheckIn, $newCheckIn, $newCheckOut);
            }

            // Always refresh the balance. Customer-only edits must not reset
            // a booking's due to the old advance amount when later payments
            // already exist.
            $this->syncBookingTotals($booking);

            return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated successfully' . ($checkoutExtended && $oldStatus === 'checked_out' ? ' — Status auto-updated to checked-in' : ''));
        } catch (\Throwable $e) {
            Log::error('Booking update failed', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            try {
                static $bookingColumnsFallback = null;
                if ($bookingColumnsFallback === null) {
                    $bookingColumnsFallback = Schema::getColumnListing('bookings');
                }

                $minimal = [
                    'room_id' => $request->input('room_id', $booking->room_id),
                    'check_in_date' => $request->input('check_in_date', $booking->check_in_date),
                    'check_out_date' => $request->input('check_out_date', $booking->check_out_date),
                    'customer_name' => $request->input('customer_name', $booking->customer_name),
                    'customer_phone' => $request->input('customer_phone', $booking->customer_phone),
                    'customer_email' => $request->input('customer_email', $booking->customer_email),
                    'customer_nid' => $request->input('customer_nid', $booking->customer_nid),
                    'status' => $request->input('status', $booking->status),
                ];

                $minimalFiltered = array_intersect_key($minimal, array_flip($bookingColumnsFallback));
                $minimalFiltered['updated_by_id'] = Auth::id();

                if (!empty($minimalFiltered)) {
                    $booking->update($minimalFiltered);

                    return redirect()->route('admin.bookings.show', $booking)
                        ->with('success', 'Booking partially updated (production compatibility mode).');
                }
            } catch (\Throwable $fallbackError) {
                Log::error('Booking update fallback failed', [
                    'booking_id' => $booking->id,
                    'user_id' => Auth::id(),
                    'error' => $fallbackError->getMessage(),
                ]);
            }

            return back()
                ->withInput()
                ->with('error', 'Booking update failed. Please check the data and try again.');
        }
    }

    /**
     * Keep booking_rooms dates aligned with a booking edited from the global
     * date/time controls. A room added later may have a custom check-in date,
     * so only rows that used the original parent check-in date are moved.
     * Checkout is shared by the booking and must always follow an extension.
     */
    private function syncBookingRoomDates(
        Booking $booking,
        Carbon $oldCheckIn,
        Carbon $newCheckIn,
        Carbon $newCheckOut
    ): void {
        $oldCheckInDate = $oldCheckIn->toDateString();
        $newCheckInDate = $newCheckIn->toDateString();
        $newCheckOutDate = $newCheckOut->toDateString();

        foreach ($booking->bookingRooms()->get() as $bookingRoom) {
            $roomCheckInDate = $bookingRoom->check_in_date?->toDateString();
            $updates = [
                // All rooms in a booking share the parent's checkout. This
                // also repairs rows left behind by an earlier date update.
                'check_out_date' => $newCheckOutDate,
            ];

            if ($roomCheckInDate === null || $roomCheckInDate === $oldCheckInDate) {
                $updates['check_in_date'] = $newCheckInDate;
            }

            $bookingRoom->update($updates);
        }
    }

    /**
     * Older single-room bookings may not have a booking_rooms row. Normalize
     * those records before a date change so an extension can use the original
     * agreed nightly rate instead of leaving total_amount frozen.
     */
    private function ensureBookingRoomForLegacyBooking(
        Booking $booking,
        Carbon $oldCheckIn,
        Carbon $oldCheckOut
    ): void {
        if ($booking->bookingRooms()->exists() || !$booking->room_id) {
            return;
        }

        $room = $booking->room()->with('roomType')->first();
        if (!$room) {
            return;
        }

        $oldNights = max(1, $oldCheckIn->diffInDays($oldCheckOut));
        $storedTotal = (float) ($booking->total_amount ?? 0);
        $pricePerNight = $storedTotal > 0
            ? $storedTotal / $oldNights
            : (float) ($room->price_per_night ?? $room->roomType?->base_price ?? 0);

        \App\Models\BookingRoom::firstOrCreate(
            ['booking_id' => $booking->id, 'room_id' => $booking->room_id],
            [
                'price_per_night' => $pricePerNight,
                'check_in_date' => $oldCheckIn->toDateString(),
                'check_out_date' => $oldCheckOut->toDateString(),
            ]
        );
    }

    /**
     * Persist the canonical room total and payment balance after a date or
     * room assignment change.
     */
    private function syncBookingTotals(Booking $booking): void
    {
        $booking->unsetRelation('bookingRooms');
        $booking->unsetRelation('payments');
        $booking->total_amount = (float) $booking->getCalculatedTotal();
        $booking->vat_amount = (float) $booking->getVatAmount();
        $booking->remaining_payment = max(0, (float) $booking->getCalculatedRemaining());
        $booking->payment_status = $booking->getCalculatedPaymentStatus();
        $booking->updated_by_id = Auth::id();
        $booking->save();
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully');
    }

    public function sendInvoiceEmail(Request $request, Booking $booking)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        try {
            Mail::to($email)->send(new CheckoutInvoiceMail($booking));
            return response()->json([
                'success' => true,
                'message' => 'Invoice email sent successfully to ' . $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email manually: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function sendReservationEmail(Request $request, Booking $booking)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        try {
            Mail::to($email)->send(new BookingConfirmationMail($booking));
            return response()->json([
                'success' => true,
                'message' => 'Reservation email sent successfully to ' . $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send reservation email manually: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function removeRoom(Booking $booking, $roomId)
    {
        if (in_array($booking->status, ['checked_out', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove room from ' . $booking->status . ' booking'
            ], 400);
        }

        $bookingRoom = \App\Models\BookingRoom::where('booking_id', $booking->id)
            ->where('room_id', $roomId)
            ->first();

        $legacyRoomMatches = (int) $booking->room_id === (int) $roomId;

        if (!$bookingRoom && !$legacyRoomMatches) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found in this booking'
            ], 404);
        }

        if ($bookingRoom) {
            $bookingRoom->delete();
        }

        // Mixed historical records can point to the same room from both
        // columns. Remove both representations together.
        if ($legacyRoomMatches) {
            $booking->room_id = null;
        }

        // Recalculate from the remaining canonical rows. Do not refresh here:
        // refresh would restore the legacy room_id we just cleared.
        $booking->unsetRelation('bookingRooms');
        $booking->load('bookingRooms', 'payments');
        $booking->total_amount = $booking->getCalculatedTotal();
        $booking->vat_amount = $booking->getVatAmount();
        $booking->remaining_payment = max(0, $booking->getCalculatedRemaining());
        $booking->payment_status = $booking->getCalculatedPaymentStatus();

        // Update notes
        $allRoomNumbers = $booking->getAllRooms()->pluck('room_number')->implode(', ');
        $booking->notes = preg_replace('/\[Rooms:.*?\]/', '', $booking->notes ?? '');
        $booking->notes = trim($booking->notes) . " [Rooms: {$allRoomNumbers}]";
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Room removed successfully'
        ]);
    }
}

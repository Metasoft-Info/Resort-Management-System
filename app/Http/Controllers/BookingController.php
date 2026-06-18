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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('room.roomType', 'createdBy', 'discountApprovedBy', 'discountRequestedBy');
        
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
            'payment_method' => 'required|in:cash,card,mfs',
            'booking_purpose' => 'nullable|string|max:50',
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['updated_by_id'] = Auth::id();
        $validated['remaining_payment'] = $validated['total_amount'] - $validated['advance_payment'];
        $validated['payment_status'] = $validated['advance_payment'] >= $validated['total_amount'] ? 'paid' : 'partial';

        $booking = Booking::create($validated);

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
                'booking' => $booking
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

        // Prevent check-in before check-in date
        if ($newStatus === 'checked_in' && $checkInDate->gt($today)) {
            return response()->json(['message' => 'Check-in not allowed before ' . $checkInDate->format('d M Y')], 422);
        }

        // Prevent cancel after check-out
        if ($newStatus === 'cancelled' && $oldStatus === 'checked_out') {
            return response()->json(['message' => 'Cannot cancel a checked-out booking'], 422);
        }

        // Prevent any status change after check-out (except keeping checked_out)
        if ($oldStatus === 'checked_out' && $newStatus !== 'checked_out') {
            return response()->json(['message' => 'Cannot modify a checked-out booking'], 422);
        }

        $booking->update(['status' => $newStatus, 'updated_by_id' => Auth::id()]);

        // Free up room(s) when checking out
        if ($validated['status'] === 'checked_out' && $oldStatus !== 'checked_out') {
            $roomIds = [];
            if ($booking->room_id) {
                $roomIds[] = $booking->room_id;
            }
            foreach ($booking->bookingRooms as $bookingRoom) {
                $roomIds[] = $bookingRoom->room_id;
            }
            $roomIds = array_unique($roomIds);
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
        
        $successMessage = 'স্ট্যাটাস সফলভাবে আপডেট হয়েছে';
        if ($emailSent) {
            $successMessage .= '. ইমেইল প্রেরিত হয়েছে।';
        }
        
        return back()->with('success', $successMessage);
    }

    public function updateTime(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
        ]);

        $validated['updated_by_id'] = Auth::id();
        $booking->update($validated);
        return response()->json(['message' => 'Time updated successfully']);
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'method' => 'required|in:cash,card,mfs',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'discount_type' => 'nullable|in:none,flat,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $paymentAmount = floatval($validated['amount'] ?? 0);

        // Recalculate current balances from rooms + discount + extra + VAT.
        $baseAmount = $booking->getCalculatedTotal();
        $currentGrandTotal = $booking->getGrandTotal();
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
            return response()->json(['message' => 'পেমেন্ট ও ডিসকাউন্টের মোট বাকি টাকার চেয়ে বেশি হতে পারে না। বাকি আছে: ৳' . number_format($currentRemaining, 2)], 422);
        }

        // Calculate grand total (including VAT, existing discount, extra charges)
        $existingDiscountAmount = 0;
        
        if ($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
            $existingDiscountAmount = ($baseAmount * $booking->discount_percentage) / 100;
        } elseif ($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
            $existingDiscountAmount = $booking->discount_amount;
        }

        // Calculate with NEW discount added
        $totalDiscountAmount = $existingDiscountAmount + $paymentDiscountAmount;
        $afterDiscount = max(0, $baseAmount - $totalDiscountAmount);
        $extraCharges = $booking->extra_charges ?? 0;
        $vatAmount = $booking->vat_enabled ? ($afterDiscount * 0.15) : 0;
        $grandTotal = $afterDiscount + $extraCharges + $vatAmount;

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
            $updateData['discount_amount'] = ($booking->discount_amount ?? 0) + $paymentDiscountAmount;
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

        // Do NOT update advance_payment - it stays as the initial booking payment only
        // Record additional payment in booking_payments table
        if ($paymentAmount > 0) {
            $booking->payments()->create([
                'amount' => $paymentAmount,
                'method' => $validated['method'],
                'type' => 'payment',
                'note' => $validated['note'] ?? null,
                'recorded_by_id' => Auth::id(),
            ]);
        }

        // Recalculate remaining using getTotalDeposited() (advance + all payments)
        $booking->refresh();
        $newRemainingPayment = max(0, $booking->getCalculatedRemaining());
        
        $updateData['total_amount'] = $baseAmount;
        $updateData['remaining_payment'] = $newRemainingPayment;
        $updateData['payment_status'] = $newRemainingPayment <= 0 ? 'paid' : 'partial';
        $updateData['updated_by_id'] = Auth::id();
        
        $booking->update($updateData);

        $paymentNote = $validated['note'] ?? '';
        if ($paymentDiscountAmount > 0) {
            $paymentNote .= ' [Discount: ৳' . number_format($paymentDiscountAmount, 2) . ' - Ref: ' . ($validated['discount_reference'] ?? 'N/A') . ']';
        }

        $message = 'Payment recorded successfully';
        if ($paymentDiscountAmount > 0 && $paymentAmount <= 0) {
            $message = 'Discount of ৳' . number_format($paymentDiscountAmount, 2) . ' applied successfully';
        } elseif ($paymentDiscountAmount > 0) {
            $message = 'Payment of ৳' . number_format($paymentAmount, 2) . ' with discount of ৳' . number_format($paymentDiscountAmount, 2) . ' recorded';
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

        // Recalculate remaining payment
        $baseAmount = $booking->total_amount;
        $discountAmount = 0;
        
        if ($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
            $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
        } elseif ($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
            $discountAmount = $booking->discount_amount;
        }
        
        $afterDiscount = $baseAmount - $discountAmount;
        $vatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
        $grandTotal = $afterDiscount + $newExtraCharges + $vatAmount;
        $totalDeposited = $booking->getTotalDeposited();
        $newRemainingPayment = max(0, $grandTotal - $totalDeposited);

        $booking->update([
            'extra_charges' => $newExtraCharges,
            'extra_charges_description' => $newDescription,
            'extra_charges_data' => $existingData,
            'remaining_payment' => $newRemainingPayment,
            'updated_by_id' => Auth::id(),
        ]);

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
            'customer_photo'        => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'customer_nid_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'passport_document'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'visiting_card'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $updateData = collect($validated)->except([
            'customer_photo','customer_nid_document','passport_document','visiting_card'
        ])->toArray();

        // Handle file uploads
        foreach (['customer_photo','customer_nid_document','passport_document','visiting_card'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('bookings/documents', 'public');
                $updateData[$field] = $path;
            }
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

        // Recalculate remaining using getTotalDeposited() (which excludes refunds)
        $booking->refresh();
        $newRemainingPayment = max(0, $booking->getCalculatedRemaining());
        
        $booking->update([
            'remaining_payment' => $newRemainingPayment,
            'payment_status' => $newRemainingPayment <= 0 ? 'paid' : 'partial',
            'updated_by_id' => Auth::id(),
        ]);

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
        
        // Recalculate remaining payment
        $grandTotal = $this->calculateGrandTotal($booking);
        $newRemainingPayment = max(0, $grandTotal - $booking->advance_payment);
        
        $booking->update([
            'remaining_payment' => $newRemainingPayment,
            'payment_status' => $newRemainingPayment <= 0 ? 'paid' : ($booking->advance_payment > 0 ? 'partial' : 'pending'),
        ]);

        return response()->json(['message' => 'VAT updated successfully']);
    }

    private function calculateGrandTotal(Booking $booking): float
    {
        $baseAmount = $booking->total_amount;
        $discountAmount = 0;
        
        if ($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
            $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
        } elseif ($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
            $discountAmount = $booking->discount_amount;
        }
        
        $afterDiscount = $baseAmount - min($discountAmount, $baseAmount);
        $extraCharges = $booking->extra_charges ?? 0;
        
        // Calculate VAT dynamically as 15% of after-discount amount
        $vatAmount = $booking->vat_enabled ? ($afterDiscount * 0.15) : 0;
        
        return $afterDiscount + $extraCharges + $vatAmount;
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
                'check_out_date' => 'required|date|after_or_equal:check_in_date',
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
            $oldCheckIn = \Carbon\Carbon::parse($booking->check_in_date)->startOfDay();
            $oldCheckOut = \Carbon\Carbon::parse($booking->check_out_date)->startOfDay();
            $newCheckIn = \Carbon\Carbon::parse($validated['check_in_date'])->startOfDay();
            $newCheckOut = \Carbon\Carbon::parse($validated['check_out_date'])->startOfDay();

            $datesChanged = !$newCheckIn->equalTo($oldCheckIn) || !$newCheckOut->equalTo($oldCheckOut);
            $checkoutExtended = $newCheckOut->gt($oldCheckOut);

            $mustCheckAvailability = $roomChanged || !$newCheckIn->equalTo($oldCheckIn) || $checkoutExtended;

            if ($mustCheckAvailability) {
                $hasConflict = Booking::query()
                    ->where('id', '!=', $booking->id)
                    ->where('room_id', $validated['room_id'])
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->where(function ($query) use ($validated) {
                        $query->where('check_in_date', '<', $validated['check_out_date'])
                            ->where('check_out_date', '>', $validated['check_in_date']);
                    })
                    ->exists();

                if ($hasConflict) {
                    return back()
                        ->withErrors(['check_out_date' => 'এই রুমটি নির্বাচিত তারিখে আগে থেকেই বুকড আছে। অন্য রুম/তারিখ নির্বাচন করুন।'])
                        ->withInput()
                        ->with('error', 'এই তারিখে রুমটি অলরেডি বুকড।');
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
                $roomTypePrice = optional($room->roomType)->price_per_night;
                $roomOwnPrice = $room->price_per_night ?? 0;
                $roomPrice = is_numeric($roomTypePrice) ? (float) $roomTypePrice : (float) $roomOwnPrice;
                $validated['total_amount'] = max(0, $roomPrice * $nights);
            } else {
                $validated['total_amount'] = max(0, (float) $validated['total_amount']);
            }

            $advancePayment = isset($validated['advance_payment']) ? (float) $validated['advance_payment'] : (float) ($booking->advance_payment ?? 0);
            $validated['advance_payment'] = max(0, $advancePayment);
            $validated['remaining_payment'] = max(0, (float) $validated['total_amount'] - (float) $validated['advance_payment']);

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

            return redirect()->route('admin.bookings.show', $booking)->with('success', 'বুকিং সফলভাবে আপডেট হয়েছে');
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
                        ->with('success', 'বুকিং আংশিকভাবে আপডেট হয়েছে (production compatibility mode)।');
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
}

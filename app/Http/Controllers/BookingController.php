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

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('room.roomType', 'createdBy');
        
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
        ]);

        $validated['created_by_id'] = Auth::id();
        $validated['remaining_payment'] = $validated['total_amount'] - $validated['advance_payment'];
        $validated['payment_status'] = $validated['advance_payment'] >= $validated['total_amount'] ? 'paid' : 'partial';

        $booking = Booking::create($validated);
        
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
        $booking->update(['status' => $validated['status']]);
        
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

        $booking->update($validated);
        return response()->json(['message' => 'Time updated successfully']);
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'method' => 'required|in:cash,card,bkash',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'discount_type' => 'nullable|in:none,flat,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $paymentAmount = floatval($validated['amount'] ?? 0);

        // Handle payment-level discount if provided
        $paymentDiscountAmount = 0;
        if (isset($validated['discount_type']) && $validated['discount_type'] !== 'none') {
            if ($validated['discount_type'] === 'flat') {
                $paymentDiscountAmount = floatval($validated['discount_amount'] ?? 0);
            } elseif ($validated['discount_type'] === 'percentage') {
                // Percentage discount on remaining amount
                $paymentDiscountAmount = ($booking->remaining_payment * floatval($validated['discount_percentage'] ?? 0)) / 100;
            }
        }

        // At least one must be provided: payment or discount
        if ($paymentAmount <= 0 && $paymentDiscountAmount <= 0) {
            return response()->json(['message' => 'Please enter payment amount or discount'], 422);
        }

        // Validate: payment + discount cannot exceed remaining balance
        $totalDeduction = $paymentAmount + $paymentDiscountAmount;
        if ($totalDeduction > $booking->remaining_payment) {
            return response()->json(['message' => 'পেমেন্ট ও ডিসকাউন্টের মোট বাকি টাকার চেয়ে বেশি হতে পারে না। বাকি আছে: ৳' . number_format($booking->remaining_payment, 2)], 422);
        }

        // Calculate grand total (including VAT, existing discount, extra charges)
        $baseAmount = $booking->total_amount;
        $existingDiscountAmount = 0;
        
        if ($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
            $existingDiscountAmount = ($baseAmount * $booking->discount_percentage) / 100;
        } elseif ($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
            $existingDiscountAmount = $booking->discount_amount;
        }

        // Calculate with NEW discount added
        $totalDiscountAmount = $existingDiscountAmount + $paymentDiscountAmount;
        $afterDiscount = $baseAmount - $totalDiscountAmount;
        $extraCharges = $booking->extra_charges ?? 0;
        $vatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
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
        }

        // Update booking payments
        $newAdvancePayment = $booking->advance_payment + $paymentAmount;
        $newRemainingPayment = max(0, $grandTotal - $newAdvancePayment);
        
        $updateData['advance_payment'] = $newAdvancePayment;
        $updateData['remaining_payment'] = $newRemainingPayment;
        $updateData['payment_status'] = $newRemainingPayment <= 0 ? 'paid' : ($newAdvancePayment > 0 || $paymentDiscountAmount > 0 ? 'partial' : 'pending');
        
        $booking->update($updateData);

        // Payment history recording is disabled until payments table is created
        // Just log the note if provided
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
        ]);

        $newExtraCharges = ($booking->extra_charges ?? 0) + $validated['amount'];
        $newDescription = $booking->extra_charges_description 
            ? $booking->extra_charges_description . '; ' . $validated['description']
            : $validated['description'];

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
        $newRemainingPayment = max(0, $grandTotal - $booking->advance_payment);

        $booking->update([
            'extra_charges' => $newExtraCharges,
            'extra_charges_description' => $newDescription,
            'remaining_payment' => $newRemainingPayment,
        ]);

        return response()->json(['message' => 'Extra charges added successfully']);
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

        if ($validated['amount'] > $booking->advance_payment) {
            return response()->json(['message' => 'Refund amount cannot exceed advance payment'], 422);
        }

        $newAdvancePayment = $booking->advance_payment - $validated['amount'];
        $grandTotal = $this->calculateGrandTotal($booking);
        $newRemainingPayment = max(0, $grandTotal - $newAdvancePayment);
        
        $booking->update([
            'advance_payment' => $newAdvancePayment,
            'remaining_payment' => $newRemainingPayment,
            'payment_status' => $newAdvancePayment <= 0 ? 'refunded' : 'partial',
        ]);

        // Record refund payment
        $booking->payments()->create([
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'type' => 'refund',
            'note' => $validated['note'],
            'recorded_by_id' => Auth::id(),
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

        // Calculate remaining payment
        $validated['remaining_payment'] = ($validated['total_amount'] ?? 0) - ($validated['advance_payment'] ?? 0);
        
        $booking->update($validated);
        return redirect()->route('admin.bookings.show', $booking)->with('success', 'বুকিং সফলভাবে আপডেট হয়েছে');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully');
    }
}

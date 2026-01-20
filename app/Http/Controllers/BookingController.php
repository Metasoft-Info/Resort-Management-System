<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\AdditionalGuest;
use App\Models\BookingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('room.roomType', 'createdBy');
        
        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        Booking::create($validated);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully');
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'room.roomType', 
            'createdBy', 
            'additionalGuests', 
            'payments.recordedBy',
            'foodPackage'
        ]);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $booking->update(['status' => $validated['status']]);
        return response()->json(['message' => 'Status updated successfully']);
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
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,mfs',
            'note' => 'nullable|string',
        ]);

        // Calculate grand total (including VAT, discount, extra charges)
        $baseAmount = $booking->total_amount;
        $discountAmount = 0;
        
        if ($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
            $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
        } elseif ($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
            $discountAmount = $booking->discount_amount;
        }
        
        $afterDiscount = $baseAmount - $discountAmount;
        $extraCharges = $booking->extra_charges ?? 0;
        $vatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
        $grandTotal = $afterDiscount + $extraCharges + $vatAmount;

        // Update booking
        $newAdvancePayment = $booking->advance_payment + $validated['amount'];
        $newRemainingPayment = max(0, $grandTotal - $newAdvancePayment);
        
        $booking->update([
            'advance_payment' => $newAdvancePayment,
            'remaining_payment' => $newRemainingPayment,
            'payment_status' => $newRemainingPayment <= 0 ? 'paid' : ($newAdvancePayment > 0 ? 'partial' : 'pending'),
            'payment_method' => $validated['method'],
        ]);

        // Record payment history
        $booking->payments()->create([
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'type' => 'payment',
            'note' => $validated['note'],
            'recorded_by_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Payment recorded successfully']);
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
            'vat_amount' => 'required|numeric|min:0',
        ]);

        $booking->update($validated);
        
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
        $vatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
        
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
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'payment_status' => 'sometimes|in:pending,partial,paid,refunded',
        ]);

        $booking->update($validated);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully');
    }
}

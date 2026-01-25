<?php

namespace App\Http\Controllers;

use App\Models\ConventionBooking;
use App\Models\ConventionHall;
use App\Models\ConventionPayment;
use App\Models\FoodPackage;
use App\Models\AddonService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConventionBookingController extends Controller
{
    public function searchCustomer($phone)
    {
        $booking = ConventionBooking::where('customer_phone', $phone)
            ->latest()
            ->first();
        
        if ($booking) {
            return response()->json([
                'customerName' => $booking->customer_name,
                'customerEmail' => $booking->customer_email,
                'customerWhatsapp' => $booking->customer_whatsapp,
                'customerNid' => $booking->customer_nid,
                'customerAddress' => $booking->customer_address,
                'organizationName' => $booking->organization_name,
            ]);
        }
        
        return response()->json([], 404);
    }

    public function index(Request $request)
    {
        $query = ConventionBooking::with('conventionHall');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by customer info
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_nid', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%");
            });
        }

        // Filter by event date range
        if ($request->filled('event_date_from')) {
            $query->whereDate('event_date', '>=', $request->event_date_from);
        }
        if ($request->filled('event_date_to')) {
            $query->whereDate('event_date', '<=', $request->event_date_to);
        }

        // Filter by booking date range
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $bookings = $query->latest()->paginate(15);
        return view('admin.convention-bookings.index', compact('bookings'));
    }

    public function findByPhone(Request $request)
    {
        $phone = $request->phone;
        
        $booking = ConventionBooking::where('customer_phone', $phone)
            ->orWhere('customer_nid', $phone)
            ->orWhere('customer_whatsapp', $phone)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($booking) {
            return response()->json([
                'customerName' => $booking->customer_name,
                'customerNid' => $booking->customer_nid,
                'customerPhone' => $booking->customer_phone,
                'customerWhatsapp' => $booking->customer_whatsapp,
                'customerEmail' => $booking->customer_email,
                'customerAddress' => $booking->customer_address,
                'organizationName' => $booking->organization_name,
            ]);
        }

        return response()->json(null, 404);
    }

    public function checkAvailability(Request $request)
    {
        $hallId = $request->hall_id;
        $eventDate = Carbon::parse($request->event_date)->startOfDay();
        $timeSlot = $request->time_slot;

        $hall = ConventionHall::find($hallId);
        if (!$hall) {
            return response()->json(['message' => 'Hall not found'], 404);
        }

        $conflictingBookings = ConventionBooking::where('hall_id', $hallId)
            ->whereDate('event_date', $eventDate)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($timeSlot) {
                // Full day bookings always conflict
                $query->where('time_slot', 'fullday')
                      ->orWhere(function($q) use ($timeSlot) {
                          if ($timeSlot === 'fullday') {
                              $q->whereIn('time_slot', ['morning', 'afternoon', 'evening', 'fullday']);
                          } else {
                              $q->where('time_slot', $timeSlot);
                          }
                      });
            })
            ->get();

        $available = $conflictingBookings->isEmpty();

        return response()->json([
            'available' => $available,
            'hall' => $hall,
            'conflictingBookings' => $available ? [] : $conflictingBookings,
            'message' => $available 
                ? "{$hall->name} is available for {$timeSlot} on {$eventDate->format('d/m/Y')}"
                : "{$hall->name} is already booked for {$timeSlot} on {$eventDate->format('d/m/Y')}"
        ]);
    }

    public function getAvailableHalls(Request $request)
    {
        $eventDate = Carbon::parse($request->event_date)->startOfDay();
        $timeSlot = $request->time_slot;

        $allHalls = ConventionHall::all();

        // Get booked hall IDs for this date/time
        $bookedHallIds = ConventionBooking::whereDate('event_date', $eventDate)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($timeSlot) {
                $query->where('time_slot', 'fullday')
                      ->orWhere(function($q) use ($timeSlot) {
                          if ($timeSlot === 'fullday') {
                              $q->whereIn('time_slot', ['morning', 'afternoon', 'evening', 'fullday']);
                          } else {
                              $q->where('time_slot', $timeSlot);
                          }
                      });
            })
            ->pluck('hall_id')
            ->unique()
            ->toArray();

        $availableHalls = $allHalls->whereNotIn('id', $bookedHallIds)->values();

        return response()->json([
            'availableHalls' => $availableHalls,
            'bookedHallIds' => $bookedHallIds,
            'totalHalls' => $allHalls->count(),
            'availableCount' => $availableHalls->count(),
        ]);
    }

    public function create()
    {
        $halls = ConventionHall::all();
        $foodPackages = FoodPackage::where('is_active', true)->get();
        $addonServices = AddonService::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        return view('admin.convention-bookings.create', compact('halls', 'foodPackages', 'addonServices'));
    }

    private function calculateTotals($data)
    {
        $hallRent = floatval($data['hall_rent'] ?? 0);
        $foodCost = floatval($data['food_cost'] ?? 0);
        $addonsCost = floatval($data['addons_cost'] ?? 0);
        $discount = floatval($data['discount'] ?? 0);
        $vatAmount = floatval($data['vat_amount'] ?? 0);
        $advancePayment = floatval($data['advance_payment'] ?? 0);

        $totalAmount = max(0, $hallRent + $foodCost + $addonsCost - $discount + $vatAmount);
        $remainingPayment = max(0, $totalAmount - $advancePayment);
        
        if ($remainingPayment <= 0) {
            $paymentStatus = 'paid';
        } elseif ($advancePayment > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }

        return [
            'hall_rent' => $hallRent,
            'food_cost' => $foodCost,
            'addons_cost' => $addonsCost,
            'discount' => $discount,
            'vat_amount' => $vatAmount,
            'advance_payment' => $advancePayment,
            'total_amount' => $totalAmount,
            'remaining_payment' => $remainingPayment,
            'payment_status' => $paymentStatus,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hall_id' => 'required|exists:convention_halls,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_nid' => 'nullable|string|max:50',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'organization_name' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'time_slot' => 'required|in:morning,afternoon,evening,fullday',
            'event_type' => 'required|string|max:255',
            'number_of_guests' => 'required|integer|min:1',
            'selected_food_package_id' => 'nullable|exists:food_packages,id',
            'selected_addons' => 'nullable|array',
            'addon_quantities' => 'nullable|array',
            'hall_rent' => 'required|numeric',
            'food_cost' => 'nullable|numeric',
            'addons_cost' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|in:flat,percentage',
            'discount_value' => 'nullable|numeric',
            'vat_amount' => 'nullable|numeric',
            'vat_percentage' => 'nullable|numeric',
            'advance_payment' => 'nullable|numeric',
            'payment_method' => 'required|in:cash,card,mfs',
            'notes' => 'nullable|string',
        ]);

        // Map selected_food_package_id to food_package_id
        if (isset($validated['selected_food_package_id'])) {
            $validated['food_package_id'] = $validated['selected_food_package_id'];
            unset($validated['selected_food_package_id']);
        }

        // Set defaults for missing fields
        $validated['discount_type'] = $validated['discount_type'] ?? 'flat';
        $validated['discount_value'] = $validated['discount_value'] ?? 0;
        $validated['vat_percentage'] = $validated['vat_percentage'] ?? 0;
        $validated['created_by_id'] = auth()->id();

        $totals = $this->calculateTotals($validated);
        $validated = array_merge($validated, $totals);
        $validated['status'] = $request->status ?? 'confirmed';

        $booking = ConventionBooking::create($validated);

        // Add advance payment if provided
        if ($booking->advance_payment > 0) {
            ConventionPayment::create([
                'convention_booking_id' => $booking->id,
                'amount' => $booking->advance_payment,
                'payment_method' => $validated['payment_method'],
                'payment_date' => now(),
                'notes' => 'Initial advance payment',
                'received_by_id' => auth()->id()
            ]);
        }

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking created successfully!');
    }

    public function show(ConventionBooking $conventionBooking)
    {
        $conventionBooking->load(['conventionHall', 'foodPackage', 'payments']);
        $booking = $conventionBooking;
        return view('admin.convention-bookings.show', compact('booking'));
    }

    public function edit(ConventionBooking $conventionBooking)
    {
        $halls = ConventionHall::all();
        $foodPackages = FoodPackage::all();
        $addonServices = AddonService::all();
        return view('admin.convention-bookings.edit', compact('conventionBooking', 'halls', 'foodPackages', 'addonServices'));
    }

    public function update(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'hall_id' => 'required|exists:convention_halls,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_nid' => 'nullable|string|max:50',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'organization_name' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'time_slot' => 'required|in:morning,afternoon,evening,fullday',
            'event_type' => 'required|string|max:255',
            'number_of_guests' => 'required|integer|min:1',
            'selected_food_package_id' => 'nullable|exists:food_packages,id',
            'selected_addons' => 'nullable|array',
            'addon_quantities' => 'nullable|array',
            'hall_rent' => 'required|numeric',
            'food_cost' => 'nullable|numeric',
            'addons_cost' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'vat_amount' => 'nullable|numeric',
            'advance_payment' => 'nullable|numeric',
            'payment_method' => 'nullable|in:cash,card,mfs',
            'notes' => 'nullable|string',
        ]);

        $totals = $this->calculateTotals($validated);
        $validated = array_merge($validated, $totals);

        $conventionBooking->update($validated);

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking updated successfully!');
    }

    public function addPayment(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,mfs',
            'note' => 'nullable|string',
        ]);

        // Create payment record
        ConventionPayment::create([
            'convention_booking_id' => $conventionBooking->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['method'],
            'payment_date' => now(),
            'notes' => $validated['note'] ?? null,
            'received_by_id' => auth()->id(),
        ]);

        // Update booking
        $newAdvance = $conventionBooking->advance_payment + $validated['amount'];
        $remainingPayment = max(0, $conventionBooking->total_amount - $newAdvance);
        
        if ($remainingPayment <= 0) {
            $paymentStatus = 'paid';
        } elseif ($newAdvance > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'unpaid';
        }

        $conventionBooking->update([
            'advance_payment' => $newAdvance,
            'remaining_payment' => $remainingPayment,
            'payment_status' => $paymentStatus,
        ]);

        return redirect()->route('admin.convention-bookings.show', $conventionBooking)
            ->with('success', 'Payment added successfully!');
    }

    public function updateStatus(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $conventionBooking->update($validated);

        return response()->json([
            'message' => 'Status updated successfully',
            'booking' => $conventionBooking,
        ]);
    }

    public function destroy(ConventionBooking $conventionBooking)
    {
        $conventionBooking->delete();

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking deleted successfully!');
    }
}


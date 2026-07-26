<?php

namespace App\Http\Controllers;

use App\Models\ConventionBooking;
use App\Models\ConventionHall;
use App\Models\ConventionPayment;
use App\Models\FoodPackage;
use App\Models\AddonService;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConventionBookingController extends Controller
{
    public function searchCustomer($phone)
    {
        // First check convention bookings
        $conventionBooking = ConventionBooking::where('customer_phone', $phone)
            ->latest()
            ->first();
        
        if ($conventionBooking) {
            return response()->json([
                'customerName' => $conventionBooking->customer_name,
                'customerEmail' => $conventionBooking->customer_email,
                'customerWhatsapp' => $conventionBooking->customer_whatsapp,
                'customerNid' => $conventionBooking->customer_nid,
                'customerAddress' => $conventionBooking->customer_address,
                'organizationName' => $conventionBooking->organization_name,
                'source' => 'convention'
            ]);
        }
        
        // Then check room bookings (resort)
        $roomBooking = Booking::where('guest_phone', $phone)
            ->latest()
            ->first();
        
        if ($roomBooking) {
            return response()->json([
                'customerName' => $roomBooking->guest_name,
                'customerEmail' => $roomBooking->guest_email,
                'customerWhatsapp' => $roomBooking->whatsapp_number,
                'customerNid' => $roomBooking->nid_number,
                'customerAddress' => $roomBooking->address,
                'organizationName' => $roomBooking->company_name ?? '',
                'source' => 'resort'
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
                $query->where('time_slot', 'full_day')
                      ->orWhere(function($q) use ($timeSlot) {
                          if ($timeSlot === 'full_day') {
                              $q->whereIn('time_slot', ['morning', 'night', 'full_day']);
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
                $query->where('time_slot', 'full_day')
                      ->orWhere(function($q) use ($timeSlot) {
                          if ($timeSlot === 'full_day') {
                              $q->whereIn('time_slot', ['morning', 'night', 'full_day']);
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
        $addonServices = AddonService::forConvention()->active()->orderBy('category')->orderBy('name')->get();
        return view('admin.convention-bookings.create', compact('halls', 'foodPackages', 'addonServices'));
    }

    private function calculateTotals($data)
    {
        $hallRent = round(floatval($data['hall_rent'] ?? 0));
        $foodCost = round(floatval($data['food_cost'] ?? 0));
        $addonsCost = round(floatval($data['addons_cost'] ?? 0));
        $discount = round(floatval($data['discount'] ?? 0));
        $vatPercentage = floatval($data['vat_percentage'] ?? 0);
        $advancePayment = round(floatval($data['advance_payment'] ?? 0));

        // Subtract discount from subtotal, then calculate VAT on the discounted amount
        $subtotal = $hallRent + $foodCost + $addonsCost - $discount;
        $vatAmount = round($subtotal * ($vatPercentage / 100));
        $totalAmount = round($subtotal + $vatAmount);
        $remainingPayment = round($totalAmount - $advancePayment);
        
        // If remaining is 5 or less due to rounding, consider it paid
        if ($remainingPayment <= 5) {
            $remainingPayment = 0;
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
            'hall_id' => 'nullable|exists:convention_halls,id',
            'hall_ids' => 'nullable|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_nid' => 'nullable|string|max:50',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'organization_name' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'time_slot' => 'required|in:morning,night,full_day',
            'event_type' => 'required|string|max:255',
            'event_description' => 'nullable|string',
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
            'payment_method' => 'required|in:cash,card,bkash,mfs',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'customer_photo' => 'nullable|array',
            'customer_photo.*' => 'nullable|image|max:5120',
            'customer_nid_document' => 'nullable|array',
            'customer_nid_document.*' => 'nullable|file|max:5120',
            'passport_document' => 'nullable|array',
            'passport_document.*' => 'nullable|file|max:5120',
            'visiting_card' => 'nullable|array',
            'visiting_card.*' => 'nullable|file|max:5120',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
        ]);

        // Parse multiple hall IDs if provided
        $hallIds = [];
        if (!empty($validated['hall_ids'])) {
            $hallIds = array_filter(explode(',', $validated['hall_ids']), fn($id) => is_numeric($id));
        }
        if (empty($hallIds) && !empty($validated['hall_id'])) {
            $hallIds = [$validated['hall_id']];
        }
        if (empty($hallIds)) {
            return redirect()->back()->withErrors(['hall_id' => 'At least one hall must be selected.'])->withInput();
        }

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
        $validated['updated_by_id'] = auth()->id();

        // Set discount approval status
        $hasDiscount = ($validated['discount'] ?? 0) > 0;
        if ($hasDiscount) {
            if (\Illuminate\Support\Facades\Auth::user()->canApproveDiscounts()) {
                $validated['discount_status'] = 'approved';
                $validated['discount_approved_by'] = \Illuminate\Support\Facades\Auth::id();
                $validated['discount_approved_at'] = now();
            } else {
                $validated['discount_status'] = 'pending';
                $validated['discount_requested_by'] = \Illuminate\Support\Facades\Auth::id();
            }
        }

        // Handle file uploads
        $docFields = ['customer_photo', 'customer_nid_document', 'passport_document', 'visiting_card'];
        foreach ($docFields as $field) {
            $paths = [];
            if ($request->hasFile($field)) {
                $files = $request->file($field);
                if (!is_array($files)) {
                    $files = [$files];
                }
                foreach ($files as $f) {
                    if ($f && $f->isValid()) {
                        $paths[] = $f->store('convention-bookings/documents', 'public');
                    }
                }
            }
            if (!empty($paths)) {
                $validated[$field] = $paths;
            }
        }

        // Remove hall_ids and hall_id from validated (not DB columns)
        $hallRentTotal = floatval($validated['hall_rent'] ?? 0);
        $discountTotal = floatval($validated['discount'] ?? 0);
        unset($validated['hall_ids'], $validated['hall_id']);

        // Calculate per-hall rent and discount
        $hallCount = count($hallIds);
        $perHallRent = round($hallRentTotal / $hallCount);
        $perHallDiscount = round($discountTotal / $hallCount);

        $createdBookings = [];

        foreach ($hallIds as $idx => $hallId) {
            $bookingData = $validated;
            $bookingData['hall_id'] = $hallId;
            $bookingData['hall_rent'] = $perHallRent;
            $bookingData['discount'] = $perHallDiscount;

            // For multiple halls: only first booking gets food, addons, and advance
            // Subsequent halls are hall-rent-only bookings
            if ($idx > 0) {
                $bookingData['food_cost'] = 0;
                $bookingData['food_package_id'] = null;
                $bookingData['addons_cost'] = 0;
                $bookingData['selected_addons'] = [];
                $bookingData['addon_quantities'] = [];
                $bookingData['advance_payment'] = 0;
            }

            $totals = $this->calculateTotals($bookingData);
            $bookingData = array_merge($bookingData, $totals);
            $bookingData['status'] = $request->status ?? 'confirmed';

            $booking = ConventionBooking::create($bookingData);
            $createdBookings[] = $booking;

            ActivityLog::log('Created convention booking', 'ConventionBooking', $booking->id, [
                'customer_name' => $booking->customer_name,
                'hall_id' => $booking->hall_id,
                'event_date' => $booking->event_date,
                'total_amount' => $booking->total_amount
            ]);

            // Add advance payment if provided (only for first booking)
            if ($idx === 0 && $booking->advance_payment > 0) {
                ConventionPayment::create([
                    'convention_booking_id' => $booking->id,
                    'amount' => $booking->advance_payment,
                    'payment_method' => $validated['payment_method'],
                    'bkash_number' => $validated['bkash_number'] ?? null,
                    'bank_name' => $validated['bank_name'] ?? null,
                    'payment_date' => now(),
                    'notes' => 'Initial advance payment',
                    'received_by_id' => auth()->id()
                ]);
            }
        }

        $msg = count($createdBookings) > 1
            ? count($createdBookings) . ' convention bookings created successfully!'
            : 'Convention booking created successfully!';

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', $msg);
    }

    public function show(Request $request, ConventionBooking $conventionBooking)
    {
        $conventionBooking->load(['conventionHall', 'foodPackage', 'payments']);
        $booking = $conventionBooking;

        // Find related bookings for same customer + event (multi-hall support)
        $relatedBookings = ConventionBooking::where('id', '!=', $booking->id)
            ->where('customer_phone', $booking->customer_phone)
            ->whereDate('event_date', $booking->event_date->toDateString())
            ->where('time_slot', $booking->time_slot)
            ->where('status', '!=', 'cancelled')
            ->with(['conventionHall', 'payments'])
            ->orderBy('id')
            ->get();

        $allBookings = collect([$booking])->merge($relatedBookings);

        $groupTotals = [
            'hall_rent' => $allBookings->sum('hall_rent'),
            'food_cost' => $allBookings->sum('food_cost'),
            'addons_cost' => $allBookings->sum('addons_cost'),
            'discount' => $allBookings->sum('discount'),
            'vat_amount' => $allBookings->sum('vat_amount'),
            'total_amount' => $allBookings->sum('total_amount'),
            'advance_payment' => $allBookings->sum('advance_payment'),
            'remaining_payment' => $allBookings->sum('remaining_payment'),
        ];

        // Halls available to add to this event
        $allHalls = ConventionHall::all();
        $bookedHallIds = $allBookings->pluck('hall_id')->unique()->values()->toArray();
        $eventDate = Carbon::parse($booking->event_date)->startOfDay();
        $timeSlot = $booking->time_slot;

        $bookedByOthers = ConventionBooking::whereDate('event_date', $eventDate->toDateString())
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($timeSlot) {
                $query->where('time_slot', 'full_day')
                      ->orWhere(function($q) use ($timeSlot) {
                          if ($timeSlot === 'full_day') {
                              $q->whereIn('time_slot', ['morning', 'night', 'full_day']);
                          } else {
                              $q->where('time_slot', $timeSlot);
                          }
                      });
            })
            ->pluck('hall_id')
            ->unique()
            ->toArray();

        $unavailableHallIds = array_unique(array_merge($bookedHallIds, $bookedByOthers));

        $availableHalls = $allHalls->filter(function ($hall) use ($unavailableHallIds) {
            return !in_array($hall->id, $unavailableHallIds);
        })->values();

        // Return JSON for AJAX requests
        if ($request->has('ajax') || $request->ajax()) {
            return response()->json([
                'booking' => $booking,
                'related_bookings' => $relatedBookings,
                'group_totals' => $groupTotals
            ]);
        }

        return view('admin.convention-bookings.show', compact('booking', 'relatedBookings', 'groupTotals', 'availableHalls'));
    }

    public function edit(ConventionBooking $conventionBooking)
    {
        $halls = ConventionHall::all();
        $foodPackages = FoodPackage::all();
        $addonServices = AddonService::forConvention()->get();

        // Find related hall bookings for the same customer + event
        $relatedBookings = ConventionBooking::where('id', '!=', $conventionBooking->id)
            ->where('customer_phone', $conventionBooking->customer_phone)
            ->whereDate('event_date', $conventionBooking->event_date->toDateString())
            ->where('time_slot', $conventionBooking->time_slot)
            ->where('status', '!=', 'cancelled')
            ->with('conventionHall')
            ->orderBy('id')
            ->get();

        // Halls already booked in this group
        $bookedHallIds = collect([$conventionBooking->hall_id])
            ->merge($relatedBookings->pluck('hall_id'))
            ->unique()
            ->values()
            ->toArray();

        // Halls available for this date + slot (not booked by anyone, excluding current group)
        $eventDate = Carbon::parse($conventionBooking->event_date)->startOfDay();
        $timeSlot = $conventionBooking->time_slot;

        $bookedByOthers = ConventionBooking::whereDate('event_date', $eventDate->toDateString())
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($timeSlot) {
                $query->where('time_slot', 'full_day')
                      ->orWhere(function($q) use ($timeSlot) {
                          if ($timeSlot === 'full_day') {
                              $q->whereIn('time_slot', ['morning', 'night', 'full_day']);
                          } else {
                              $q->where('time_slot', $timeSlot);
                          }
                      });
            })
            ->pluck('hall_id')
            ->unique()
            ->toArray();

        // Exclude current group's halls + others' booked halls
        $unavailableHallIds = array_unique(array_merge($bookedHallIds, $bookedByOthers));

        $availableHalls = $halls->filter(function ($hall) use ($unavailableHallIds) {
            return !in_array($hall->id, $unavailableHallIds);
        })->values();

        return view('admin.convention-bookings.edit', compact('conventionBooking', 'halls', 'foodPackages', 'addonServices', 'relatedBookings', 'availableHalls'));
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
            'time_slot' => 'required|in:morning,night,full_day',
            'event_type' => 'required|string|max:255',
            'event_description' => 'nullable|string',
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
            'payment_method' => 'nullable|in:cash,card,bkash,mfs',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
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
        $validated['updated_by_id'] = auth()->id();

        $totals = $this->calculateTotals($validated);
        $validated = array_merge($validated, $totals);

        $conventionBooking->update($validated);

        // Create additional hall bookings if requested
        $additionalHallIds = $request->input('additional_hall_ids', []);
        $createdCount = 0;
        if (is_array($additionalHallIds) && count($additionalHallIds) > 0) {
            foreach ($additionalHallIds as $hallId) {
                $hall = ConventionHall::find($hallId);
                if (!$hall) continue;

                // Check hall is still available for this date + slot
                $eventDate = Carbon::parse($validated['event_date'])->startOfDay();
                $timeSlot = $validated['time_slot'];
                $conflict = ConventionBooking::where('hall_id', $hallId)
                    ->whereDate('event_date', $eventDate)
                    ->where('status', '!=', 'cancelled')
                    ->where(function($query) use ($timeSlot) {
                        $query->where('time_slot', 'full_day')
                              ->orWhere(function($q) use ($timeSlot) {
                                  if ($timeSlot === 'full_day') {
                                      $q->whereIn('time_slot', ['morning', 'night', 'full_day']);
                                  } else {
                                      $q->where('time_slot', $timeSlot);
                                  }
                              });
                    })
                    ->exists();

                if ($conflict) continue;

                $bookingData = $validated;
                $bookingData['hall_id'] = $hallId;
                $bookingData['hall_rent'] = $hall->price_per_day;
                // Additional halls: no food/addons, no discount, no advance
                $bookingData['food_cost'] = 0;
                $bookingData['food_package_id'] = null;
                $bookingData['addons_cost'] = 0;
                $bookingData['selected_addons'] = [];
                $bookingData['addon_quantities'] = [];
                $bookingData['discount'] = 0;
                $bookingData['discount_value'] = 0;
                $bookingData['advance_payment'] = 0;
                $bookingData['created_by_id'] = auth()->id();
                $bookingData['updated_by_id'] = auth()->id();

                $newTotals = $this->calculateTotals($bookingData);
                $bookingData = array_merge($bookingData, $newTotals);
                $bookingData['status'] = $conventionBooking->status;

                ConventionBooking::create($bookingData);
                $createdCount++;
            }
        }

        $msg = $createdCount > 0
            ? 'Booking updated and ' . $createdCount . ' additional hall(s) booked successfully!'
            : 'Convention booking updated successfully!';

        return redirect()->route('admin.convention-bookings.show', $conventionBooking)
            ->with('success', $msg);
    }

    public function addHalls(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'hall_ids' => 'required|array|min:1',
            'hall_ids.*' => 'exists:convention_halls,id',
        ]);

        $hallIds = $validated['hall_ids'];
        $createdCount = 0;
        $skippedHalls = [];

        $eventDate = Carbon::parse($conventionBooking->event_date)->startOfDay();
        $timeSlot = $conventionBooking->time_slot;

        $createdHalls = [];

        DB::transaction(function () use ($conventionBooking, $hallIds, $eventDate, $timeSlot, &$createdCount, &$skippedHalls, &$createdHalls) {
            // Lock existing bookings for this date/slot to prevent race conditions
            $existingIds = ConventionBooking::whereDate('event_date', $eventDate->toDateString())
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($timeSlot) {
                    $query->where('time_slot', 'full_day')
                          ->orWhere(function($q) use ($timeSlot) {
                              if ($timeSlot === 'full_day') {
                                  $q->whereIn('time_slot', ['morning', 'night', 'full_day']);
                              } else {
                                  $q->where('time_slot', $timeSlot);
                              }
                          });
                })
                ->lockForUpdate()
                ->pluck('hall_id')
                ->unique()
                ->toArray();

            // Include current group's halls
            $relatedBookings = ConventionBooking::where('id', '!=', $conventionBooking->id)
                ->where('customer_phone', $conventionBooking->customer_phone)
                ->whereDate('event_date', $eventDate->toDateString())
                ->where('time_slot', $timeSlot)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->get();

            $allBookings = collect([$conventionBooking])->merge($relatedBookings);
            $bookedHallIds = $allBookings->pluck('hall_id')->unique()->values()->toArray();

            foreach ($hallIds as $hallId) {
                $hallId = intval($hallId);

                // Skip if already booked in this group or by anyone else
                if (in_array($hallId, $bookedHallIds) || in_array($hallId, $existingIds)) {
                    $skippedHalls[] = 'Already booked';
                    continue;
                }

                $hall = ConventionHall::find($hallId);
                if (!$hall) {
                    $skippedHalls[] = 'Hall not found';
                    continue;
                }

                // Build new booking from existing booking data
                $bookingData = [
                    'hall_id' => $hallId,
                    'hall_rent' => $hall->price_per_day,
                    'customer_name' => $conventionBooking->customer_name,
                    'customer_phone' => $conventionBooking->customer_phone,
                    'customer_whatsapp' => $conventionBooking->customer_whatsapp,
                    'customer_email' => $conventionBooking->customer_email,
                    'customer_nid' => $conventionBooking->customer_nid,
                    'customer_address' => $conventionBooking->customer_address,
                    'organization_name' => $conventionBooking->organization_name,
                    'event_date' => $conventionBooking->event_date,
                    'time_slot' => $conventionBooking->time_slot,
                    'event_type' => $conventionBooking->event_type,
                    'event_description' => $conventionBooking->event_description,
                    'number_of_guests' => $conventionBooking->number_of_guests,
                    'food_package_id' => null,
                    'food_cost' => 0,
                    'selected_addons' => [],
                    'addon_quantities' => [],
                    'addons_cost' => 0,
                    'discount' => 0,
                    'discount_type' => $conventionBooking->discount_type ?? 'flat',
                    'discount_value' => 0,
                    'vat_percentage' => $conventionBooking->vat_percentage ?? 0,
                    'advance_payment' => 0,
                    'payment_method' => $conventionBooking->payment_method,
                    'bkash_number' => $conventionBooking->bkash_number,
                    'bank_name' => $conventionBooking->bank_name,
                    'notes' => $conventionBooking->notes,
                    'status' => $conventionBooking->status,
                    'created_by_id' => auth()->id(),
                    'updated_by_id' => auth()->id(),
                ];

                $newTotals = $this->calculateTotals($bookingData);
                $bookingData = array_merge($bookingData, $newTotals);

                ConventionBooking::create($bookingData);
                $createdCount++;
                $createdHalls[] = $hall->name;
                $bookedHallIds[] = $hallId;
                $existingIds[] = $hallId;
            }
        });

        if ($createdCount > 0) {
            $msg = $createdCount . ' additional hall(s) added successfully!';
            $msg .= ' (' . implode(', ', $createdHalls) . ')';
            $type = 'success';
        } else {
            $msg = 'No halls were added.';
            if (!empty($skippedHalls)) {
                $msg .= ' Reasons: ' . implode(', ', array_unique($skippedHalls)) . '.';
            } else {
                $msg .= ' Please select free halls and try again.';
            }
            $type = 'error';
        }

        return redirect()->route('admin.convention-bookings.show', $conventionBooking)
            ->with($type, $msg);
    }

    public function addPayment(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bkash,mfs',
            'bkash_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        // Create payment record
        ConventionPayment::create([
            'convention_booking_id' => $conventionBooking->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['method'],
            'bkash_number' => $validated['bkash_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
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
            $paymentStatus = 'pending';
        }

        $conventionBooking->update([
            'advance_payment' => $newAdvance,
            'remaining_payment' => $remainingPayment,
            'payment_status' => $paymentStatus,
            'updated_by_id' => auth()->id(),
        ]);

        return redirect()->route('admin.convention-bookings.show', $conventionBooking)
            ->with('success', 'Payment added successfully!');
    }

    public function updateStatus(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $validated['updated_by_id'] = auth()->id();
        $conventionBooking->update($validated);

        return response()->json([
            'message' => 'Status updated successfully',
            'booking' => $conventionBooking,
        ]);
    }

    public function updateAddons(Request $request, ConventionBooking $conventionBooking)
    {
        $selectedAddons = $request->input('selected_addons', []);
        $addonQuantities = $request->input('addon_quantities', []);
        
        // Calculate new addons cost
        $addonsCost = 0;
        if (!empty($selectedAddons)) {
            foreach ($selectedAddons as $addonId) {
                $addon = AddonService::find($addonId);
                if ($addon) {
                    $qty = isset($addonQuantities[$addonId]) ? intval($addonQuantities[$addonId]) : 1;
                    $addonsCost += $addon->price * $qty;
                }
            }
        }
        
        // Recalculate totals
        $hallRent = round(floatval($conventionBooking->hall_rent));
        $foodCost = round(floatval($conventionBooking->food_cost));
        $discount = round(floatval($conventionBooking->discount));
        $vatPercentage = floatval($conventionBooking->vat_percentage ?? 0);
        
        $subtotal = $hallRent + $foodCost + $addonsCost - $discount;
        $vatAmount = round($subtotal * ($vatPercentage / 100));
        $totalAmount = round($subtotal + $vatAmount);
        $remainingPayment = max(0, $totalAmount - $conventionBooking->advance_payment);
        
        if ($remainingPayment <= 0) {
            $paymentStatus = 'paid';
        } elseif ($conventionBooking->advance_payment > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }
        
        $conventionBooking->update([
            'selected_addons' => $selectedAddons,
            'addon_quantities' => $addonQuantities,
            'addons_cost' => round($addonsCost),
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'remaining_payment' => $remainingPayment,
            'payment_status' => $paymentStatus,
        ]);
        
        return redirect()->route('admin.convention-bookings.show', $conventionBooking)
            ->with('success', 'Addon services updated successfully!');
    }

    public function destroy(ConventionBooking $conventionBooking)
    {
        $conventionBooking->delete();

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking deleted successfully!');
    }
}


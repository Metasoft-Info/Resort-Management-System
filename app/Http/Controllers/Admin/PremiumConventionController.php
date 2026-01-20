<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ConventionHall, ConventionBooking, FoodPackage, AddonService};
use Illuminate\Http\Request;

class PremiumConventionController extends Controller {
    public function index() {
        $halls = ConventionHall::all();
        $foodPackages = FoodPackage::where('is_active', true)->get();
        $addonServices = AddonService::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        return view('admin.premium-convention.index', compact('halls', 'foodPackages', 'addonServices'));
    }
    public function search(Request $request) {
        $bookedHallIds = ConventionBooking::whereDate('event_date', $request->date)
            ->where('time_slot', $request->slot)->whereNotIn('status', ['cancelled'])
            ->pluck('hall_id')->toArray();
        $availableHalls = ConventionHall::whereNotIn('id', $bookedHallIds)->get();
        return response()->json(['availableHalls' => $availableHalls]);
    }
    public function book(Request $request) {
        $validated = $request->validate([
            'convention_hall_id' => 'required|exists:convention_halls,id',
            'event_date' => 'required|date',
            'time_slot' => 'required|string',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_email' => 'nullable|email',
            'number_of_guests' => 'required|integer|min:1',
            'event_type' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
        ]);
        
        // Map convention_hall_id to hall_id for database
        $validated['hall_id'] = $validated['convention_hall_id'];
        unset($validated['convention_hall_id']);
        
        // Set defaults
        $validated['created_by_id'] = auth()->id();
        $validated['status'] = 'confirmed';
        $validated['payment_status'] = 'pending';
        $validated['hall_rent'] = $validated['total_amount'];
        $validated['food_cost'] = 0;
        $validated['addons_cost'] = 0;
        $validated['discount'] = 0;
        $validated['vat_amount'] = 0;
        $validated['advance_payment'] = $validated['advance_payment'] ?? 0;
        $validated['remaining_payment'] = $validated['total_amount'] - $validated['advance_payment'];
        $validated['notes'] = $validated['special_requests'] ?? '';
        
        try {
            ConventionBooking::create($validated);
            return response()->json(['success' => true, 'message' => 'Booking created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ConventionHall, ConventionBooking, FoodPackage};
use Illuminate\Http\Request;

class PremiumConventionController extends Controller {
    public function index() {
        $halls = ConventionHall::all();
        $foodPackages = FoodPackage::all();
        return view('admin.premium-convention.index', compact('halls', 'foodPackages'));
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
            'hall_id' => 'required|exists:convention_halls,id',
            'event_date' => 'required|date',
            'time_slot' => 'required|string',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'confirmed';
        ConventionBooking::create($validated);
        return response()->json(['success' => true]);
    }
}
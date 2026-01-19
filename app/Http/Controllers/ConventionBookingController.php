<?php

namespace App\Http\Controllers;

use App\Models\ConventionBooking;
use App\Models\ConventionHall;
use Illuminate\Http\Request;

class ConventionBookingController extends Controller
{
    public function index()
    {
        $bookings = ConventionBooking::with('conventionHall')->latest()->paginate(15);
        return view('admin.convention-bookings.index', compact('bookings'));
    }

    public function create()
    {
        $halls = ConventionHall::all();
        return view('admin.convention-bookings.create', compact('halls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'convention_hall_id' => 'required|exists:convention_halls,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'event_date' => 'required|date',
            'event_type' => 'required|string|max:255',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        ConventionBooking::create($validated);

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking created successfully!');
    }

    public function show(ConventionBooking $conventionBooking)
    {
        $conventionBooking->load('conventionHall');
        return view('admin.convention-bookings.show', compact('conventionBooking'));
    }

    public function edit(ConventionBooking $conventionBooking)
    {
        $halls = ConventionHall::all();
        return view('admin.convention-bookings.edit', compact('conventionBooking', 'halls'));
    }

    public function update(Request $request, ConventionBooking $conventionBooking)
    {
        $validated = $request->validate([
            'convention_hall_id' => 'required|exists:convention_halls,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'event_date' => 'required|date',
            'event_type' => 'required|string|max:255',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $conventionBooking->update($validated);

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking updated successfully!');
    }

    public function destroy(ConventionBooking $conventionBooking)
    {
        $conventionBooking->delete();

        return redirect()->route('admin.convention-bookings.index')
            ->with('success', 'Convention booking deleted successfully!');
    }
}


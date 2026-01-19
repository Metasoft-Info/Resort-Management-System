<?php

namespace App\Http\Controllers;

use App\Models\ConventionHall;
use Illuminate\Http\Request;

class ConventionHallController extends Controller
{
    public function index()
    {
        $halls = ConventionHall::paginate(10);
        return view('admin.convention-halls.index', compact('halls'));
    }

    public function create()
    {
        return view('admin.convention-halls.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dimensions' => 'required|numeric',
            'max_capacity' => 'required|integer',
            'price_per_day' => 'required|numeric',
            'amenities' => 'nullable|array',
            'event_types' => 'nullable|array',
            'status' => 'required|in:available,booked,maintenance',
        ]);

        ConventionHall::create($validated);

        return redirect()->route('admin.convention-halls.index')
            ->with('success', 'Convention hall created successfully!');
    }

    public function show(ConventionHall $conventionHall)
    {
        return view('admin.convention-halls.show', compact('conventionHall'));
    }

    public function edit(ConventionHall $conventionHall)
    {
        return view('admin.convention-halls.edit', compact('conventionHall'));
    }

    public function update(Request $request, ConventionHall $conventionHall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dimensions' => 'required|numeric',
            'max_capacity' => 'required|integer',
            'price_per_day' => 'required|numeric',
            'amenities' => 'nullable|array',
            'event_types' => 'nullable|array',
            'status' => 'required|in:available,booked,maintenance',
        ]);

        $conventionHall->update($validated);

        return redirect()->route('admin.convention-halls.index')
            ->with('success', 'Convention hall updated successfully!');
    }

    public function destroy(ConventionHall $conventionHall)
    {
        $conventionHall->delete();

        return redirect()->route('admin.convention-halls.index')
            ->with('success', 'Convention hall deleted successfully!');
    }
}


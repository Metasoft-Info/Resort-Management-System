<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->paginate(15);
        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('admin.room-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
        ]);

        RoomType::create($validated);

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type created successfully');
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
        ]);

        $roomType->update($validated);

        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type updated successfully');
    }

    public function destroy(RoomType $roomType)
    {
        $roomType->delete();
        return redirect()->route('admin.room-types.index')
            ->with('success', 'Room type deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->latest()->paginate(15);
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomTypes = \App\Models\RoomType::where('is_active', true)->get();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms',
            'name' => 'required',
            'room_type_id' => 'required|exists:room_types,id',
            'description' => 'nullable',
            'price_per_night' => 'required|numeric',
            'max_guests' => 'nullable|integer',
            'number_of_beds' => 'nullable|integer',
            'status' => 'required|in:available,booked,maintenance',
        ]);

        Room::create($validated);
        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully');
    }

    public function edit(Room $room)
    {
        $roomTypes = \App\Models\RoomType::where('is_active', true)->get();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $room->id,
            'name' => 'required',
            'room_type_id' => 'required|exists:room_types,id',
            'price_per_night' => 'required|numeric',
            'status' => 'required|in:available,booked,maintenance',
        ]);

        $room->update($validated);
        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully');
    }
}

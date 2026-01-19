@extends('layouts.admin')

@section('title', 'Add Room')
@section('header', 'Add New Room')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.rooms.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-hashtag mr-2 text-primary-600"></i>Room Number
                </label>
                <input type="text" name="room_number" value="{{ old('room_number') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition @error('room_number') border-red-500 @enderror" 
                    placeholder="e.g., 101" required>
                @error('room_number')
                    <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Room Name</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                <i class="fas fa-tag mr-2 text-primary-600"></i>Room Type
            </label>
            <select name="room_type_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition @error('room_type_id') border-red-500 @enderror" required>
                <option value="">Select Room Type</option>
                @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }} (৳{{ number_format($type->base_price) }})
                    </option>
                @endforeach
            </select>
            @error('room_type_id')
                <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" rows="3" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Price Per Night</label>
            <input type="number" step="0.01" name="price_per_night" value="{{ old('price_per_night') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Max Guests</label>
                <input type="number" name="max_guests" value="{{ old('max_guests', 2) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Number of Beds</label>
                <input type="number" name="number_of_beds" value="{{ old('number_of_beds', 1) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
            <select name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">
                Create Room
            </button>
            <a href="{{ route('admin.rooms.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

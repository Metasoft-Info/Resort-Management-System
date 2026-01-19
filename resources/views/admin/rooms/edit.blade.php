@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">রুম সম্পাদনা করুন</h1>
        <p class="text-gray-600 mt-2">Edit Room Details</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl">
        <form action="{{ route('admin.rooms.update', $room) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-2"></i>রুম নম্বর *
                    </label>
                    <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('room_number') border-red-500 @enderror">
                    @error('room_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-door-open mr-2"></i>রুমের নাম *
                    </label>
                    <input type="text" name="name" value="{{ old('name', $room->name) }}" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2"></i>রুম টাইপ *
                    </label>
                    <select name="room_type_id" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('room_type_id') border-red-500 @enderror">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (৳{{ number_format($type->base_price) }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-bangladeshi-taka-sign mr-2"></i>প্রতি রাতের মূল্য (৳) *
                    </label>
                    <input type="number" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night) }}" step="0.01" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-users mr-2"></i>সর্বোচ্চ অতিথি
                    </label>
                    <input type="number" name="max_guests" value="{{ old('max_guests', $room->max_guests) }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-bed mr-2"></i>বিছানার সংখ্যা
                    </label>
                    <input type="number" name="number_of_beds" value="{{ old('number_of_beds', $room->number_of_beds) }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2"></i>বিবরণ
                    </label>
                    <textarea name="description" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $room->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>স্ট্যাটাস *
                    </label>
                    <select name="status" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="booked" {{ old('status', $room->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center mt-8">
                        <input type="checkbox" name="has_ac" value="1" {{ old('has_ac', $room->has_ac) ? 'checked' : '' }} 
                            class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-semibold text-gray-700">
                            <i class="fas fa-snowflake mr-2"></i>এসি আছে
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>আপডেট করুন
                </button>
                <a href="{{ route('admin.rooms.index') }}" 
                    class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-times mr-2"></i>বাতিল
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

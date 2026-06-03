@extends('layouts.admin')

@section('content')
<div class="p-6">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">Room Type Edit </h1>
 <p class="text-gray-600 mt-2">Update Room Type information</p>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-8">
 <form action="{{ route('admin.room-types.update', $roomType) }}" method="POST">
 @csrf
 @method('PUT')
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label>
 <input type="text" name="name" value="{{ old('name', $roomType->name) }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
 @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Price () *</label>
 <input type="number" name="base_price" value="{{ old('base_price', $roomType->base_price) }}" step="0.01" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
 @error('base_price')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Max Occupancy *</label>
 <input type="number" name="max_occupancy" value="{{ old('max_occupancy', $roomType->max_occupancy) }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
 @error('max_occupancy')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Amenities</label>
 <input type="text" name="amenities" value="{{ old('amenities', $roomType->amenities) }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
 @error('amenities')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
 </div>

 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
 <textarea name="description" rows="4"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ old('description', $roomType->description) }}</textarea>
 @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
 </div>
 </div>

 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-save mr-2"></i>Update
 </button>
 <a href="{{ route('admin.room-types.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
 <i class="fas fa-times mr-2"></i>Cancel
 </a>
 </div>
 </form>
 </div>
</div>
@endsection

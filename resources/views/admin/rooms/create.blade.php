@extends('layouts.admin')

@section('title', 'Add Room')
@section('header', 'Add New Room')

@section('content')
<div class="max-w-4xl">
 <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl p-8">
 @csrf

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div class="mb-4">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-hashtag mr-2 text-primary-600"></i>Room Number
 </label>
 <input type="text" name="room_number" value="{{ old('room_number') }}" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition @error('room_number') border-red-500 @enderror" 
 placeholder="e.g., 101" required>
 @error('room_number')
 <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
 @enderror
 </div>

 <div class="mb-4">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-door-open mr-2 text-primary-600"></i>Room Name
 </label>
 <input type="text" name="name" value="{{ old('name') }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition @error('name') border-red-500 @enderror" required>
 @error('name')
 <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
 @enderror
 </div>

 <div class="mb-4">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-tag mr-2 text-primary-600"></i>Room Type
 </label>
 <select name="room_type_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition @error('room_type_id') border-red-500 @enderror" required>
 <option value="">Select Room Type</option>
 @foreach($roomTypes as $type)
 <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
 {{ $type->name }} ({{ number_format($type->base_price) }})
 </option>
 @endforeach
 </select>
 @error('room_type_id')
 <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
 @enderror
 </div>

 <div class="mb-4">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-align-left mr-2 text-primary-600"></i>Description
 </label>
 <textarea name="description" rows="3"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition">{{ old('description') }}</textarea>
 </div>

 <div class="mb-4">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>Price Per Night
 </label>
 <input type="number" step="0.01" name="price_per_night" value="{{ old('price_per_night') }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition" required>
 </div>

 <div class="grid grid-cols-2 gap-4 mb-4">
 <div>
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-users mr-2 text-primary-600"></i>Max Guests
 </label>
 <input type="number" name="max_guests" value="{{ old('max_guests', 2) }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition">
 </div>
 <div>
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-bed mr-2 text-primary-600"></i>Number of Beds
 </label>
 <input type="number" name="number_of_beds" value="{{ old('number_of_beds', 1) }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition">
 </div>
 </div>

 <div class="mb-6">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-info-circle mr-2 text-primary-600"></i>Status
 </label>
 <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 transition" required>
 <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
 <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>Booked</option>
 <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
 </select>
 </div>

 <!-- Room Images Upload -->
 <div class="mb-6">
 <label class="block text-gray-700 text-sm font-bold mb-2">
 <i class="fas fa-images mr-2 text-primary-600"></i>Room Images (Multiple)
 </label>
 <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-primary-500 transition"
 onclick="document.getElementById('images').click()">
 <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
 <p class="text-gray-600">Click to select room images or drag and drop</p>
 <p class="text-sm text-gray-400 mt-1">Supports: JPG, PNG, GIF (Max 2MB each)</p>
 </div>
 <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
 <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
 </div>

 <div class="flex gap-4">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg font-semibold">
 <i class="fas fa-save mr-2"></i>Create Room
 </button>
 <a href="{{ route('admin.rooms.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-xl hover:bg-gray-600 transition font-semibold">
 <i class="fas fa-times mr-2"></i>Cancel
 </a>
 </div>
 </form>
</div>

<script>
function previewImages(input) {
 const preview = document.getElementById('image-preview');
 preview.innerHTML = '';
 
 if (input.files) {
 Array.from(input.files).forEach((file, index) => {
 const reader = new FileReader();
 reader.onload = function(e) {
 const div = document.createElement('div');
 div.className = 'relative';
 div.innerHTML = `
 <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border-2 border-gray-200">
 <span class="absolute bottom-1 left-1 bg-black/60 text-white text-xs px-2 py-1 rounded">${index + 1}</span>
 `;
 preview.appendChild(div);
 };
 reader.readAsDataURL(file);
 });
 }
}
</script>
@endsection

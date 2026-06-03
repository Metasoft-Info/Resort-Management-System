@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">New Convention Hall</h1>
 <p class="text-gray-600 mt-2">New Convention Hall Add</p>
 </div>

 <form action="{{ route('admin.convention-halls.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8">
 @csrf

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-building mr-2 text-primary-600"></i>Hall Name *
 </label>
 <input type="text" name="name" value="{{ old('name') }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
 placeholder=": Room">
 @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-align-left mr-2 text-primary-600"></i>Description
 </label>
 <textarea name="description" rows="3" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
 placeholder="Hall Details Description...">{{ old('description') }}</textarea>
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-ruler-combined mr-2 text-primary-600"></i>Dimensions (sq.ft) *
 </label>
 <input type="number" step="0.01" name="dimensions" value="{{ old('dimensions') }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
 placeholder="5000">
 @error('dimensions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-users mr-2 text-primary-600"></i>Max Capacity *
 </label>
 <input type="number" name="max_capacity" value="{{ old('max_capacity') }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
 placeholder="200">
 @error('max_capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>Rent (per day) *
 </label>
 <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day') }}" required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
 placeholder="50000">
 @error('price_per_day')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-info-circle mr-2 text-primary-600"></i>Status *
 </label>
 <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
 <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>Booked</option>
 <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
 </select>
 </div>

 <!-- Images Upload -->
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-images mr-2 text-primary-600"></i>Hall Images
 </label>
 <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition">
 <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
 <label for="imageInput" class="cursor-pointer">
 <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
 <p class="text-gray-600">Click to upload images</p>
 <p class="text-xs text-gray-400 mt-1">You can select multiple images (Max 5MB each)</p>
 </label>
 </div>
 <div id="imagePreview" class="grid grid-cols-4 gap-4 mt-4"></div>
 </div>

 <!-- Amenities -->
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-check-circle mr-2 text-primary-600"></i>Amenities
 </label>
 <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
 @foreach(['AC' => 'AC', 'Projector' => 'Projector', 'Sound System' => 'Sound System', 'Stage' => 'Stage', 'Parking' => 'Parking', 'WiFi' => 'WiFi', 'Generator' => 'Generator', 'Kitchen' => 'Kitchen'] as $key => $label)
 <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition">
 <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ in_array($key, old('amenities', [])) ? 'checked' : '' }}
 class="w-4 h-4 text-primary-600 rounded focus:ring-green-500">
 <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
 </label>
 @endforeach
 </div>
 </div>

 <!-- Event Types -->
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-calendar-check mr-2 text-primary-600"></i>Event 
 </label>
 <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
 @foreach(['Wedding' => 'Wedding', 'Conference' => 'Conference', 'Birthday' => 'Birthday', 'Meeting' => 'Meeting', 'Seminar' => 'Seminar', 'Party' => 'Party', 'Exhibition' => 'Exhibition', 'Other' => 'Other'] as $key => $label)
 <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition">
 <input type="checkbox" name="event_types[]" value="{{ $key }}" {{ in_array($key, old('event_types', [])) ? 'checked' : '' }}
 class="w-4 h-4 text-primary-600 rounded focus:ring-green-500">
 <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
 </label>
 @endforeach
 </div>
 </div>
 </div>

 <div class="flex gap-4 mt-8 pt-6 border-t">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-save mr-2"></i>Save
 </button>
 <a href="{{ route('admin.convention-halls.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
 <i class="fas fa-times mr-2"></i>Cancelled
 </a>
 </div>
 </form>
</div>

<script>
function previewImages(input) {
 const preview = document.getElementById('imagePreview');
 preview.innerHTML = '';
 
 if (input.files) {
 Array.from(input.files).forEach((file, index) => {
 const reader = new FileReader();
 reader.onload = function(e) {
 const div = document.createElement('div');
 div.className = 'relative';
 div.innerHTML = `
 <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border">
 <span class="absolute bottom-1 left-1 bg-black/50 text-white text-xs px-2 py-1 rounded">${file.name.substring(0, 15)}...</span>
 `;
 preview.appendChild(div);
 };
 reader.readAsDataURL(file);
 });
 }
}
</script>
@endsection

@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">কনভেনশন হল সম্পাদনা</h1>
        <p class="text-gray-600 mt-2">{{ $conventionHall->name }} - তথ্য আপডেট করুন</p>
    </div>

    <form action="{{ route('admin.convention-halls.update', $conventionHall) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8">
        @csrf
        @method('PUT')
        <input type="hidden" name="delete_images" id="deleteImages" value="[]">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-building mr-2 text-primary-600"></i>হলের নাম *
                </label>
                <input type="text" name="name" value="{{ old('name', $conventionHall->name) }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2 text-primary-600"></i>বর্ণনা
                </label>
                <textarea name="description" rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">{{ old('description', $conventionHall->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-ruler-combined mr-2 text-primary-600"></i>আয়তন (বর্গফুট) *
                </label>
                <input type="number" step="0.01" name="dimensions" value="{{ old('dimensions', $conventionHall->dimensions) }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                @error('dimensions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-users mr-2 text-primary-600"></i>সর্বোচ্চ ধারণক্ষমতা *
                </label>
                <input type="number" name="max_capacity" value="{{ old('max_capacity', $conventionHall->max_capacity) }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                @error('max_capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>ভাড়া (প্রতি দিন) *
                </label>
                <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day', $conventionHall->price_per_day) }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                @error('price_per_day')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>স্ট্যাটাস *
                </label>
                <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="available" {{ $conventionHall->is_available ? 'selected' : '' }}>উপলব্ধ</option>
                    <option value="booked" {{ !$conventionHall->is_available ? 'selected' : '' }}>বুকড</option>
                    <option value="maintenance">রক্ষণাবেক্ষণ</option>
                </select>
            </div>

            <!-- Existing Images -->
            @if($conventionHall->images && count($conventionHall->images) > 0)
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-images mr-2 text-primary-600"></i>বর্তমান ছবি
                </label>
                <div id="existingImages" class="grid grid-cols-4 gap-4">
                    @foreach($conventionHall->images as $image)
                    <div class="relative group" data-image="{{ $image }}">
                        <img src="{{ asset('storage/' . $image) }}" class="w-full h-24 object-cover rounded-lg border">
                        <button type="button" onclick="removeExistingImage(this, '{{ $image }}')" 
                            class="absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full text-xs opacity-0 group-hover:opacity-100 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- New Images Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-plus-circle mr-2 text-primary-600"></i>নতুন ছবি যোগ করুন
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition">
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                    <label for="imageInput" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">নতুন ছবি আপলোড করতে ক্লিক করুন</p>
                        <p class="text-xs text-gray-400 mt-1">একাধিক ছবি নির্বাচন করতে পারবেন</p>
                    </label>
                </div>
                <div id="imagePreview" class="grid grid-cols-4 gap-4 mt-4"></div>
            </div>

            <!-- Amenities -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-check-circle mr-2 text-primary-600"></i>সুবিধাসমূহ
                </label>
                @php $currentAmenities = $conventionHall->amenities ?? []; @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(['AC' => 'এসি', 'Projector' => 'প্রজেক্টর', 'Sound System' => 'সাউন্ড সিস্টেম', 'Stage' => 'মঞ্চ', 'Parking' => 'পার্কিং', 'WiFi' => 'ওয়াইফাই', 'Generator' => 'জেনারেটর', 'Kitchen' => 'রান্নাঘর'] as $key => $label)
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition">
                        <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ in_array($key, old('amenities', $currentAmenities)) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary-600 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Event Types -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check mr-2 text-primary-600"></i>ইভেন্ট ধরন
                </label>
                @php $currentEventTypes = $conventionHall->event_types ?? []; @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(['Wedding' => 'বিবাহ', 'Conference' => 'সম্মেলন', 'Birthday' => 'জন্মদিন', 'Meeting' => 'মিটিং', 'Seminar' => 'সেমিনার', 'Party' => 'পার্টি', 'Exhibition' => 'প্রদর্শনী', 'Other' => 'অন্যান্য'] as $key => $label)
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition">
                        <input type="checkbox" name="event_types[]" value="{{ $key }}" {{ in_array($key, old('event_types', $currentEventTypes)) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary-600 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex gap-4 mt-8 pt-6 border-t">
            <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
                <i class="fas fa-save mr-2"></i>আপডেট করুন
            </button>
            <a href="{{ route('admin.convention-halls.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>বাতিল
            </a>
        </div>
    </form>
</div>

<script>
let deleteImages = [];

function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border">
                    <span class="absolute bottom-1 left-1 bg-black/50 text-white text-xs px-2 py-1 rounded">New</span>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

function removeExistingImage(btn, imagePath) {
    if (confirm('এই ছবিটি মুছে ফেলতে চান?')) {
        deleteImages.push(imagePath);
        document.getElementById('deleteImages').value = JSON.stringify(deleteImages);
        btn.closest('[data-image]').remove();
    }
}
</script>
@endsection

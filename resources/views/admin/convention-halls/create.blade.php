@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">নতুন কনভেনশন হল</h1>
        <p class="text-gray-600 mt-2">নতুন কনভেনশন হল যোগ করুন</p>
    </div>

    <form action="{{ route('admin.convention-halls.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-building mr-2 text-green-600"></i>হলের নাম *
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                    placeholder="যেমন: গ্র্যান্ড বলরুম">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2 text-green-600"></i>বর্ণনা
                </label>
                <textarea name="description" rows="3" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                    placeholder="হলের বিস্তারিত বর্ণনা...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-ruler-combined mr-2 text-green-600"></i>আয়তন (বর্গফুট) *
                </label>
                <input type="number" step="0.01" name="dimensions" value="{{ old('dimensions') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                    placeholder="5000">
                @error('dimensions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-users mr-2 text-green-600"></i>সর্বোচ্চ ধারণক্ষমতা *
                </label>
                <input type="number" name="max_capacity" value="{{ old('max_capacity') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                    placeholder="200">
                @error('max_capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-bangladeshi-taka-sign mr-2 text-green-600"></i>ভাড়া (প্রতি দিন) *
                </label>
                <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                    placeholder="50000">
                @error('price_per_day')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2 text-green-600"></i>স্ট্যাটাস *
                </label>
                <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>উপলব্ধ</option>
                    <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>বুকড</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>রক্ষণাবেক্ষণ</option>
                </select>
            </div>

            <!-- Images Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-images mr-2 text-green-600"></i>হলের ছবি
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition">
                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                    <label for="imageInput" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">ছবি আপলোড করতে ক্লিক করুন</p>
                        <p class="text-xs text-gray-400 mt-1">একাধিক ছবি নির্বাচন করতে পারবেন (সর্বোচ্চ 5MB প্রতিটি)</p>
                    </label>
                </div>
                <div id="imagePreview" class="grid grid-cols-4 gap-4 mt-4"></div>
            </div>

            <!-- Amenities -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>সুবিধাসমূহ
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(['AC' => 'এসি', 'Projector' => 'প্রজেক্টর', 'Sound System' => 'সাউন্ড সিস্টেম', 'Stage' => 'মঞ্চ', 'Parking' => 'পার্কিং', 'WiFi' => 'ওয়াইফাই', 'Generator' => 'জেনারেটর', 'Kitchen' => 'রান্নাঘর'] as $key => $label)
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition">
                        <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ in_array($key, old('amenities', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Event Types -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check mr-2 text-green-600"></i>ইভেন্ট ধরন
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(['Wedding' => 'বিবাহ', 'Conference' => 'সম্মেলন', 'Birthday' => 'জন্মদিন', 'Meeting' => 'মিটিং', 'Seminar' => 'সেমিনার', 'Party' => 'পার্টি', 'Exhibition' => 'প্রদর্শনী', 'Other' => 'অন্যান্য'] as $key => $label)
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-green-50 transition">
                        <input type="checkbox" name="event_types[]" value="{{ $key }}" {{ in_array($key, old('event_types', [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex gap-4 mt-8 pt-6 border-t">
            <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg">
                <i class="fas fa-save mr-2"></i>সংরক্ষণ করুন
            </button>
            <a href="{{ route('admin.convention-halls.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>বাতিল
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

@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">নতুন অ্যাডঅন সার্ভিস</h1>
        <p class="text-gray-600 mt-2">রুম বা কনভেনশন বুকিং এর জন্য নতুন অতিরিক্ত সেবা যোগ করুন</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.addon-services.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সার্ভিসের নাম *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="যেমন: এক্সট্রা বেড, সাউন্ড সিস্টেম">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সার্ভিস ধরন *</label>
                    <select name="service_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">-- নির্বাচন করুন --</option>
                        <option value="room" {{ old('service_type') == 'room' ? 'selected' : '' }}>🛏️ শুধু রুম বুকিং</option>
                        <option value="convention" {{ old('service_type') == 'convention' ? 'selected' : '' }}>🏛️ শুধু কনভেনশন বুকিং</option>
                        <option value="both" {{ old('service_type') == 'both' ? 'selected' : '' }}>🔄 উভয় বুকিং</option>
                    </select>
                    @error('service_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ক্যাটাগরি *</label>
                    <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">-- নির্বাচন করুন --</option>
                        <optgroup label="রুম সার্ভিস">
                            <option value="room_service" {{ old('category') == 'room_service' ? 'selected' : '' }}>রুম সার্ভিস</option>
                            <option value="laundry" {{ old('category') == 'laundry' ? 'selected' : '' }}>লন্ড্রি</option>
                            <option value="parking" {{ old('category') == 'parking' ? 'selected' : '' }}>পার্কিং</option>
                        </optgroup>
                        <optgroup label="ইভেন্ট সার্ভিস">
                            <option value="decoration" {{ old('category') == 'decoration' ? 'selected' : '' }}>সাজসজ্জা</option>
                            <option value="sound_system" {{ old('category') == 'sound_system' ? 'selected' : '' }}>সাউন্ড সিস্টেম</option>
                            <option value="photography" {{ old('category') == 'photography' ? 'selected' : '' }}>ফটোগ্রাফি</option>
                            <option value="catering" {{ old('category') == 'catering' ? 'selected' : '' }}>ক্যাটারিং</option>
                        </optgroup>
                        <optgroup label="অন্যান্য">
                            <option value="transport" {{ old('category') == 'transport' ? 'selected' : '' }}>পরিবহন</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>অন্যান্য</option>
                        </optgroup>
                    </select>
                    @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মূল্য (৳) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="0.00">
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইউনিট</label>
                    <input type="text" name="unit" value="{{ old('unit') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="যেমন: প্রতি ঘণ্টা, প্রতি দিন, প্রতি পিস">
                    @error('unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm font-semibold text-gray-700">সক্রিয় (বুকিং এ দেখাবে)</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">বর্ণনা</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="সার্ভিসের বিস্তারিত বর্ণনা...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>সংরক্ষণ করুন
                </button>
                <a href="{{ route('admin.addon-services.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-times mr-2"></i>বাতিল
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

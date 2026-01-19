@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">সিস্টেম সেটিংস</h1>
        <p class="text-gray-600 mt-2">রিসোর্ট তথ্য ও সেটিংস পরিচালনা</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রিসোর্টের নাম *</label>
                    <input type="text" name="resort_name" value="{{ old('resort_name', $settings->resort_name ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="text" name="resort_phone" value="{{ old('resort_phone', $settings->resort_phone ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল *</label>
                    <input type="email" name="resort_email" value="{{ old('resort_email', $settings->resort_email ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ওয়েবসাইট</label>
                    <input type="url" name="resort_website" value="{{ old('resort_website', $settings->resort_website ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা *</label>
                    <textarea name="resort_address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('resort_address', $settings->resort_address ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সুবিধা সমূহ</label>
                    <textarea name="facilities" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="সুইমিং পুল, রেস্টুরেন্ট, পার্কিং">{{ old('facilities', $settings->facilities ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সম্পর্কে</label>
                    <textarea name="about" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('about', $settings->about ?? '') }}</textarea>
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>সেটিংস সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">সিস্টেম সেটিংস</h1>
        <p class="text-gray-600 mt-2">রিসোর্ট তথ্য ও সেটিংস পরিচালনা</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.settings.resort-info') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">রিসোর্টের নাম *</label>
                    <input type="text" name="resort_name" value="{{ old('resort_name', $resortInfo->resort_name ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ট্যাগলাইন</label>
                    <input type="text" name="resort_tagline" value="{{ old('resort_tagline', $resortInfo->resort_tagline ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="তুফান রিসোর্ট">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফোন নম্বর *</label>
                    <input type="text" name="phone" value="{{ old('phone', $resortInfo->phone ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ইমেইল *</label>
                    <input type="email" name="email" value="{{ old('email', $resortInfo->email ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ঠিকানা *</label>
                    <textarea name="address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('address', $resortInfo->address ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সুবিধা সমূহ (কমা দিয়ে আলাদা করুন)</label>
                    <textarea name="facilities" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="সুইমিং পুল, রেস্টুরেন্ট, পার্কিং">{{ old('facilities', is_array($resortInfo->facilities ?? '') ? implode(', ', $resortInfo->facilities) : ($resortInfo->facilities ?? '')) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সম্পর্কে</label>
                    <textarea name="about_text" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('about_text', $resortInfo->about_text ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">মিশন</label>
                    <textarea name="mission_text" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('mission_text', $resortInfo->mission_text ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ফুটার বর্ণনা</label>
                    <textarea name="footer_description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Premium accommodation and event hosting services...">{{ old('footer_description', $resortInfo->footer_description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $resortInfo->social_links['facebook'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://facebook.com/yourpage">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $resortInfo->social_links['instagram'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://instagram.com/yourpage">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter URL</label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $resortInfo->social_links['twitter'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="https://twitter.com/yourpage">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ম্যাপ এমবেড URL</label>
                    <input type="url" name="map_embed_url" value="{{ old('map_embed_url', $resortInfo->map_embed_url ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">কপিরাইট টেক্সট</label>
                    <input type="text" name="copyright_text" value="{{ old('copyright_text', $resortInfo->copyright_text ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="© 2026 Tufan Resort. All rights reserved.">
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

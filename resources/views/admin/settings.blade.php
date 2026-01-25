@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">সিস্টেম সেটিংস</h1>
        <p class="text-gray-600 mt-2">রিসোর্ট তথ্য, নেভবার, ফুটার ও সেটিংস পরিচালনা</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8">
            <button onclick="showTab('resort')" id="tab-resort" class="tab-btn py-4 px-1 border-b-2 border-blue-500 font-semibold text-blue-600">
                <i class="fas fa-hotel mr-2"></i>রিসোর্ট তথ্য
            </button>
            <button onclick="showTab('navbar')" id="tab-navbar" class="tab-btn py-4 px-1 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars mr-2"></i>নেভবার লিংক
            </button>
            <button onclick="showTab('footer')" id="tab-footer" class="tab-btn py-4 px-1 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700">
                <i class="fas fa-shoe-prints mr-2"></i>ফুটার সেকশন
            </button>
        </nav>
    </div>

    <!-- Resort Info Tab -->
    <div id="content-resort" class="tab-content">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.settings.resort-info') }}" method="POST">
                @csrf
                <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-3"></i>মূল তথ্য
                </h3>
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
                        <textarea name="facilities" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="সুইমিং পুল, রেস্টুরেন্ট, পার্কিং">{{ old('facilities', is_array($resortInfo->facilities ?? '') ? implode(', ', $resortInfo->facilities) : ($resortInfo->facilities ?? '')) }}</textarea>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-700 mb-6 mt-8 flex items-center border-t pt-6">
                    <i class="fas fa-file-alt text-green-500 mr-3"></i>বিবরণ
                </h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">সম্পর্কে</label>
                        <textarea name="about_text" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('about_text', $resortInfo->about_text ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">মিশন</label>
                        <textarea name="mission_text" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('mission_text', $resortInfo->mission_text ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ফুটার বর্ণনা</label>
                        <textarea name="footer_description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('footer_description', $resortInfo->footer_description ?? '') }}</textarea>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-700 mb-6 mt-8 flex items-center border-t pt-6">
                    <i class="fab fa-facebook text-blue-600 mr-3"></i>সোশ্যাল মিডিয়া
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                </div>

                <h3 class="text-xl font-bold text-gray-700 mb-6 mt-8 flex items-center border-t pt-6">
                    <i class="fas fa-map-marker-alt text-red-500 mr-3"></i>অন্যান্য
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ম্যাপ এমবেড URL</label>
                        <input type="url" name="map_embed_url" value="{{ old('map_embed_url', $resortInfo->map_embed_url ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
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

    <!-- Navbar Links Tab -->
    <div id="content-navbar" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Add New Navbar Link -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-green-500 mr-3"></i>নতুন নেভবার লিংক যোগ করুন
                </h3>
                <form action="{{ route('admin.settings.navbar-links.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">লেবেল *</label>
                            <input type="text" name="label" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Home">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
                            <input type="text" name="url" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="/">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">ক্রম *</label>
                            <input type="number" name="display_order" value="{{ ($navbarLinks->max('order') ?? 0) + 1 }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="navbar_is_active" checked class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <label for="navbar_is_active" class="ml-2 text-sm font-medium text-gray-700">সক্রিয়</label>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition shadow-lg">
                            <i class="fas fa-plus mr-2"></i>লিংক যোগ করুন
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Navbar Links -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-list text-blue-500 mr-3"></i>বর্তমান নেভবার লিংক
                </h3>
                <div class="space-y-3">
                    @forelse($navbarLinks as $link)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border {{ $link->is_active ? 'border-green-300' : 'border-red-300' }}">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $link->label }}</span>
                                <span class="text-sm text-gray-500 ml-2">({{ $link->url }})</span>
                                @if(!$link->is_active)
                                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 rounded text-xs">নিষ্ক্রিয়</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-400">ক্রম: {{ $link->order }}</span>
                                <form action="{{ route('admin.settings.navbar-links.destroy', $link) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('মুছে ফেলতে চান?')" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">কোনো নেভবার লিংক নেই</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Sections Tab -->
    <div id="content-footer" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Add New Footer Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-purple-500 mr-3"></i>নতুন ফুটার সেকশন যোগ করুন
                </h3>
                <form action="{{ route('admin.settings.footer-sections.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">শিরোনাম *</label>
                            <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Quick Links">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">ক্রম *</label>
                            <input type="number" name="display_order" value="{{ ($footerSections->max('order') ?? 0) + 1 }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="footer_section_is_active" checked class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <label for="footer_section_is_active" class="ml-2 text-sm font-medium text-gray-700">সক্রিয়</label>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg">
                            <i class="fas fa-plus mr-2"></i>সেকশন যোগ করুন
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Footer Sections -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-th-list text-purple-500 mr-3"></i>বর্তমান ফুটার সেকশন
                </h3>
                <div class="space-y-4">
                    @forelse($footerSections as $section)
                        <div class="p-4 bg-gray-50 rounded-lg border {{ $section->is_active ? 'border-purple-300' : 'border-red-300' }}">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="font-bold text-gray-800">{{ $section->title }}</span>
                                    @if(!$section->is_active)
                                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 rounded text-xs">নিষ্ক্রিয়</span>
                                    @endif
                                </div>
                                <form action="{{ route('admin.settings.footer-sections.destroy', $section) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('মুছে ফেলতে চান?')" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <!-- Links in this section -->
                            @if($section->links->count() > 0)
                                <div class="ml-4 space-y-1">
                                    @foreach($section->links as $link)
                                        <div class="flex items-center justify-between text-sm py-1">
                                            <span class="text-gray-600">↳ {{ $link->label }} ({{ $link->url }})</span>
                                            <form action="{{ route('admin.settings.footer-links.destroy', $link) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400 ml-4">কোনো লিংক নেই</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">কোনো ফুটার সেকশন নেই</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Add Footer Link -->
        @if($footerSections->count() > 0)
        <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-link text-indigo-500 mr-3"></i>ফুটার লিংক যোগ করুন
            </h3>
            <form action="{{ route('admin.settings.footer-links.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">সেকশন *</label>
                        <select name="footer_section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            @foreach($footerSections as $section)
                                <option value="{{ $section->id }}">{{ $section->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">লেবেল *</label>
                        <input type="text" name="label" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="About Us">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
                        <input type="text" name="url" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="/about">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ক্রম</label>
                        <input type="number" name="display_order" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-3 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-lg">
                            <i class="fas fa-plus mr-2"></i>যোগ করুন
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-blue-500', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-blue-500', 'text-blue-600');
}
</script>
@endsection

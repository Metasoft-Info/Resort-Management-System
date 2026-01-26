@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">সিস্টেম সেটিংস</h1>
        <p class="text-gray-600 mt-2">রিসোর্ট তথ্য, লোগো, মেনু, নেভবার ও ফুটার পরিচালনা</p>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200 overflow-x-auto">
        <nav class="flex space-x-4 min-w-max">
            <button onclick="showTab('resort')" id="tab-resort" class="tab-btn py-4 px-3 border-b-2 border-blue-500 font-semibold text-blue-600 whitespace-nowrap">
                <i class="fas fa-hotel mr-2"></i>রিসোর্ট তথ্য
            </button>
            <button onclick="showTab('logos')" id="tab-logos" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-image mr-2"></i>লোগো সমূহ
            </button>
            <button onclick="showTab('menus')" id="tab-menus" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-th-list mr-2"></i>অ্যাডমিন মেনু
            </button>
            <button onclick="showTab('navbar')" id="tab-navbar" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-bars mr-2"></i>নেভবার লিংক
            </button>
            <button onclick="showTab('footer')" id="tab-footer" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-shoe-prints mr-2"></i>ফুটার সেকশন
            </button>
            <button onclick="showTab('reset')" id="tab-reset" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
                <i class="fas fa-trash-alt mr-2"></i>ডাটা রিসেট
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
                        <input type="text" name="resort_tagline" value="{{ old('resort_tagline', $resortInfo->resort_tagline ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                        <textarea name="facilities" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('facilities', is_array($resortInfo->facilities ?? '') ? implode(', ', $resortInfo->facilities) : ($resortInfo->facilities ?? '')) }}</textarea>
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
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $resortInfo->social_links['facebook'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $resortInfo->social_links['instagram'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter URL</label>
                        <input type="url" name="twitter_url" value="{{ old('twitter_url', $resortInfo->social_links['twitter'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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
                        <input type="text" name="copyright_text" value="{{ old('copyright_text', $resortInfo->copyright_text ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
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

    <!-- Logos Tab -->
    <div id="content-logos" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
                <i class="fas fa-images text-purple-500 mr-3"></i>ওয়েবসাইট লোগো পরিচালনা
            </h3>
            <form action="{{ route('admin.settings.logos.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Header Logo -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition">
                        <div class="mb-4"><i class="fas fa-heading text-4xl text-blue-500"></i></div>
                        <h4 class="font-bold text-lg text-gray-700 mb-2">হেডার লোগো</h4>
                        <p class="text-sm text-gray-500 mb-4">ওয়েবসাইটের উপরে (প্রস্তাবিত: 200x60px)</p>
                        @if($resortInfo->header_logo ?? null)
                            <div class="mb-4 bg-gray-100 p-4 rounded-lg inline-block">
                                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="Header Logo" class="max-h-16 mx-auto">
                            </div>
                        @endif
                        <input type="file" name="header_logo" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>

                    <!-- Footer Logo -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-400 transition">
                        <div class="mb-4"><i class="fas fa-shoe-prints text-4xl text-green-500"></i></div>
                        <h4 class="font-bold text-lg text-gray-700 mb-2">ফুটার লোগো</h4>
                        <p class="text-sm text-gray-500 mb-4">ওয়েবসাইটের নিচে (প্রস্তাবিত: 200x60px)</p>
                        @if($resortInfo->footer_logo ?? null)
                            <div class="mb-4 bg-gray-800 p-4 rounded-lg inline-block">
                                <img src="{{ asset('storage/' . $resortInfo->footer_logo) }}" alt="Footer Logo" class="max-h-16 mx-auto">
                            </div>
                        @endif
                        <input type="file" name="footer_logo" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>

                    <!-- Admin Logo -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition">
                        <div class="mb-4"><i class="fas fa-user-shield text-4xl text-purple-500"></i></div>
                        <h4 class="font-bold text-lg text-gray-700 mb-2">অ্যাডমিন প্যানেল লোগো</h4>
                        <p class="text-sm text-gray-500 mb-4">সাইডবারে দেখাবে (প্রস্তাবিত: 150x50px)</p>
                        @if($resortInfo->admin_logo ?? null)
                            <div class="mb-4 bg-purple-900 p-4 rounded-lg inline-block">
                                <img src="{{ asset('storage/' . $resortInfo->admin_logo) }}" alt="Admin Logo" class="max-h-12 mx-auto">
                            </div>
                        @endif
                        <input type="file" name="admin_logo" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>

                    <!-- Favicon -->
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition">
                        <div class="mb-4"><i class="fas fa-star text-4xl text-orange-500"></i></div>
                        <h4 class="font-bold text-lg text-gray-700 mb-2">ফেভিকন</h4>
                        <p class="text-sm text-gray-500 mb-4">ব্রাউজার ট্যাবে (প্রস্তাবিত: 32x32px)</p>
                        @if($resortInfo->favicon ?? null)
                            <div class="mb-4 bg-gray-100 p-4 rounded-lg inline-block">
                                <img src="{{ asset('storage/' . $resortInfo->favicon) }}" alt="Favicon" class="max-h-8 mx-auto">
                            </div>
                        @endif
                        <input type="file" name="favicon" accept="image/*,.ico" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition shadow-lg">
                        <i class="fas fa-save mr-2"></i>লোগো আপডেট করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Menus Tab -->
    <div id="content-menus" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-700 flex items-center">
                    <i class="fas fa-th-list text-indigo-500 mr-3"></i>অ্যাডমিন সাইডবার মেনু সেটিংস
                </h3>
                <form action="{{ route('admin.settings.menus.seed') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-sync mr-2"></i>ডিফল্ট মেনু লোড করুন
                    </button>
                </form>
            </div>
            
            <p class="text-gray-600 mb-6">সাইডবার মেনু সক্রিয়/নিষ্ক্রিয় করুন। নিষ্ক্রিয় মেনু কেউ দেখতে পাবে না।</p>

            <form action="{{ route('admin.settings.menus.update') }}" method="POST">
                @csrf
                @php $groupedMenus = $menuSettings ?? collect(); @endphp
                
                @if($groupedMenus->isEmpty())
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <i class="fas fa-exclamation-circle text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">কোনো মেনু নেই। "ডিফল্ট মেনু লোড করুন" ক্লিক করুন।</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($groupedMenus as $groupName => $menus)
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200">
                                    <h4 class="font-bold text-gray-700">
                                        <i class="fas fa-folder mr-2 text-yellow-500"></i>
                                        {{ $groupName ?: 'প্রধান মেনু' }}
                                    </h4>
                                </div>
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($menus as $menu)
                                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $menu->is_active ? 'border-green-300 bg-green-50' : 'border-gray-200' }} {{ $menu->is_system ? 'opacity-60' : '' }}">
                                            <input type="checkbox" name="active_menus[]" value="{{ $menu->menu_key }}" 
                                                {{ $menu->is_active ? 'checked' : '' }}
                                                {{ $menu->is_system ? 'disabled checked' : '' }}
                                                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 mr-3">
                                            <div class="flex-1">
                                                <span class="flex items-center font-semibold text-gray-800">
                                                    <i class="{{ $menu->menu_icon }} w-5 mr-2 text-gray-500"></i>
                                                    {{ $menu->menu_label }}
                                                </span>
                                                @if($menu->is_system)
                                                    <span class="text-xs text-blue-600"><i class="fas fa-lock mr-1"></i>সিস্টেম</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-4 mt-8">
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-8 py-3 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-lg">
                            <i class="fas fa-save mr-2"></i>মেনু সেটিংস সংরক্ষণ করুন
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Navbar Links Tab -->
    <div id="content-navbar" class="tab-content hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-green-500 mr-3"></i>নতুন নেভবার লিংক
                </h3>
                <form action="{{ route('admin.settings.navbar-links.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">লেবেল *</label>
                            <input type="text" name="label" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
                            <input type="text" name="url" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
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

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-list text-blue-500 mr-3"></i>বর্তমান লিংক
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
                            <form action="{{ route('admin.settings.navbar-links.destroy', $link) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'মুছে ফেলতে চান?')" class="text-red-600 hover:text-red-800 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-purple-500 mr-3"></i>নতুন ফুটার সেকশন
                </h3>
                <form action="{{ route('admin.settings.footer-sections.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">শিরোনাম *</label>
                            <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
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

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-th-list text-purple-500 mr-3"></i>বর্তমান সেকশন
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
                                    <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'মুছে ফেলতে চান?')" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @if($section->links->count() > 0)
                                <div class="ml-4 space-y-1">
                                    @foreach($section->links as $link)
                                        <div class="flex items-center justify-between text-sm py-1">
                                            <span class="text-gray-600">↳ {{ $link->label }}</span>
                                            <form action="{{ route('admin.settings.footer-links.destroy', $link) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">কোনো ফুটার সেকশন নেই</p>
                    @endforelse
                </div>
            </div>
        </div>

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
                        <select name="footer_section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                            @foreach($footerSections as $section)
                                <option value="{{ $section->id }}">{{ $section->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">লেবেল *</label>
                        <input type="text" name="label" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
                        <input type="text" name="url" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ক্রম</label>
                        <input type="number" name="display_order" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
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

    <!-- Data Reset Tab -->
    <div id="content-reset" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-red-700 mb-2 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>সতর্কতা: ডাটা রিসেট
                </h3>
                <p class="text-gray-600">এই অপশনগুলো ব্যবহার করলে নির্বাচিত ডাটা স্থায়ীভাবে মুছে যাবে। এই কাজ পূর্বাবস্থায় ফেরানো যাবে না!</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reset Room Bookings -->
                <div class="border-2 border-yellow-200 bg-yellow-50 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-yellow-800 mb-2">
                        <i class="fas fa-bed mr-2"></i>রুম বুকিং রিসেট
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">সমস্ত রুম বুকিং, পেমেন্ট এবং অতিরিক্ত গেস্ট তথ্য মুছে ফেলা হবে।</p>
                    <form action="{{ route('admin.settings.reset.room-bookings') }}" method="POST" onsubmit="return confirmReset('Room Bookings')">
                        @csrf
                        <input type="hidden" name="confirm" value="RESET">
                        <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-3 rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-trash mr-2"></i>রুম বুকিং রিসেট করুন
                        </button>
                    </form>
                </div>

                <!-- Reset Convention Bookings -->
                <div class="border-2 border-orange-200 bg-orange-50 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-orange-800 mb-2">
                        <i class="fas fa-building mr-2"></i>কনভেনশন বুকিং রিসেট
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">সমস্ত কনভেনশন হল বুকিং এবং পেমেন্ট তথ্য মুছে ফেলা হবে।</p>
                    <form action="{{ route('admin.settings.reset.convention-bookings') }}" method="POST" onsubmit="return confirmReset('Convention Bookings')">
                        @csrf
                        <input type="hidden" name="confirm" value="RESET">
                        <button type="submit" class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-trash mr-2"></i>কনভেনশন বুকিং রিসেট করুন
                        </button>
                    </form>
                </div>

                <!-- Reset All Bookings -->
                <div class="border-2 border-red-200 bg-red-50 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-red-800 mb-2">
                        <i class="fas fa-calendar-times mr-2"></i>সব বুকিং রিসেট
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">রুম এবং কনভেনশন উভয় বুকিং সহ সমস্ত বুকিং ডাটা মুছে ফেলা হবে।</p>
                    <form action="{{ route('admin.settings.reset.all-bookings') }}" method="POST" onsubmit="return confirmReset('ALL Bookings')">
                        @csrf
                        <input type="hidden" name="confirm" value="RESET">
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash-alt mr-2"></i>সব বুকিং রিসেট করুন
                        </button>
                    </form>
                </div>

                <!-- Clear Activity Logs -->
                <div class="border-2 border-gray-200 bg-gray-50 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-2">
                        <i class="fas fa-history mr-2"></i>অ্যাক্টিভিটি লগ ক্লিয়ার
                    </h4>
                    <p class="text-sm text-gray-600 mb-4">সমস্ত সিস্টেম অ্যাক্টিভিটি লগ মুছে ফেলা হবে।</p>
                    <form action="{{ route('admin.settings.clear.activity-logs') }}" method="POST" onsubmit="return confirmClear('Activity Logs')">
                        @csrf
                        <input type="hidden" name="confirm" value="CLEAR">
                        <button type="submit" class="w-full bg-gray-600 text-white px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-eraser mr-2"></i>লগ ক্লিয়ার করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmReset(type) {
    return confirm('⚠️ WARNING!\n\nAre you sure you want to reset ' + type + '?\n\nThis action CANNOT be undone!\n\nType confirmation is required.');
}
function confirmClear(type) {
    return confirm('Are you sure you want to clear ' + type + '?\n\nThis action cannot be undone.');
}
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-blue-500', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-blue-500', 'text-blue-600');
}
</script>
@endsection

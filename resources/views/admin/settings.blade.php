@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
 <p class="text-gray-600 mt-2">Manage Resort Information, Logo, Menu, Navbar and Footer</p>
 </div>

 <!-- Tab Navigation -->
 <div class="mb-6 border-b border-gray-200 overflow-x-auto">
 <nav class="flex space-x-4 min-w-max">
 <button onclick="showTab('resort')" id="tab-resort" class="tab-btn py-4 px-3 border-b-2 border-primary-500 font-semibold text-primary-600 whitespace-nowrap">
 <i class="fas fa-hotel mr-2"></i>Resort Information
 </button>
 <button onclick="showTab('logos')" id="tab-logos" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
 <i class="fas fa-image mr-2"></i>Logo 
 </button>
 <button onclick="showTab('menus')" id="tab-menus" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
 <i class="fas fa-th-list mr-2"></i>Admin Menu
 </button>
 <button onclick="showTab('navbar')" id="tab-navbar" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
 <i class="fas fa-bars mr-2"></i>Navbar Links
 </button>
 <button onclick="showTab('footer')" id="tab-footer" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
 <i class="fas fa-shoe-prints mr-2"></i>Footer Section
 </button>
 <button onclick="showTab('reset')" id="tab-reset" class="tab-btn py-4 px-3 border-b-2 border-transparent font-semibold text-gray-500 hover:text-gray-700 whitespace-nowrap">
 <i class="fas fa-trash-alt mr-2"></i>Data Reset
 </button>
 </nav>
 </div>

 <!-- Resort Info Tab -->
 <div id="content-resort" class="tab-content">
 <div class="bg-white rounded-xl shadow-lg p-8">
 
 @if($errors->any())
 <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
 <p class="font-bold">Error:</p>
 <ul>
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif
 
 @if(session('success'))
 <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
 <p class="font-bold">Success:</p>
 <p>{{ session('success') }}</p>
 </div>
 @endif
 
 <form action="{{ route('admin.settings.resort-info') }}" method="POST">
 @csrf
 <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
 <i class="fas fa-info-circle text-primary-500 mr-3"></i>Main Information
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Resort Name *</label>
 <input type="text" name="resort_name" value="{{ old('resort_name', $resortInfo->resort_name ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Tagline</label>
 <input type="text" name="resort_tagline" value="{{ old('resort_tagline', $resortInfo->resort_tagline ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
 <input type="text" name="phone" value="{{ old('phone', $resortInfo->phone ?? '') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
 <input type="email" name="email" value="{{ $resortInfo->email ?? '' }}" required autocomplete="off" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Address *</label>
 <textarea name="address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('address', $resortInfo->address ?? '') }}</textarea>
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Amenities (Separate by comma)</label>
 <textarea name="facilities" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('facilities', is_array($resortInfo->facilities ?? '') ? implode(', ', $resortInfo->facilities) : ($resortInfo->facilities ?? '')) }}</textarea>
 </div>
 </div>

 <h3 class="text-xl font-bold text-gray-700 mb-6 mt-8 flex items-center border-t pt-6">
 <i class="fas fa-file-alt text-green-500 mr-3"></i>Description
 </h3>
 <div class="grid grid-cols-1 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">About</label>
 <textarea name="about_text" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('about_text', $resortInfo->about_text ?? '') }}</textarea>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Mission</label>
 <textarea name="mission_text" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('mission_text', $resortInfo->mission_text ?? '') }}</textarea>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Description</label>
 <textarea name="footer_description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">{{ old('footer_description', $resortInfo->footer_description ?? '') }}</textarea>
 </div>
 </div>

 <h3 class="text-xl font-bold text-gray-700 mb-6 mt-8 flex items-center border-t pt-6">
 <i class="fab fa-facebook text-primary-600 mr-3"></i>Social Media
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
 <input type="url" name="facebook_url" value="{{ old('facebook_url', $resortInfo->social_links['facebook'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
 <input type="url" name="instagram_url" value="{{ old('instagram_url', $resortInfo->social_links['instagram'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter URL</label>
 <input type="url" name="twitter_url" value="{{ old('twitter_url', $resortInfo->social_links['twitter'] ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 </div>

 <h3 class="text-xl font-bold text-gray-700 mb-6 mt-8 flex items-center border-t pt-6">
 <i class="fas fa-map-marker-alt text-red-500 mr-3"></i>Other
 </h3>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Map Embed URL</label>
 <input type="url" name="map_embed_url" value="{{ old('map_embed_url', $resortInfo->map_embed_url ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Copyright Text</label>
 <input type="text" name="copyright_text" value="{{ old('copyright_text', $resortInfo->copyright_text ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 </div>

 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-save mr-2"></i>Save Settings
 </button>
 </div>
 </form>
 </div>
 </div>

 <!-- Logos Tab -->
 <div id="content-logos" class="tab-content hidden">
 <div class="bg-white rounded-xl shadow-lg p-8">
 <h3 class="text-xl font-bold text-gray-700 mb-6 flex items-center">
 <i class="fas fa-images text-primary-500 mr-3"></i>Manage Website Logos
 </h3>
 <form action="{{ route('admin.settings.logos.update') }}" method="POST" enctype="multipart/form-data">
 @csrf
 <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
 <!-- Header Logo -->
 <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition">
 <div class="mb-4"><i class="fas fa-heading text-4xl text-primary-500"></i></div>
 <h4 class="font-bold text-lg text-gray-700 mb-2">Header Logo</h4>
 <p class="text-sm text-gray-500 mb-4">At top of website (Recommended: 200x60px)</p>
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
 <h4 class="font-bold text-lg text-gray-700 mb-2">Footer Logo</h4>
 <p class="text-sm text-gray-500 mb-4">At bottom of website (Recommended: 200x60px)</p>
 @if($resortInfo->footer_logo ?? null)
 <div class="mb-4 bg-gray-800 p-4 rounded-lg inline-block">
 <img src="{{ asset('storage/' . $resortInfo->footer_logo) }}" alt="Footer Logo" class="max-h-16 mx-auto">
 </div>
 @endif
 <input type="file" name="footer_logo" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 </div>

 <!-- Admin Logo -->
 <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition">
 <div class="mb-4"><i class="fas fa-user-shield text-4xl text-primary-500"></i></div>
 <h4 class="font-bold text-lg text-gray-700 mb-2">Admin Panel Logo</h4>
 <p class="text-sm text-gray-500 mb-4">Shown in sidebar (Recommended: 150x50px)</p>
 @if($resortInfo->admin_logo ?? null)
 <div class="mb-4 bg-purple-900 p-4 rounded-lg inline-block">
 <img src="{{ asset('storage/' . $resortInfo->admin_logo) }}" alt="Admin Logo" class="max-h-12 mx-auto">
 </div>
 @endif
 <input type="file" name="admin_logo" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 </div>

 <!-- Favicon -->
 <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary-400 transition">
 <div class="mb-4"><i class="fas fa-star text-4xl text-primary-500"></i></div>
 <h4 class="font-bold text-lg text-gray-700 mb-2">Favicon</h4>
 <p class="text-sm text-gray-500 mb-4">In browser tab (Recommended: 32x32px)</p>
 @if($resortInfo->favicon ?? null)
 <div class="mb-4 bg-gray-100 p-4 rounded-lg inline-block">
 <img src="{{ asset('storage/' . $resortInfo->favicon) }}" alt="Favicon" class="max-h-8 mx-auto">
 </div>
 @endif
 <input type="file" name="favicon" accept="image/*,.ico" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 </div>
 </div>

 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-save mr-2"></i>Logo Update
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
 <i class="fas fa-th-list text-primary-500 mr-3"></i>Admin Sidebar Menu Settings
 </h3>
 <form action="{{ route('admin.settings.menus.seed') }}" method="POST" class="inline">
 @csrf
 <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
 <i class="fas fa-sync mr-2"></i>Load Default Menu
 </button>
 </form>
 </div>
 
 <p class="text-gray-600 mb-6">Active/Deactivate sidebar menus. Inactive menus are hidden No।</p>

 <form action="{{ route('admin.settings.menus.update') }}" method="POST">
 @csrf
 @php $groupedMenus = $menuSettings ?? collect(); @endphp
 
 @if($groupedMenus->isEmpty())
 <div class="text-center py-8 bg-gray-50 rounded-lg">
 <i class="fas fa-exclamation-circle text-4xl text-gray-400 mb-4"></i>
 <p class="text-gray-600">No menus found. Click "Load Default Menu" to load.</p>
 </div>
 @else
 <div class="space-y-6">
 @foreach($groupedMenus as $groupName => $menus)
 <div class="border border-gray-200 rounded-xl overflow-hidden">
 <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200">
 <h4 class="font-bold text-gray-700">
 <i class="fas fa-folder mr-2 text-yellow-500"></i>
 {{ $groupName ?: 'Main Menu' }}
 </h4>
 </div>
 <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
 @foreach($menus as $menu)
 <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $menu->is_active ? 'border-green-300 bg-green-50' : 'border-gray-200' }} {{ $menu->is_system ? 'opacity-60' : '' }}">
 <input type="checkbox" name="active_menus[]" value="{{ $menu->menu_key }}" 
 {{ $menu->is_active ? 'checked' : '' }}
 {{ $menu->is_system ? 'disabled checked' : '' }}
 class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-green-500 mr-3">
 <div class="flex-1">
 <span class="flex items-center font-semibold text-gray-800">
 <i class="{{ $menu->menu_icon }} w-5 mr-2 text-gray-500"></i>
 {{ $menu->menu_label }}
 </span>
 @if($menu->is_system)
 <span class="text-xs text-primary-600"><i class="fas fa-lock mr-1"></i>System</span>
 @endif
 </div>
 </label>
 @endforeach
 </div>
 </div>
 @endforeach
 </div>

 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-save mr-2"></i>Menu Save Settings
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
 <i class="fas fa-plus-circle text-green-500 mr-3"></i>Add Navbar Link
 </h3>
 <form action="{{ route('admin.settings.navbar-links.store') }}" method="POST">
 @csrf
 <div class="space-y-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Label *</label>
 <input type="text" name="label" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
 <input type="text" name="url" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Order *</label>
 <input type="number" name="display_order" value="{{ ($navbarLinks->max('order') ?? 0) + 1 }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
 </div>
 <div class="flex items-center">
 <input type="checkbox" name="is_active" id="navbar_is_active" checked class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-green-500">
 <label for="navbar_is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
 </div>
 <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-plus mr-2"></i>Add Link
 </button>
 </div>
 </form>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6">
 <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
 <i class="fas fa-list text-primary-500 mr-3"></i>Current Links
 </h3>
 <div class="space-y-3">
 @forelse($navbarLinks as $link)
 <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border {{ $link->is_active ? 'border-green-300' : 'border-red-300' }}">
 <div>
 <span class="font-semibold text-gray-800">{{ $link->label }}</span>
 <span class="text-sm text-gray-500 ml-2">({{ $link->url }})</span>
 @if(!$link->is_active)
 <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 rounded text-xs">Inactive</span>
 @endif
 </div>
 <form action="{{ route('admin.settings.navbar-links.destroy', $link) }}" method="POST" class="inline">
 @csrf @method('DELETE')
 <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'Do you want to delete?')" class="text-red-600 hover:text-red-800 p-2">
 <i class="fas fa-trash"></i>
 </button>
 </form>
 </div>
 @empty
 <p class="text-gray-500 text-center py-4">No navbar links found</p>
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
 <i class="fas fa-plus-circle text-primary-500 mr-3"></i>Add Footer Section
 </h3>
 <form action="{{ route('admin.settings.footer-sections.store') }}" method="POST">
 @csrf
 <div class="space-y-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
 <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Order *</label>
 <input type="number" name="display_order" value="{{ ($footerSections->max('order') ?? 0) + 1 }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 </div>
 <div class="flex items-center">
 <input type="checkbox" name="is_active" id="footer_section_is_active" checked class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
 <label for="footer_section_is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
 </div>
 <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-plus mr-2"></i>Add Section
 </button>
 </div>
 </form>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6">
 <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
 <i class="fas fa-th-list text-primary-500 mr-3"></i>Current Sections
 </h3>
 <div class="space-y-4">
 @forelse($footerSections as $section)
 <div class="p-4 bg-gray-50 rounded-lg border {{ $section->is_active ? 'border-purple-300' : 'border-red-300' }}">
 <div class="flex items-center justify-between mb-3">
 <div>
 <span class="font-bold text-gray-800">{{ $section->title }}</span>
 @if(!$section->is_active)
 <span class="ml-2 px-2 py-1 bg-red-100 text-red-600 rounded text-xs">Inactive</span>
 @endif
 </div>
 <form action="{{ route('admin.settings.footer-sections.destroy', $section) }}" method="POST" class="inline">
 @csrf @method('DELETE')
 <button type="submit" onclick="event.preventDefault(); confirmDelete(this.form, 'Do you want to delete?')" class="text-red-600 hover:text-red-800 p-2">
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
 <p class="text-gray-500 text-center py-4">No footer sections found</p>
 @endforelse
 </div>
 </div>
 </div>

 @if($footerSections->count() > 0)
 <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
 <h3 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
 <i class="fas fa-link text-primary-500 mr-3"></i>Footer Add Link
 </h3>
 <form action="{{ route('admin.settings.footer-links.store') }}" method="POST">
 @csrf
 <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Section *</label>
 <select name="footer_section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 @foreach($footerSections as $section)
 <option value="{{ $section->id }}">{{ $section->title }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Label *</label>
 <input type="text" name="label" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">URL *</label>
 <input type="text" name="url" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
 <input type="number" name="display_order" value="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
 </div>
 <div class="flex items-end">
 <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-plus mr-2"></i>Add
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
 <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>Warning: Data Reset
 </h3>
 <p class="text-gray-600">Using these options will permanently delete data। Before doing this No!</p>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <!-- Reset Room Bookings -->
 <div class="border-2 border-yellow-200 bg-yellow-50 rounded-xl p-6">
 <h4 class="text-lg font-bold text-yellow-800 mb-2">
 <i class="fas fa-bed mr-2"></i>Room Booking Reset
 </h4>
 <p class="text-sm text-gray-600 mb-4">All Room Booking, Payment and Additional Guest Information ।</p>
 <form action="{{ route('admin.settings.reset.room-bookings') }}" method="POST" onsubmit="return confirmReset('Room Bookings')">
 @csrf
 <input type="hidden" name="confirm" value="RESET">
 <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-3 rounded-lg hover:bg-yellow-700 transition">
 <i class="fas fa-trash mr-2"></i>Reset Room Bookings
 </button>
 </form>
 </div>

 <!-- Reset Convention Bookings -->
 <div class="border-2 border-primary-200 bg-primary-50 rounded-xl p-6">
 <h4 class="text-lg font-bold text-primary-800 mb-2">
 <i class="fas fa-building mr-2"></i>Convention Booking Reset
 </h4>
 <p class="text-sm text-gray-600 mb-4">All Convention Hall Booking and Payment Information ।</p>
 <form action="{{ route('admin.settings.reset.convention-bookings') }}" method="POST" onsubmit="return confirmReset('Convention Bookings')">
 @csrf
 <input type="hidden" name="confirm" value="RESET">
 <button type="submit" class="w-full bg-primary-600 text-white px-4 py-3 rounded-lg hover:bg-primary-700 transition">
 <i class="fas fa-trash mr-2"></i>Reset Convention Bookings
 </button>
 </form>
 </div>

 <!-- Reset All Bookings -->
 <div class="border-2 border-red-200 bg-red-50 rounded-xl p-6">
 <h4 class="text-lg font-bold text-red-800 mb-2">
 <i class="fas fa-calendar-times mr-2"></i>All Booking Reset
 </h4>
 <p class="text-sm text-gray-600 mb-4">Both Room and Convention Booking all Booking ।</p>
 <form action="{{ route('admin.settings.reset.all-bookings') }}" method="POST" onsubmit="return confirmReset('ALL Bookings')">
 @csrf
 <input type="hidden" name="confirm" value="RESET">
 <button type="submit" class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition">
 <i class="fas fa-trash-alt mr-2"></i>Reset All Bookings
 </button>
 </form>
 </div>

 <!-- Clear Activity Logs -->
 <div class="border-2 border-gray-200 bg-gray-50 rounded-xl p-6">
 <h4 class="text-lg font-bold text-gray-800 mb-2">
 <i class="fas fa-history mr-2"></i>Clear Activity Log
 </h4>
 <p class="text-sm text-gray-600 mb-4">All System activity logs will be deleted.</p>
 <form action="{{ route('admin.settings.clear.activity-logs') }}" method="POST" onsubmit="return confirmClear('Activity Logs')">
 @csrf
 <input type="hidden" name="confirm" value="CLEAR">
 <button type="submit" class="w-full bg-gray-600 text-white px-4 py-3 rounded-lg hover:bg-gray-700 transition">
 <i class="fas fa-eraser mr-2"></i>Clear Log
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
// Force correct email value after page load (override browser autofill)
document.addEventListener('DOMContentLoaded', function() {
    var emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.value = '{{ addslashes($resortInfo->email ?? '') }}';
    }
});

function showTab(tabName) {
 document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
 document.querySelectorAll('.tab-btn').forEach(el => {
 el.classList.remove('border-primary-500', 'text-primary-600');
 el.classList.add('border-transparent', 'text-gray-500');
 });
 document.getElementById('content-' + tabName).classList.remove('hidden');
 document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
 document.getElementById('tab-' + tabName).classList.add('border-primary-500', 'text-primary-600');
}
</script>
@endsection

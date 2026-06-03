@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">Hero Slide Edit</h1>
 <p class="text-gray-600 mt-2">Update slide information</p>
 </div>
 <div class="bg-white rounded-xl shadow-lg p-8">
 <form action="{{ route('admin.hero-slides.update', $heroSlide) }}" method="POST" enctype="multipart/form-data">
 @csrf @method('PUT')
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
 <input type="text" name="title" value="{{ old('title', $heroSlide->title) }}" required 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500" 
 placeholder="Welcome to Tufan Resort">
 @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Order *</label>
 <input type="number" name="order" value="{{ old('order', $heroSlide->order) }}" required min="1"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 @error('order') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Subtitle</label>
 <input type="text" name="subtitle" value="{{ old('subtitle', $heroSlide->subtitle) }}" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
 placeholder="Experience Luxury & Tranquility by the Lake">
 @error('subtitle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Button Text</label>
 <input type="text" name="button_text" value="{{ old('button_text', $heroSlide->button_text) }}" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
 placeholder="e.g., Book Now">
 @error('button_text') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Button Link</label>
 <input type="text" name="button_link" value="{{ old('button_link', $heroSlide->button_link) }}" 
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
 placeholder="/rooms">
 @error('button_link') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
 </div>
 <div class="md:col-span-2">
 <label class="block text-sm font-semibold text-gray-700 mb-2">Slide Image</label>
 @if($heroSlide->image)
 <div class="mb-3">
 <img src="{{ asset('storage/' . $heroSlide->image) }}" alt="Current Image" class="h-32 w-auto rounded-lg shadow">
 <p class="text-gray-500 text-sm mt-1">Current image</p>
 </div>
 @endif
 <input type="file" name="image" accept="image/*"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
 <p class="text-gray-500 text-sm mt-1">Leave empty to keep current image. Recommended size: 1920x1080px. Max: 5MB</p>
 @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
 </div>
 <div class="md:col-span-2">
 <label class="flex items-center cursor-pointer">
 <input type="checkbox" name="is_active" value="1" {{ $heroSlide->is_active ? 'checked' : '' }}
 class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
 <span class="ml-2 text-gray-700">Active</span>
 </label>
 </div>
 </div>
 <div class="flex gap-4 mt-8">
 <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
 <i class="fas fa-save mr-2"></i>Update Slide
 </button>
 <a href="{{ route('admin.hero-slides.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-lg hover:bg-gray-600 transition">
 <i class="fas fa-times mr-2"></i>Cancel
 </a>
 </div>
 </form>
 </div>
</div>
@endsection

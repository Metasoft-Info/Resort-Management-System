@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Edit Addon Service</h1>
        <p class="text-gray-600 mt-2">{{ $addonService->name }} - Update service information</p>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.addon-services.update', $addonService) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="service_type" value="convention">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Service Name *</label>
                    <input type="text" name="name" value="{{ old('name', $addonService->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category *</label>
                    <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
                        <option value="">-- Select Category --</option>
                        <option value="decoration" {{ old('category', $addonService->category) == 'decoration' ? 'selected' : '' }}>🎨 Decoration</option>
                        <option value="sound_system" {{ old('category', $addonService->category) == 'sound_system' ? 'selected' : '' }}>🔊 Sound System</option>
                        <option value="photography" {{ old('category', $addonService->category) == 'photography' ? 'selected' : '' }}>📷 Photography</option>
                        <option value="catering" {{ old('category', $addonService->category) == 'catering' ? 'selected' : '' }}>🍽️ Catering</option>
                        <option value="transport" {{ old('category', $addonService->category) == 'transport' ? 'selected' : '' }}>🚗 Transport</option>
                        <option value="lighting" {{ old('category', $addonService->category) == 'lighting' ? 'selected' : '' }}>💡 Lighting</option>
                        <option value="stage" {{ old('category', $addonService->category) == 'stage' ? 'selected' : '' }}>🎭 Stage Setup</option>
                        <option value="other" {{ old('category', $addonService->category) == 'other' ? 'selected' : '' }}>📦 Other</option>
                    </select>
                    @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (৳) *</label>
                    <input type="number" name="price" value="{{ old('price', $addonService->price) }}" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $addonService->unit) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="e.g. per hour, per day">
                    @error('unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $addonService->is_active) ? 'checked' : '' }} class="w-5 h-5 text-violet-600 rounded focus:ring-violet-500">
                        <span class="ml-3 text-sm font-semibold text-gray-700">Active (Show in booking)</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500">{{ old('description', $addonService->description) }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-8 py-3 rounded-xl hover:from-violet-700 hover:to-purple-700 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
                <a href="{{ route('admin.addon-services.index') }}" class="bg-gray-500 text-white px-8 py-3 rounded-xl hover:bg-gray-600 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

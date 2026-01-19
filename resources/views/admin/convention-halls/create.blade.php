@extends('layouts.admin')

@section('title', 'Add Convention Hall')
@section('header', 'Add New Convention Hall')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.convention-halls.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-building mr-2 text-primary-600"></i>Hall Name
                </label>
                <input type="text" name="name" value="{{ old('name') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition @error('name') border-red-500 @enderror" 
                    placeholder="e.g., Grand Ballroom" required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-align-left mr-2 text-primary-600"></i>Description
                </label>
                <textarea name="description" rows="4" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition" 
                    placeholder="Describe the hall features and amenities">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-ruler-combined mr-2 text-primary-600"></i>Dimensions (sq ft)
                </label>
                <input type="number" step="0.01" name="dimensions" value="{{ old('dimensions') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition" 
                    placeholder="5000" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-users mr-2 text-primary-600"></i>Max Capacity
                </label>
                <input type="number" name="max_capacity" value="{{ old('max_capacity') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition" 
                    placeholder="200" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>Price Per Day
                </label>
                <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition" 
                    placeholder="50000" required>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Status
                </label>
                <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="booked" {{ old('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </div>

        <div class="flex gap-4 mt-8 pt-6 border-t">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-semibold shadow-lg inline-flex items-center">
                <i class="fas fa-check mr-2"></i>Create Hall
            </button>
            <a href="{{ route('admin.convention-halls.index') }}" class="px-8 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition font-semibold inline-flex items-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Convention Hall')
@section('header', 'Convention Hall Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
 <div>
 <h3 class="text-lg font-semibold text-gray-700">Total Hall: <span class="text-primary-600">{{ $halls->total() }}</span></h3>
 </div>
 <a href="{{ route('admin.convention-halls.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition font-semibold shadow-lg hover:shadow-xl">
 <i class="fas fa-plus mr-2"></i>Add New Hall
 </a>
</div>

<!-- Card-based Layout with Images -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @forelse($halls as $hall)
 <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
 <!-- Hall Image -->
 <div class="relative h-48 bg-gradient-to-br from-primary-100 to-primary-200">
 @php
 $images = is_array($hall->images) ? $hall->images : (json_decode($hall->images, true) ?? []);
 @endphp
 @if(count($images) > 0)
 <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $hall->name }}" class="w-full h-full object-cover">
 @if(count($images) > 1)
 <span class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded-lg">
 <i class="fas fa-images mr-1"></i>{{ count($images) }}images
 </span>
 @endif
 @else
 <div class="w-full h-full flex items-center justify-center">
 <div class="text-center text-gray-400">
 <i class="fas fa-building text-6xl mb-2"></i>
 <p class="text-sm">No images</p>
 </div>
 </div>
 @endif
 <!-- Status Badge -->
 <span class="absolute top-2 left-2 px-3 py-1 text-xs font-bold rounded-full
 @if($hall->is_available) bg-green-500 text-white
 @else bg-red-500 text-white
 @endif">
 @if($hall->is_available)
 <i class="fas fa-check-circle mr-1"></i>Available
 @else
 <i class="fas fa-tools mr-1"></i>Maintenance
 @endif
 </span>
 </div>
 
 <!-- Hall Info -->
 <div class="p-5">
 <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $hall->name }}</h3>
 
 <div class="space-y-2 mb-4">
 <div class="flex items-center justify-between text-sm">
 <span class="text-gray-500"><i class="fas fa-ruler-combined mr-2 text-primary-500"></i>Dimensions</span>
 <span class="font-semibold text-gray-700">{{ number_format($hall->dimensions) }} sq.ft</span>
 </div>
 <div class="flex items-center justify-between text-sm">
 <span class="text-gray-500"><i class="fas fa-users mr-2 text-primary-500"></i>Capacity</span>
 <span class="font-semibold text-gray-700">{{ $hall->max_capacity }} people</span>
 </div>
 <div class="flex items-center justify-between text-sm">
 <span class="text-gray-500"><i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-500"></i>Price/day</span>
 <span class="font-bold text-primary-600 text-lg">{{ number_format($hall->price_per_day, 0) }}</span>
 </div>
 </div>
 
 <!-- Amenities Preview -->
 @php
 $amenities = is_array($hall->amenities) ? $hall->amenities : (json_decode($hall->amenities, true) ?? []);
 @endphp
 @if(count($amenities) > 0)
 <div class="flex flex-wrap gap-1 mb-4">
 @foreach(array_slice($amenities, 0, 4) as $amenity)
 <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $amenity }}</span>
 @endforeach
 @if(count($amenities) > 4)
 <span class="bg-primary-100 text-primary-600 text-xs px-2 py-1 rounded-full">+{{ count($amenities) - 4 }} more</span>
 @endif
 </div>
 @endif
 
 <!-- Actions -->
 <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
 <a href="{{ route('admin.convention-halls.edit', $hall) }}" class="flex-1 px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm font-semibold text-center inline-flex items-center justify-center">
 <i class="fas fa-edit mr-2"></i>Edit
 </a>
 <form action="{{ route('admin.convention-halls.destroy', $hall) }}" method="POST" class="flex-1" onsubmit="return confirmDelete(this, '{{ $hall->name }} Do you want to delete?')">
 @csrf
 @method('DELETE')
 <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-semibold inline-flex items-center justify-center">
 <i class="fas fa-trash mr-2"></i>Delete
 </button>
 </form>
 </div>
 </div>
 </div>
 @empty
 <div class="col-span-full">
 <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
 <div class="text-gray-400 text-6xl mb-3">
 <i class="fas fa-building"></i>
 </div>
 <p class="text-gray-500 text-lg font-semibold">No Convention Halls found</p>
 <a href="{{ route('admin.convention-halls.create') }}" class="inline-flex items-center mt-4 px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition font-semibold">
 <i class="fas fa-plus mr-2"></i>Add First Hall
 </a>
 </div>
 </div>
 @endforelse
</div>

<div class="mt-6">
 {{ $halls->links() }}
</div>
@endsection

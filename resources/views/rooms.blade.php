@extends('layouts.app')

@section('title', 'Rooms & Suites')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-primary-600 via-accent-600 to-pink-600 text-white py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="container mx-auto px-4 relative z-10">
        <nav class="flex items-center text-sm mb-6">
            <a href="{{ route('home') }}" class="hover:text-yellow-200 transition">
                <i class="fas fa-home mr-2"></i>Home
            </a>
            <i class="fas fa-chevron-right mx-3 text-sm opacity-50"></i>
            <span class="text-yellow-200">Rooms & Suites</span>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">Our Rooms & Suites</h1>
            <p class="text-xl md:text-2xl text-gray-100 font-light">Discover comfort and luxury in every corner</p>
        </div>
    </div>
</div>

<!-- Rooms Grid -->
<section class="py-16 bg-gradient-to-b from-white to-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($rooms as $room)
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <!-- Room Image/Gradient -->
                    <div class="relative h-64 bg-gradient-to-br from-primary-400 via-accent-400 to-pink-400 overflow-hidden">
                        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                        
                        <!-- Room Type Badge -->
                        <div class="absolute top-4 right-4 px-4 py-2 bg-white rounded-full text-sm font-bold text-primary-700 shadow-xl backdrop-blur-sm flex items-center">
                            <i class="fas fa-{{ $room->type == 'suite' ? 'crown' : 'bed' }} mr-2"></i>
                            {{ ucfirst($room->type) }}
                        </div>
                        
                        <!-- Availability Badge -->
                        <div class="absolute bottom-4 left-4 flex items-center space-x-2">
                            @if($room->status == 'available')
                                <span class="px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold flex items-center shadow-lg">
                                    <i class="fas fa-check-circle mr-2"></i>Available Now
                                </span>
                            @else
                                <span class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold flex items-center shadow-lg">
                                    <i class="fas fa-times-circle mr-2"></i>Not Available
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Room Details -->
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-primary-600 transition">{{ $room->name }}</h3>
                        <p class="text-sm text-gray-500 mb-3 flex items-center">
                            <i class="fas fa-door-open mr-2 text-primary-500"></i>
                            Room {{ $room->room_number }}
                        </p>
                        <p class="text-gray-600 mb-4 line-clamp-2 leading-relaxed">{{ $room->description }}</p>
                        
                        <!-- Room Features -->
                        <div class="flex items-center gap-6 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-100">
                            <span class="flex items-center">
                                <i class="fas fa-users mr-2 text-primary-500"></i>
                                <span class="font-semibold">{{ $room->max_guests }}</span> Guests
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-bed mr-2 text-primary-500"></i>
                                <span class="font-semibold">{{ $room->number_of_beds }}</span> Beds
                            </span>
                            @if($room->has_ac)
                                <span class="flex items-center">
                                    <i class="fas fa-snowflake mr-2 text-blue-500"></i>
                                    <span class="font-semibold">AC</span>
                                </span>
                            @endif
                        </div>
                        
                        <!-- Price and Action -->
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                                    ৳{{ number_format($room->price_per_night, 0) }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">/night</span>
                                <div class="text-xs text-gray-400 mt-1">+ VAT & Tax</div>
                            </div>
                            @if($room->status == 'available')
                                <a href="{{ route('login') }}" class="group/btn px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-semibold shadow-md hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                                    Book Now
                                    <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition-transform"></i>
                                </a>
                            @else
                                <button disabled class="px-6 py-3 bg-gray-200 text-gray-400 rounded-xl cursor-not-allowed font-semibold flex items-center">
                                    <i class="fas fa-ban mr-2"></i>
                                    Unavailable
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-12">
            {{ $rooms->links() }}
        </div>
    </div>
</section>

<!-- Info Banner -->
<section class="py-12 bg-gradient-to-r from-primary-600 to-accent-600 text-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <h3 class="text-2xl font-bold mb-2">Need Help Choosing?</h3>
                <p class="text-gray-100">Our team is here to help you find the perfect room</p>
            </div>
            <a href="{{ route('about') }}" class="px-8 py-4 bg-white text-primary-700 rounded-xl hover:bg-gray-100 transition font-bold flex items-center shadow-xl">
                <i class="fas fa-phone-alt mr-3"></i>
                Contact Us
            </a>
        </div>
    </div>
</section>
@endsection

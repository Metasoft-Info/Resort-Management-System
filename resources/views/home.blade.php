@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section with Slides -->
@if(isset($heroSlides) && $heroSlides->count() > 0)
<div class="relative h-[70vh] sm:h-[80vh] overflow-hidden">
 <!-- Slides Container -->
 <div id="heroSlider" class="relative h-full">
 @foreach($heroSlides as $index => $slide)
 <div class="hero-slide absolute inset-0 transition-opacity duration-1000 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}" data-index="{{ $index }}">
 <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}" class="w-full h-full object-cover">
 <div class="absolute inset-0 bg-black bg-opacity-40"></div>
 <div class="absolute inset-0 flex items-center justify-center">
 <div class="text-center text-white px-4 max-w-4xl">
 @if($slide->title)
 <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-4 animate-fade-in">{{ $slide->title }}</h1>
 @endif
 @if($slide->subtitle)
 <p class="text-xl sm:text-2xl mb-6 animate-fade-in">{{ $slide->subtitle }}</p>
 @endif
 @if($slide->button_text && $slide->button_link)
 <a href="{{ $slide->button_link }}" class="inline-block px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-lg hover:bg-gray-100 transition shadow-xl">
 {{ $slide->button_text }}
 </a>
 @endif
 </div>
 </div>
 </div>
 @endforeach
 </div>
 
 <!-- Slide Navigation Dots -->
 @if($heroSlides->count() > 1)
 <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
 @foreach($heroSlides as $index => $slide)
 <button class="slide-dot w-3 h-3 rounded-full transition {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" data-index="{{ $index }}"></button>
 @endforeach
 </div>
 @endif
 
 <!-- Slide Arrows -->
 @if($heroSlides->count() > 1)
 <button id="prevSlide" class="absolute left-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-20">
 <i class="fas fa-chevron-left text-xl"></i>
 </button>
 <button id="nextSlide" class="absolute right-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center text-white transition z-20">
 <i class="fas fa-chevron-right text-xl"></i>
 </button>
 @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
 const slides = document.querySelectorAll('.hero-slide');
 const dots = document.querySelectorAll('.slide-dot');
 let currentSlide = 0;
 const totalSlides = slides.length;
 
 function showSlide(index) {
 slides.forEach((slide, i) => {
 slide.classList.toggle('opacity-100', i === index);
 slide.classList.toggle('opacity-0', i !== index);
 });
 dots.forEach((dot, i) => {
 dot.classList.toggle('bg-white', i === index);
 dot.classList.toggle('bg-white/50', i !== index);
 });
 currentSlide = index;
 }
 
 function nextSlide() {
 showSlide((currentSlide + 1) % totalSlides);
 }
 
 function prevSlide() {
 showSlide((currentSlide - 1 + totalSlides) % totalSlides);
 }
 
 // Auto slide every 5 seconds
 if (totalSlides > 1) {
 setInterval(nextSlide, 5000);
 }
 
 // Navigation buttons
 document.getElementById('prevSlide')?.addEventListener('click', prevSlide);
 document.getElementById('nextSlide')?.addEventListener('click', nextSlide);
 
 // Dot navigation
 dots.forEach(dot => {
 dot.addEventListener('click', () => showSlide(parseInt(dot.dataset.index)));
 });
});
</script>
@else
<!-- Fallback Hero Section -->
<div class="hero-gradient text-white relative overflow-hidden">
 <div class="absolute inset-0 bg-black opacity-20"></div>
 <div class="container mx-auto px-4 py-24 sm:py-32 lg:py-40 relative z-10">
 <div class="max-w-4xl mx-auto text-center">
 <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
 Welcome to <span class="block mt-2 bg-gradient-to-r from-yellow-200 to-pink-200 bg-clip-text text-transparent">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</span>
 </h1>
 <p class="text-xl sm:text-2xl mb-4 text-gray-100 font-light">
 {{ $resortInfo->resort_tagline ?? 'Welcome to Tufan Resort' }}
 </p>
 <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
 <a href="{{ route('rooms') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-700 rounded-xl font-bold text-lg hover:bg-gray-100 transition shadow-xl">
 <i class="fas fa-bed mr-3"></i>View Rooms
 </a>
 <a href="{{ route('convention-hall') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white rounded-xl font-bold text-lg hover:bg-white hover:text-primary-700 transition">
 <i class="fas fa-building mr-3"></i>Explore Venues
 </a>
 </div>
 </div>
 </div>
</div>
@endif

<!-- Features Section -->
<section class="py-12 sm:py-16 -mt-8 relative z-20">
 <div class="container mx-auto px-4">
 <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
 <div class="bg-white rounded-2xl shadow-xl p-6 text-center transform hover:-translate-y-2 transition">
 <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-full flex items-center justify-center mx-auto mb-4">
 <i class="fas fa-hotel text-3xl text-white"></i>
 </div>
 <h3 class="text-xl font-bold mb-2">Luxury Rooms</h3>
 <p class="text-gray-600">Luxurious accommodations with modern amenities</p>
 </div>
 <div class="bg-white rounded-2xl shadow-xl p-6 text-center transform hover:-translate-y-2 transition">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mx-auto mb-4">
 <i class="fas fa-users text-3xl text-white"></i>
 </div>
 <h3 class="text-xl font-bold mb-2">Event Venues</h3>
 <p class="text-gray-600">Perfect for weddings, conferences & celebrations</p>
 </div>
 <div class="bg-white rounded-2xl shadow-xl p-6 text-center transform hover:-translate-y-2 transition">
 <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
 <i class="fas fa-concierge-bell text-3xl text-white"></i>
 </div>
 <h3 class="text-xl font-bold mb-2">24/7 Service</h3>
 <p class="text-gray-600">Dedicated staff for exceptional hospitality</p>
 </div>
 </div>
 </div>
</section>

<!-- Featured Rooms -->
<section class="py-16 sm:py-20">
 <div class="container mx-auto px-4">
 <div class="text-center mb-12">
 <span class="px-4 py-2 bg-primary-50 text-primary-700 rounded-full text-sm font-semibold inline-block mb-4">
 <i class="fas fa-bed mr-2"></i>Accommodations
 </span>
 <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4">Featured Rooms & Suites</h2>
 <p class="text-gray-600 text-lg max-w-2xl mx-auto">
 Choose from our selection of beautifully designed rooms with stunning lake views
 </p>
 </div>
 <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
 @forelse($rooms as $room)
 <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
 <div class="relative h-56 bg-gradient-to-br from-primary-400 via-accent-400 to-pink-400 overflow-hidden">
 @if($room->images && count($room->images) > 0)
 <img src="{{ asset('storage/' . $room->images[0]) }}" alt="{{ $room->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
 @else
 <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>
 <div class="absolute inset-0 flex items-center justify-center">
 <i class="fas fa-bed text-6xl text-white/50"></i>
 </div>
 @endif
 <div class="absolute top-4 right-4 px-3 py-1 bg-white rounded-full text-sm font-bold text-primary-700 shadow-lg">
 {{ ucfirst($room->type) }}
 </div>
 <div class="absolute bottom-4 left-4 flex items-center space-x-2">
 @if($room->status == 'available')
 <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold flex items-center">
 <i class="fas fa-check mr-1"></i>Available
 </span>
 @endif
 </div>
 </div>
 <div class="p-6">
 <h3 class="text-2xl font-bold mb-2 group-hover:text-primary-600 transition">{{ $room->name }}</h3>
 <p class="text-sm text-gray-500 mb-3">
 <i class="fas fa-door-open mr-1 text-primary-500"></i>Room {{ $room->room_number }}
 </p>
 <p class="text-gray-600 mb-4 line-clamp-2">{{ $room->description }}</p>
 <div class="flex items-center gap-4 text-sm text-gray-600 mb-4 pb-4 border-b">
 <span class="flex items-center">
 <i class="fas fa-users mr-2 text-primary-500"></i>{{ $room->max_guests }} Guests
 </span>
 <span class="flex items-center">
 <i class="fas fa-bed mr-2 text-primary-500"></i>{{ $room->number_of_beds }} Beds
 </span>
 </div>
 <div class="flex justify-between items-center">
 <div>
 <span class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
 {{ number_format($room->price_per_night, 0) }}
 </span>
 <span class="text-sm text-gray-500">/night</span>
 </div>
 <a href="{{ route('rooms') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold cursor-default shadow-sm" aria-disabled="true">
 View Details
 </a>
 </div>
 </div>
 </div>
 @empty
 <div class="col-span-3 text-center py-12">
 <div class="text-6xl mb-4 text-gray-300">🏨</div>
 <p class="text-gray-500 text-lg">No rooms available at the moment.</p>
 </div>
 @endforelse
 </div>
 <div class="text-center mt-12">
 <a href="{{ route('rooms') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-bold text-lg shadow-lg hover:shadow-xl">
 View All Rooms
 <i class="fas fa-arrow-right ml-3"></i>
 </a>
 </div>
 </div>
</section>

<!-- Convention Hall CTA -->
<section class="py-16 sm:py-20 bg-gradient-to-br from-primary-50 via-accent-50 to-pink-50 relative overflow-hidden">
 <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\' fill=\'%239333ea\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
 <div class="container mx-auto px-4 relative z-10">
 <div class="max-w-4xl mx-auto text-center">
 <div class="inline-block mb-6">
 <span class="px-4 py-2 bg-white rounded-full text-sm font-semibold text-primary-700 shadow-md">
 <i class="fas fa-building mr-2"></i>Event Venue
 </span>
 </div>
 <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">Convention Hall & Event Spaces</h2>
 <p class="text-xl text-gray-700 mb-8 leading-relaxed">
 Perfect venue for weddings, conferences, and corporate events. State-of-the-art facilities with professional service to make your event unforgettable.
 </p>
 <div class="grid sm:grid-cols-3 gap-6 mb-10 max-w-3xl mx-auto">
 <div class="bg-white rounded-xl p-6 shadow-md">
 <div class="text-3xl mb-2">👥</div>
 <div class="text-2xl font-bold text-primary-600 mb-1">200+</div>
 <div class="text-sm text-gray-600">Capacity</div>
 </div>
 <div class="bg-white rounded-xl p-6 shadow-md">
 <div class="text-3xl mb-2">🎯</div>
 <div class="text-2xl font-bold text-primary-600 mb-1">5000</div>
 <div class="text-sm text-gray-600">Sq Ft Space</div>
 </div>
 <div class="bg-white rounded-xl p-6 shadow-md">
 <div class="text-3xl mb-2">⭐</div>
 <div class="text-2xl font-bold text-primary-600 mb-1">5 Star</div>
 <div class="text-sm text-gray-600">Facilities</div>
 </div>
 </div>
 <a href="{{ route('convention-hall') }}" class="inline-flex items-center px-10 py-5 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
 <i class="fas fa-calendar-check mr-3"></i>
 Explore Convention Hall
 <i class="fas fa-arrow-right ml-3"></i>
 </a>
 </div>
 </div>
</section>

<!-- About Section -->
<section class="py-16 sm:py-20">
 <div class="container mx-auto px-4">
 <div class="max-w-5xl mx-auto">
 <div class="text-center mb-12">
 <span class="px-4 py-2 bg-primary-50 text-primary-700 rounded-full text-sm font-semibold inline-block mb-4">
 <i class="fas fa-info-circle mr-2"></i>About Us
 </span>
 <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4">About Tufan Resort</h2>
 </div>
 <div class="bg-white rounded-3xl shadow-xl p-8 sm:p-12">
 <p class="text-gray-700 text-lg leading-relaxed mb-6">
 Welcome to Tufan Resort, where luxury meets nature. Experience world-class hospitality in a serene environment.
 </p>
 <div class="grid sm:grid-cols-2 gap-4">
 <div class="flex items-start">
 <i class="fas fa-check-circle text-2xl text-green-500 mr-3 mt-1"></i>
 <div>
 <h4 class="font-bold mb-1">Excellent Location</h4>
 <p class="text-gray-600 text-sm">Beautiful lake view setting</p>
 </div>
 </div>
 <div class="flex items-start">
 <i class="fas fa-check-circle text-2xl text-green-500 mr-3 mt-1"></i>
 <div>
 <h4 class="font-bold mb-1">Modern Amenities</h4>
 <p class="text-gray-600 text-sm">All comfort facilities included</p>
 </div>
 </div>
 </div>
 <div class="mt-8 text-center">
 <a href="{{ route('about') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-semibold">
 Learn More About Us
 <i class="fas fa-arrow-right ml-2"></i>
 </a>
 </div>
 </div>
 </div>
 </div>
</section>
@endsection

@extends('layouts.app')

@section('title', 'About Us')

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
 <span class="text-yellow-200">About Us</span>
 </nav>
 <div class="max-w-3xl">
 <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">About Tufan Resort</h1>
 <p class="text-xl md:text-2xl text-gray-100 font-light">Your home away from home, by the lake</p>
 </div>
 </div>
</div>

@if($resortInfo)
<section class="py-16 bg-gradient-to-b from-white to-gray-50">
 <div class="container mx-auto px-4">
 <div class="max-w-5xl mx-auto">
 <!-- Our Story -->
 <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12 transform hover:-translate-y-1 transition">
 <div class="flex items-center mb-6">
 <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center mr-4">
 <i class="fas fa-book-open text-3xl text-white"></i>
 </div>
 <h2 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">Our Story</h2>
 </div>
 <p class="text-gray-700 text-lg leading-relaxed">
 {{ $resortInfo->about_text }}
 </p>
 </div>
 
 <!-- Our Mission -->
 <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12 transform hover:-translate-y-1 transition">
 <div class="flex items-center mb-6">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mr-4">
 <i class="fas fa-bullseye text-3xl text-white"></i>
 </div>
 <h2 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Our Mission</h2>
 </div>
 <p class="text-gray-700 text-lg leading-relaxed">
 {{ $resortInfo->mission_text }}
 </p>
 </div>
 
 <!-- Facilities -->
 <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12 transform hover:-translate-y-1 transition">
 <div class="flex items-center mb-8">
 <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mr-4">
 <i class="fas fa-concierge-bell text-3xl text-white"></i>
 </div>
 <h2 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">Facilities</h2>
 </div>
 <div class="grid md:grid-cols-2 gap-4">
 @foreach($resortInfo->facilities as $facility)
 <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:shadow-md transition">
 <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
 <i class="fas fa-check text-green-600 text-lg"></i>
 </div>
 <span class="text-gray-700 font-medium">{{ $facility }}</span>
 </div>
 @endforeach
 </div>
 </div>
 
 <!-- Contact Us -->
 <div class="bg-gradient-to-br from-primary-600 via-accent-600 to-pink-600 rounded-3xl shadow-2xl p-8 md:p-12 text-white">
 <div class="flex items-center mb-8">
 <div class="w-16 h-16 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
 <i class="fas fa-envelope text-3xl text-white"></i>
 </div>
 <h2 class="text-3xl md:text-4xl font-bold">Contact Us</h2>
 </div>
 <div class="grid md:grid-cols-2 gap-6">
 <div class="flex items-start gap-4 p-4 bg-white bg-opacity-10 backdrop-blur-sm rounded-xl">
 <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0">
 <i class="fas fa-map-marker-alt text-xl"></i>
 </div>
 <div>
 <h4 class="font-bold mb-1">Address</h4>
 <p class="text-gray-100">{{ $resortInfo->address }}</p>
 </div>
 </div>
 <div class="flex items-start gap-4 p-4 bg-white bg-opacity-10 backdrop-blur-sm rounded-xl">
 <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0">
 <i class="fas fa-phone text-xl"></i>
 </div>
 <div>
 <h4 class="font-bold mb-1">Phone</h4>
 <p class="text-gray-100">{{ $resortInfo->phone }}</p>
 </div>
 </div>
 <div class="flex items-start gap-4 p-4 bg-white bg-opacity-10 backdrop-blur-sm rounded-xl md:col-span-2">
 <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center flex-shrink-0">
 <i class="fas fa-envelope text-xl"></i>
 </div>
 <div>
 <h4 class="font-bold mb-1">Email</h4>
 <p class="text-gray-100">{{ $resortInfo->email }}</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
</section>
@endif
@endsection

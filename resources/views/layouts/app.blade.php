<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>@yield('title', 'Home') - {{ $resortInfo->resort_name ?? 'Tufan Resort' }}</title>
 @if($resortInfo && $resortInfo->favicon)
 <link rel="icon" type="image/jpeg" href="{{ asset('storage/' . $resortInfo->favicon) }}">
 <link rel="shortcut icon" type="image/jpeg" href="{{ asset('storage/' . $resortInfo->favicon) }}">
 @else
 <link rel="icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
 <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
 @endif
 <script src="https://cdn.tailwindcss.com"></script>
 <script>
 tailwind.config = {
 theme: {
 extend: {
 colors: {
 primary: {
 DEFAULT: '#8b5cf6',
 50: '#faf5ff',
 100: '#f3e8ff',
 200: '#e9d5ff',
 300: '#d8b4fe',
 400: '#c084fc',
 500: '#a855f7',
 600: '#9333ea',
 700: '#7e22ce',
 800: '#6b21a8',
 900: '#581c87',
 },
 accent: {
 DEFAULT: '#ec4899',
 50: '#fdf2f8',
 100: '#fce7f3',
 200: '#fbcfe8',
 300: '#f9a8d4',
 400: '#f472b6',
 500: '#ec4899',
 600: '#db2777',
 700: '#be185d',
 800: '#9f1239',
 900: '#831843',
 },
 },
 fontFamily: {
 heading: ['Inter', 'system-ui', 'sans-serif'],
 },
 }
 }
 }
 </script>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 <style>
 @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
 body {
 font-family: 'Inter', sans-serif;
 }
 .hero-gradient {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 }
 .glass-effect {
 background: rgba(255, 255, 255, 0.95);
 backdrop-filter: blur(10px);
 }
 </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100">
 <!-- Navbar -->
 <nav class="glass-effect shadow-lg fixed w-full z-50 border-b border-gray-200">
 <div class="container mx-auto px-4 sm:px-6 lg:px-8">
 <div class="flex justify-between items-center py-3 sm:py-4">
 <div class="flex items-center space-x-3">
 @if($resortInfo->header_logo ?? null)
 <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Logo' }}" class="h-10 sm:h-12 w-auto">
 @else
 <div class="text-3xl sm:text-4xl">🏞️</div>
 @endif
 <div>
 <h1 class="text-lg sm:text-2xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
 {{ $resortInfo->resort_name ?? 'Tufan Resort' }}
 </h1>
 <p class="text-xs text-gray-500 hidden sm:block">{{ $resortInfo->resort_tagline ?? 'Tufan Resort Welcome' }}</p>
 </div>
 </div>
 <div class="hidden md:flex items-center space-x-1">
 <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-home mr-2"></i>Home
 </a>
 <a href="{{ route('rooms') }}" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium {{ request()->routeIs('rooms') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-bed mr-2"></i>Rooms
 </a>
 <a href="{{ route('convention-hall') }}" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium {{ request()->routeIs('convention-hall') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-building mr-2"></i>Convention Hall
 </a>
 <a href="{{ route('about') }}" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium {{ request()->routeIs('about') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-info-circle mr-2"></i>About
 </a>
 </div>
 <!-- Mobile Menu Button -->
 <button id="mobileMenuBtn" class="md:hidden text-gray-700 hover:text-primary-600 transition p-2">
 <i id="menuIcon" class="fas fa-bars text-2xl"></i>
 </button>
 </div>
 
 <!-- Mobile Menu Dropdown -->
 <div id="mobileMenu" class="hidden md:hidden pb-4 border-t border-gray-200 mt-2">
 <div class="flex flex-col space-y-1 pt-3">
 <a href="{{ route('home') }}" class="px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium flex items-center {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-home mr-3 w-5"></i>Home
 </a>
 <a href="{{ route('rooms') }}" class="px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium flex items-center {{ request()->routeIs('rooms') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-bed mr-3 w-5"></i>Rooms
 </a>
 <a href="{{ route('convention-hall') }}" class="px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium flex items-center {{ request()->routeIs('convention-hall') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-building mr-3 w-5"></i>Convention Hall
 </a>
 <a href="{{ route('about') }}" class="px-4 py-3 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition font-medium flex items-center {{ request()->routeIs('about') ? 'bg-primary-50 text-primary-700' : '' }}">
 <i class="fas fa-info-circle mr-3 w-5"></i>About
 </a>
 </div>
 </div>
 </div>
 </nav>
 
 <script>
 // Mobile Menu Toggle
 document.getElementById('mobileMenuBtn').addEventListener('click', function() {
 const mobileMenu = document.getElementById('mobileMenu');
 const menuIcon = document.getElementById('menuIcon');
 
 mobileMenu.classList.toggle('hidden');
 
 if (mobileMenu.classList.contains('hidden')) {
 menuIcon.classList.remove('fa-times');
 menuIcon.classList.add('fa-bars');
 } else {
 menuIcon.classList.remove('fa-bars');
 menuIcon.classList.add('fa-times');
 }
 });
 </script>

 <!-- Main Content -->
 <main class="pt-20">
 @yield('content')
 </main>

 <!-- Footer -->
 <footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white mt-20">
 <div class="container mx-auto px-4 py-12 sm:py-16">
 <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
 <div>
 <div class="flex items-center space-x-3 mb-4">
 @if($resortInfo->footer_logo ?? null)
 <img src="{{ asset('storage/' . $resortInfo->footer_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Logo' }}" class="h-12 w-auto">
 @else
 <div class="text-4xl">🏞️</div>
 @endif
 <h3 class="text-2xl font-bold">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</h3>
 </div>
 <p class="text-gray-300 leading-relaxed mb-4">
 {{ $resortInfo->footer_description ?? 'Luxury accommodation and event hosting services. Experience comfort and tranquility by the lake.' }}
 </p>
 <div class="flex space-x-3">
 @if($resortInfo && isset($resortInfo->social_links['facebook']))
 <a href="{{ $resortInfo->social_links['facebook'] }}" target="_blank" class="w-10 h-10 bg-primary-600 hover:bg-primary-700 rounded-full flex items-center justify-center transition">
 <i class="fab fa-facebook-f"></i>
 </a>
 @endif
 @if($resortInfo && isset($resortInfo->social_links['instagram']))
 <a href="{{ $resortInfo->social_links['instagram'] }}" target="_blank" class="w-10 h-10 bg-accent-600 hover:bg-accent-700 rounded-full flex items-center justify-center transition">
 <i class="fab fa-instagram"></i>
 </a>
 @endif
 @if($resortInfo && isset($resortInfo->social_links['twitter']))
 <a href="{{ $resortInfo->social_links['twitter'] }}" target="_blank" class="w-10 h-10 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center transition">
 <i class="fab fa-twitter"></i>
 </a>
 @endif
 </div>
 </div>
 <div>
 <h3 class="text-xl font-bold mb-4 flex items-center">
 <i class="fas fa-link mr-2 text-primary-400"></i>Quick Links
 </h3>
 <ul class="space-y-3">
 <li><a href="{{ route('rooms') }}" class="text-gray-300 hover:text-white hover:translate-x-1 inline-block transition">
 <i class="fas fa-chevron-right mr-2 text-xs text-primary-400"></i>Rooms & Suites
 </a></li>
 <li><a href="{{ route('convention-hall') }}" class="text-gray-300 hover:text-white hover:translate-x-1 inline-block transition">
 <i class="fas fa-chevron-right mr-2 text-xs text-primary-400"></i>Convention Hall
 </a></li>
 <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-white hover:translate-x-1 inline-block transition">
 <i class="fas fa-chevron-right mr-2 text-xs text-primary-400"></i>About Us
 </a></li>
 </ul>
 </div>
 <div>
 <h3 class="text-xl font-bold mb-4 flex items-center">
 <i class="fas fa-phone mr-2 text-primary-400"></i>Contact
 </h3>
 <ul class="space-y-3 text-gray-300">
 @if($resortInfo && $resortInfo->email)
 <li class="flex items-start">
 <i class="fas fa-envelope mt-1 mr-3 text-primary-400"></i>
 <span>{{ $resortInfo->email }}</span>
 </li>
 @endif
 @if($resortInfo && $resortInfo->phone)
 <li class="flex items-start">
 <i class="fas fa-phone-alt mt-1 mr-3 text-primary-400"></i>
 <span>{{ $resortInfo->phone }}</span>
 </li>
 @endif
 @if($resortInfo && $resortInfo->address)
 <li class="flex items-start">
 <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary-400"></i>
 <span>{!! nl2br(e($resortInfo->address)) !!}</span>
 </li>
 @endif
 </ul>
 </div>
 </div>
 <div class="border-t border-gray-700 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center">
 <p class="text-gray-400 text-sm mb-4 sm:mb-0">
 {{ $resortInfo->copyright_text ?? '&copy; ' . date('Y') . ' Tufan Resort. All rights reserved.' }}
 </p>
 <p class="text-gray-500 text-sm">
 Developed with <i class="fas fa-heart text-red-500"></i> by Mir Javed Jeetu
 </p>
 </div>
 </div>
 </footer>

 <!-- Global Modal for Success/Error Messages -->
 <div id="globalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999] transition-opacity duration-300">
 <div id="modalContent" class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0">
 <!-- Success Modal Content -->
 <div id="successModal" class="hidden text-center">
 <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
 <i class="fas fa-check-circle text-green-500 text-5xl"></i>
 </div>
 <h3 class="text-2xl font-bold text-gray-800 mb-3">Success!</h3>
 <p id="successMessage" class="text-gray-600 mb-6 text-lg"></p>
 <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition shadow-lg">
 <i class="fas fa-thumbs-up mr-2"></i>OK
 </button>
 </div>
 
 <!-- Error Modal Content -->
 <div id="errorModal" class="hidden text-center">
 <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
 <i class="fas fa-times-circle text-red-500 text-5xl"></i>
 </div>
 <h3 class="text-2xl font-bold text-gray-800 mb-3">Error!</h3>
 <p id="errorMessage" class="text-gray-600 mb-6 text-lg"></p>
 <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg">
 <i class="fas fa-times mr-2"></i>Close
 </button>
 </div>
 
 <!-- Info Modal Content -->
 <div id="infoModal" class="hidden text-center">
 <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
 <i class="fas fa-info-circle text-blue-500 text-5xl"></i>
 </div>
 <h3 class="text-2xl font-bold text-gray-800 mb-3">Information</h3>
 <p id="infoMessage" class="text-gray-600 mb-6 text-lg"></p>
 <button onclick="closeGlobalModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg">
 <i class="fas fa-check mr-2"></i>OK
 </button>
 </div>
 </div>
 </div>

 <script>
 function showGlobalModal(type, message) {
 const modal = document.getElementById('globalModal');
 const content = document.getElementById('modalContent');
 
 document.getElementById('successModal').classList.add('hidden');
 document.getElementById('errorModal').classList.add('hidden');
 document.getElementById('infoModal').classList.add('hidden');
 
 if (type === 'success') {
 document.getElementById('successModal').classList.remove('hidden');
 document.getElementById('successMessage').textContent = message;
 } else if (type === 'error') {
 document.getElementById('errorModal').classList.remove('hidden');
 document.getElementById('errorMessage').textContent = message;
 } else if (type === 'info') {
 document.getElementById('infoModal').classList.remove('hidden');
 document.getElementById('infoMessage').textContent = message;
 }
 
 modal.classList.remove('hidden');
 modal.classList.add('flex');
 setTimeout(() => {
 content.classList.remove('scale-95', 'opacity-0');
 content.classList.add('scale-100', 'opacity-100');
 }, 10);
 }

 function closeGlobalModal() {
 const modal = document.getElementById('globalModal');
 const content = document.getElementById('modalContent');
 
 content.classList.remove('scale-100', 'opacity-100');
 content.classList.add('scale-95', 'opacity-0');
 
 setTimeout(() => {
 modal.classList.remove('flex');
 modal.classList.add('hidden');
 }, 300);
 }

 document.getElementById('globalModal')?.addEventListener('click', function(e) {
 if (e.target === this) closeGlobalModal();
 });

 document.addEventListener('keydown', function(e) {
 if (e.key === 'Escape') closeGlobalModal();
 });

 document.addEventListener('DOMContentLoaded', function() {
 @if(session('success'))
 showGlobalModal('success', @json(session('success')));
 @endif
 @if(session('error'))
 showGlobalModal('error', @json(session('error')));
 @endif
 });
 </script>
</body>
</html>

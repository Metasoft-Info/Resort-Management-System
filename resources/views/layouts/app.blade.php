<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tufan Resort') - Lake View Resort & Convention Center</title>
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
                    <div class="text-3xl sm:text-4xl">🏞️</div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">
                            {{ $resortInfo->resort_name ?? 'Tufan Resort' }}
                        </h1>
                        <p class="text-xs text-gray-500 hidden sm:block">{{ $resortInfo->resort_tagline ?? 'তুফান রিসোর্ট' }}</p>
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
                <button class="md:hidden text-gray-700 hover:text-primary-600 transition">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

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
                        <div class="text-4xl">🏞️</div>
                        <h3 class="text-2xl font-bold">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-4">
                        {{ $resortInfo->footer_description ?? 'Premium accommodation and event hosting services. Experience luxury and tranquility by the lake.' }}
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
                        <li><a href="{{ route('login') }}" class="text-gray-300 hover:text-white hover:translate-x-1 inline-block transition">
                            <i class="fas fa-chevron-right mr-2 text-xs text-primary-400"></i>Admin Panel
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
</body>
</html>

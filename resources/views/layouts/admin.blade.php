<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Tufan Resort Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
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
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-primary-900 via-primary-800 to-primary-900 text-white shadow-2xl flex-shrink-0 fixed h-screen z-30 flex flex-col">
            <div class="p-6 border-b border-primary-700">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-hotel text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-yellow-200 to-pink-200 bg-clip-text text-transparent">Tufan Resort</h1>
                        <p class="text-xs text-primary-300">Admin Dashboard</p>
                    </div>
                </div>
            </div>
            <nav class="mt-6 px-3 flex-1 overflow-y-auto pb-8" style="max-height: calc(100vh - 120px);">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.dashboard')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.todays-summary') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.todays-summary')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-calendar-day w-5 mr-3"></i>
                    <span class="font-semibold">Today's Summary</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">Rooms Management</div>
                <a href="{{ route('admin.rooms.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.rooms.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-bed w-5 mr-3"></i>
                    <span class="font-semibold">Rooms</span>
                </a>
                <a href="{{ route('admin.room-types.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.room-types.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-door-open w-5 mr-3"></i>
                    <span class="font-semibold">Room Types</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">Room Bookings</div>
                <a href="{{ route('admin.premium-booking.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.premium-booking.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-search-plus w-5 mr-3"></i>
                    <span class="font-semibold">Search & Book</span>
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.bookings.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-list w-5 mr-3"></i>
                    <span class="font-semibold">All Bookings</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">Convention Halls</div>
                <a href="{{ route('admin.premium-convention.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.premium-convention.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-search-plus w-5 mr-3"></i>
                    <span class="font-semibold">Search & Book Hall</span>
                </a>
                <a href="{{ route('admin.convention-bookings.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.convention-bookings.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-list w-5 mr-3"></i>
                    <span class="font-semibold">All Hall Bookings</span>
                </a>
                <a href="{{ route('admin.convention-halls.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.convention-halls.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-building w-5 mr-3"></i>
                    <span class="font-semibold">Manage Halls</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">Services</div>
                <a href="{{ route('admin.addon-services.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.addon-services.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-plus-circle w-5 mr-3"></i>
                    <span class="font-semibold">Addon Services</span>
                </a>
                <a href="{{ route('admin.food-packages.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.food-packages.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-utensils w-5 mr-3"></i>
                    <span class="font-semibold">Food Packages</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">Website</div>
                <a href="{{ route('admin.hero-slides.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.hero-slides.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-images w-5 mr-3"></i>
                    <span class="font-semibold">Hero Slides</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">Reports</div>
                <a href="{{ route('admin.reports.room-bookings') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.reports.room-bookings')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-file-alt w-5 mr-3"></i>
                    <span class="font-semibold">Room Bookings Report</span>
                </a>
                <a href="{{ route('admin.reports.convention-bookings') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.reports.convention-bookings')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    <span class="font-semibold">Convention Report</span>
                </a>
                
                <div class="text-xs text-primary-300 px-4 py-2 mt-4 font-semibold uppercase tracking-wider">System</div>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.users.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span class="font-semibold">Users</span>
                </a>
                <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.activity-logs.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-history w-5 mr-3"></i>
                    <span class="font-semibold">Activity Logs</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 mb-2 rounded-xl transition @if(request()->routeIs('admin.settings.*')) bg-primary-700 shadow-lg @else hover:bg-primary-700/50 @endif">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    <span class="font-semibold">Settings</span>
                </a>
                
                <div class="border-t border-primary-700 my-4"></div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 rounded-xl hover:bg-red-500/20 text-red-200 hover:text-red-100 transition">
                        <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                        <span class="font-semibold">Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64 overflow-hidden">
            <header class="bg-white shadow-md z-20">
                <div class="px-8 py-5 flex items-center justify-between">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent">@yield('header', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600"><i class="fas fa-user-circle mr-2"></i>{{ auth()->user()->name ?? 'Admin' }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-8">
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg mb-6 flex items-center shadow-sm">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <span class="font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg mb-6 flex items-center shadow-sm">
                            <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                            <span class="font-semibold">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>

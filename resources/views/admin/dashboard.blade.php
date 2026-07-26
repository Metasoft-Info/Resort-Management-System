@extends('layouts.admin')
@section('content')
<div class="space-y-6">
 <!-- Welcome Header with Mode Toggle -->
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
 <div>
 <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
 <p class="text-gray-500 mt-1">{{ date('l, F j, Y') }}</p>
 </div>
 
 <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
 @if($hasResortAccess && $hasConventionAccess)
 <!-- Dashboard Mode Toggle -->
 <div class="flex items-center bg-slate-100 p-1 rounded-xl shadow-inner">
 <button type="button" id="modeResort" onclick="switchDashboardMode('resort')" 
 class="mode-btn flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 {{ $currentMode == 'resort' ? 'bg-white shadow-lg text-emerald-600' : 'text-slate-500 hover:text-slate-700' }}">
 <i class="fas fa-hotel mr-2"></i>Resort
 </button>
 <button type="button" id="modeConvention" onclick="switchDashboardMode('convention')"
 class="mode-btn flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 {{ $currentMode == 'convention' ? 'bg-white shadow-lg text-violet-600' : 'text-slate-500 hover:text-slate-700' }}">
 <i class="fas fa-building-columns mr-2"></i>Convention
 </button>
 </div>
 @endif
 
 <div class="flex gap-2">
 @if($hasResortAccess)
 <a href="{{ route('admin.premium-booking.index') }}" class="resort-link inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white rounded-lg hover:from-emerald-700 hover:to-emerald-600 transition text-sm font-medium shadow-lg shadow-emerald-200 {{ $currentMode == 'convention' ? 'hidden' : '' }}">
 <i class="fas fa-plus mr-2"></i>Room Booking
 </a>
 @endif
 @if($hasConventionAccess)
 <a href="{{ route('admin.premium-convention.index') }}" class="convention-link inline-flex items-center px-4 py-2 bg-gradient-to-r from-violet-600 to-violet-500 text-white rounded-lg hover:from-violet-700 hover:to-violet-600 transition text-sm font-medium shadow-lg shadow-violet-200 {{ $currentMode == 'resort' ? 'hidden' : '' }}">
 <i class="fas fa-plus mr-2"></i>Hall Booking
 </a>
 @endif
 <a href="{{ route('admin.todays-summary') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium shadow-sm">
 <i class="fas fa-chart-bar mr-2"></i>Today
 </a>
 </div>
 </div>
 </div>

 <!-- ============ RESORT DASHBOARD ============ -->
 <div id="resortDashboard" class="{{ $currentMode == 'convention' ? 'hidden' : '' }}">
 <!-- Resort Stats Cards -->
 <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
 <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 shadow-lg shadow-emerald-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-emerald-100 uppercase tracking-wide">Total Bookings</p>
 <p class="text-3xl font-bold text-white mt-2">{{ $resortStats['total_bookings'] }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-check text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-emerald-100"><i class="fas fa-hotel mr-1"></i>All room bookings</div>
 </div>

 <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-5 shadow-lg shadow-teal-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-teal-100 uppercase tracking-wide">Active Guests</p>
 <p class="text-3xl font-bold text-white mt-2">{{ $resortStats['active_bookings'] }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-users text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-teal-100"><i class="fas fa-check-circle mr-1"></i>Currently staying</div>
 </div>

 <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-5 shadow-lg shadow-cyan-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-cyan-100 uppercase tracking-wide">Available Rooms</p>
 <p class="text-3xl font-bold text-white mt-2">{{ $resortStats['available_rooms'] }}<span class="text-lg text-cyan-200">/{{ $resortStats['total_rooms'] }}</span></p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-bed text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-cyan-100"><i class="fas fa-door-open mr-1"></i>Ready to book</div>
 </div>

 <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 shadow-lg shadow-green-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-green-100 uppercase tracking-wide">Room Revenue</p>
 <p class="text-2xl font-bold text-white mt-2">{{ number_format($resortStats['room_revenue']) }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-bangladeshi-taka-sign text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-green-100"><i class="fas fa-chart-line mr-1"></i>All time revenue</div>
 </div>
 </div>

 <!-- Today's Resort Activity -->
 <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-sign-in-alt text-green-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $resortStats['today_checkins'] }}</p>
 <p class="text-xs text-gray-500">Today Check-in</p>
 </div>
 </div>
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-sign-out-alt text-orange-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $resortStats['today_checkouts'] }}</p>
 <p class="text-xs text-gray-500">Today Check-out</p>
 </div>
 </div>
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-clock text-yellow-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $resortStats['pending_bookings'] }}</p>
 <p class="text-xs text-gray-500">Pending</p>
 </div>
 </div>
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-percentage text-emerald-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $resortStats['total_rooms'] > 0 ? round(($resortStats['total_rooms'] - $resortStats['available_rooms']) / $resortStats['total_rooms'] * 100) : 0 }}%</p>
 <p class="text-xs text-gray-500">Occupancy</p>
 </div>
 </div>
 </div>
 </div>
 <!-- End Resort Dashboard -->

 <!-- ============ CONVENTION DASHBOARD ============ -->
 <div id="conventionDashboard" class="{{ $currentMode == 'resort' ? 'hidden' : '' }}">
 <!-- Convention Stats Cards -->
 <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
 <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl p-5 shadow-lg shadow-violet-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-violet-100 uppercase tracking-wide">Total Bookings</p>
 <p class="text-3xl font-bold text-white mt-2">{{ $conventionStats['total_bookings'] }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-check text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-violet-100"><i class="fas fa-building-columns mr-1"></i>All hall bookings</div>
 </div>

 <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 shadow-lg shadow-purple-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-purple-100 uppercase tracking-wide">Today's Events</p>
 <p class="text-3xl font-bold text-white mt-2">{{ $conventionStats['today_events'] }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-day text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-purple-100"><i class="fas fa-star mr-1"></i>Events happening today</div>
 </div>

 <div class="bg-gradient-to-br from-fuchsia-500 to-fuchsia-600 rounded-xl p-5 shadow-lg shadow-fuchsia-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-fuchsia-100 uppercase tracking-wide">Upcoming Events</p>
 <p class="text-3xl font-bold text-white mt-2">{{ $conventionStats['upcoming_events'] }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-alt text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-fuchsia-100"><i class="fas fa-arrow-right mr-1"></i>Future bookings</div>
 </div>

 <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-5 shadow-lg shadow-pink-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-pink-100 uppercase tracking-wide">Hall Revenue</p>
 <p class="text-2xl font-bold text-white mt-2">{{ number_format($conventionStats['convention_revenue']) }}</p>
 </div>
 <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-bangladeshi-taka-sign text-white text-xl"></i>
 </div>
 </div>
 <div class="mt-3 text-xs text-pink-100"><i class="fas fa-chart-line mr-1"></i>All time revenue</div>
 </div>
 </div>

 <!-- Convention Quick Stats -->
 <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-building text-violet-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $conventionStats['total_halls'] }}</p>
 <p class="text-xs text-gray-500">Total Halls</p>
 </div>
 </div>
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-check-circle text-purple-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $conventionStats['active_bookings'] }}</p>
 <p class="text-xs text-gray-500">Active Bookings</p>
 </div>
 </div>
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-clock text-yellow-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $conventionStats['pending_bookings'] }}</p>
 <p class="text-xs text-gray-500">Pending</p>
 </div>
 </div>
 <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center">
 <div class="w-12 h-12 bg-fuchsia-100 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-calendar-week text-fuchsia-600 text-xl"></i>
 </div>
 <div>
 <p class="text-2xl font-bold text-gray-900">{{ $conventionStats['today_events'] }}</p>
 <p class="text-xs text-gray-500">Today Events</p>
 </div>
 </div>
 </div>

 <!-- Recent Convention Bookings -->
 <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
 <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-violet-50 to-purple-50">
 <h3 class="font-semibold text-gray-900"><i class="fas fa-building-columns mr-2 text-violet-500"></i>Recent Hall Bookings</h3>
 <a href="{{ route('admin.convention-bookings.index') }}" class="text-sm text-violet-600 hover:text-violet-800 font-medium">View all →</a>
 </div>
 <div class="divide-y divide-gray-100">
 @forelse($recentConventionBookings->take(5) as $booking)
 <div class="p-4 hover:bg-violet-50/50 transition cursor-pointer" onclick="window.location='{{ route('admin.convention-bookings.show', $booking) }}'">
 <div class="flex items-center justify-between mb-2">
 <span class="text-sm font-bold text-violet-600">#{{ $booking->id }}</span>
 @if($booking->status == 'confirmed')
 <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Confirmed</span>
 @elseif($booking->status == 'pending')
 <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
 @else
 <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($booking->status) }}</span>
 @endif
 </div>
 <div class="text-sm font-medium text-gray-900">{{ $booking->customer_name }}</div>
 <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
 <div>
 <i class="fas fa-building mr-1"></i>{{ $booking->conventionHall->name ?? 'N/A' }}
 <span class="mx-2">•</span>
 <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}
 </div>
 <div class="text-sm font-bold text-violet-600">{{ number_format($booking->total_amount) }}</div>
 </div>
 </div>
 @empty
 <div class="p-8 text-center text-gray-500">No convention bookings found</div>
 @endforelse
 </div>
 </div>

 <!-- Hall Availability Status - Premium Grid -->
 <div class="bg-gradient-to-br from-violet-900 via-purple-900 to-indigo-900 rounded-2xl overflow-hidden shadow-2xl">
 <div class="px-4 sm:px-6 py-4 border-b border-violet-700/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
 <i class="fas fa-building-columns text-white"></i>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg">Hall Availability</h3>
 <p class="text-violet-300 text-xs">Next 7 days slots • Click to book</p>
 </div>
 </div>
 <div class="flex items-center gap-4 text-xs flex-wrap">
 <div class="flex items-center gap-2">
 <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
 <span class="text-violet-200">Morning/Night available</span>
 </div>
 <div class="flex items-center gap-2">
 <span class="w-3 h-3 rounded-full bg-amber-500"></span>
 <span class="text-violet-200">Full day available</span>
 </div>
 <div class="flex items-center gap-2">
 <span class="w-3 h-3 rounded-full bg-gray-500"></span>
 <span class="text-violet-200">Full day not available</span>
 </div>
 <div class="flex items-center gap-2">
 <span class="w-3 h-3 rounded-full bg-rose-500"></span>
 <span class="text-violet-200">Booked</span>
 </div>
 </div>
 </div>
 <div class="p-4 sm:p-6">
 <!-- Halls Table -->
 @if(count($hallsWithStatus) > 0)
 <div class="overflow-x-auto">
 <table class="w-full">
 <thead>
 <tr>
 <th class="text-left text-violet-300 text-xs font-semibold pb-3 sticky left-0 bg-gradient-to-r from-violet-900 to-transparent">Hall</th>
 @foreach($hallsWithStatus[0]['days'] as $day)
 <th class="text-center px-2 pb-3">
 <div class="text-violet-400 text-xs font-medium">{{ $day['day_name'] }}</div>
 <div class="text-white text-sm font-bold">{{ $day['day_num'] }}</div>
 </th>
 @endforeach
 </tr>
 </thead>
 <tbody>
 @foreach($hallsWithStatus as $hallData)
 <tr class="border-t border-violet-700/30">
 <td class="py-3 pr-4 sticky left-0 bg-gradient-to-r from-violet-900/90 to-transparent">
 <div class="flex items-center">
 <div class="w-8 h-8 bg-gradient-to-br from-violet-600 to-purple-700 rounded-lg flex items-center justify-center mr-2 shadow">
 <i class="fas fa-door-open text-white text-xs"></i>
 </div>
 <div>
 <div class="text-white text-sm font-semibold">{{ $hallData['hall']->name }}</div>
 <div class="text-violet-400 text-xs">{{ $hallData['hall']->max_capacity ?? 0 }} people</div>
 </div>
 </div>
 </td>
 @foreach($hallData['days'] as $day)
 <td class="py-2 px-1 text-center">
 <div class="flex flex-col gap-1">
 <!-- Morning Slot -->
 @if($day['morning'] == 'available')
 <a href="{{ route('admin.premium-convention.index') }}?hall={{ $hallData['hall']->id }}&date={{ $day['date_str'] }}&slot=morning" 
 class="slot-btn flex items-center justify-center w-full py-1 px-1.5 rounded-md transition-all duration-200 bg-emerald-500/20 hover:bg-emerald-500/40 cursor-pointer">
 <i class="fas fa-sun text-[10px] text-emerald-400"></i>
 <span class="ml-1 text-[10px] text-emerald-300">Morning</span>
 </a>
 @else
 @php $mb = $day['morning_booking'] ?? null; @endphp
 <span class="group relative flex items-center justify-center w-full py-1 px-1.5 rounded-md bg-rose-500/20 cursor-help">
 <i class="fas fa-sun text-[10px] text-rose-400"></i>
 <span class="ml-1 text-[10px] text-rose-300">Morning</span>
 @if($mb)
 <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-56 bg-white rounded-xl shadow-2xl p-3 opacity-0 group-hover:opacity-100 transition-all duration-200 z-[100] pointer-events-none scale-95 group-hover:scale-100">
 <div class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white rotate-45"></div>
 <div class="text-gray-800 text-xs relative">
 <p class="font-bold text-sm text-rose-600 mb-1 truncate">{{ $mb->customer_name ?? 'N/A' }}</p>
 <p class="mb-0.5"><i class="fas fa-phone text-gray-400 mr-1 w-3"></i>{{ $mb->customer_phone ?? '-' }}</p>
 <p class="mb-0.5"><i class="fas fa-building text-gray-400 mr-1 w-3"></i>{{ $mb->event_type ?? '-' }}</p>
 <p class="mb-0.5"><i class="fas fa-users text-gray-400 mr-1 w-3"></i>{{ $mb->number_of_guests ?? 0 }} guests</p>
 <p class="text-emerald-600 font-semibold mt-1"><i class="fas fa-money-bill mr-1 w-3"></i>Bill: {{ number_format($mb->total_amount ?? 0, 0) }}</p>
 </div>
 </div>
 @endif
 </span>
 @endif
 <!-- Full Day Slot -->
 @if(($day['full_day'] ?? 'booked') == 'available')
 <a href="{{ route('admin.premium-convention.index') }}?hall={{ $hallData['hall']->id }}&date={{ $day['date_str'] }}&slot=full_day"
 class="slot-btn flex items-center justify-center w-full py-1 px-1.5 rounded-md transition-all duration-200 bg-amber-500/20 hover:bg-amber-500/40 cursor-pointer">
 <i class="fas fa-calendar-day text-[10px] text-amber-400"></i>
 <span class="ml-1 text-[10px] text-amber-300">Full Day</span>
 </a>
 @elseif(($day['full_day'] ?? 'booked') == 'unavailable')
 <!-- Unavailable: morning/night booked separately -->
 <span class="group relative flex items-center justify-center w-full py-1 px-1.5 rounded-md bg-gray-500/30 border border-dashed border-gray-500 cursor-help">
 <i class="fas fa-calendar-day text-[10px] text-gray-400"></i>
 <span class="ml-1 text-[10px] text-gray-400">Full Day</span>
 <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-48 bg-white rounded-xl shadow-2xl p-3 opacity-0 group-hover:opacity-100 transition-all duration-200 z-[100] pointer-events-none scale-95 group-hover:scale-100">
 <div class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white rotate-45"></div>
 <div class="text-gray-800 text-xs relative">
 <p class="text-gray-600">Full day unavailable - morning or night already booked</p>
 </div>
 </div>
 </span>
 @else
 @php $fdb = $day['full_day_booking'] ?? null; @endphp
 <!-- Actually booked as full_day -->
 <span class="group relative flex items-center justify-center w-full py-1 px-1.5 rounded-md bg-rose-500/20 cursor-help">
 <i class="fas fa-calendar-day text-[10px] text-rose-400"></i>
 <span class="ml-1 text-[10px] text-rose-300">Full Day</span>
 @if($fdb)
 <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-56 bg-white rounded-xl shadow-2xl p-3 opacity-0 group-hover:opacity-100 transition-all duration-200 z-[100] pointer-events-none scale-95 group-hover:scale-100">
 <div class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white rotate-45"></div>
 <div class="text-gray-800 text-xs relative">
 <p class="font-bold text-sm text-rose-600 mb-1 truncate">{{ $fdb->customer_name ?? 'N/A' }}</p>
 <p class="mb-0.5"><i class="fas fa-phone text-gray-400 mr-1 w-3"></i>{{ $fdb->customer_phone ?? '-' }}</p>
 <p class="mb-0.5"><i class="fas fa-building text-gray-400 mr-1 w-3"></i>{{ $fdb->event_type ?? '-' }}</p>
 <p class="mb-0.5"><i class="fas fa-users text-gray-400 mr-1 w-3"></i>{{ $fdb->number_of_guests ?? 0 }} guests</p>
 <p class="text-emerald-600 font-semibold mt-1"><i class="fas fa-money-bill mr-1 w-3"></i>Bill: {{ number_format($fdb->total_amount ?? 0, 0) }}</p>
 </div>
 </div>
 @endif
 </span>
 @endif
 <!-- Night Slot -->
 @if($day['night'] == 'available')
 <a href="{{ route('admin.premium-convention.index') }}?hall={{ $hallData['hall']->id }}&date={{ $day['date_str'] }}&slot=night"
 class="slot-btn flex items-center justify-center w-full py-1 px-1.5 rounded-md transition-all duration-200 bg-emerald-500/20 hover:bg-emerald-500/40 cursor-pointer">
 <i class="fas fa-moon text-[10px] text-emerald-400"></i>
 <span class="ml-1 text-[10px] text-emerald-300">Nights</span>
 </a>
 @else
 @php $nb = $day['night_booking'] ?? null; @endphp
 <span class="group relative flex items-center justify-center w-full py-1 px-1.5 rounded-md bg-rose-500/20 cursor-help">
 <i class="fas fa-moon text-[10px] text-rose-400"></i>
 <span class="ml-1 text-[10px] text-rose-300">Nights</span>
 @if($nb)
 <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-56 bg-white rounded-xl shadow-2xl p-3 opacity-0 group-hover:opacity-100 transition-all duration-200 z-[100] pointer-events-none scale-95 group-hover:scale-100">
 <div class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white rotate-45"></div>
 <div class="text-gray-800 text-xs relative">
 <p class="font-bold text-sm text-rose-600 mb-1 truncate">{{ $nb->customer_name ?? 'N/A' }}</p>
 <p class="mb-0.5"><i class="fas fa-phone text-gray-400 mr-1 w-3"></i>{{ $nb->customer_phone ?? '-' }}</p>
 <p class="mb-0.5"><i class="fas fa-building text-gray-400 mr-1 w-3"></i>{{ $nb->event_type ?? '-' }}</p>
 <p class="mb-0.5"><i class="fas fa-users text-gray-400 mr-1 w-3"></i>{{ $nb->number_of_guests ?? 0 }} guests</p>
 <p class="text-emerald-600 font-semibold mt-1"><i class="fas fa-money-bill mr-1 w-3"></i>Bill: {{ number_format($nb->total_amount ?? 0, 0) }}</p>
 </div>
 </div>
 @endif
 </span>
 @endif
 </div>
 </td>
 @endforeach
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @else
 <div class="text-center py-8 text-violet-300">
 <i class="fas fa-building-columns text-4xl mb-3 opacity-50"></i>
 <p>No halls added yet</p>
 </div>
 @endif
 </div>
 </div>

 <!-- Convention Charts Section -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
 <!-- Convention Booking Trend Chart -->
 <div class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-xl border border-violet-200 p-6">
 <div class="flex items-center justify-between mb-6">
 <h3 class="font-semibold text-violet-900"><i class="fas fa-chart-line mr-2 text-violet-600"></i>Booking </h3>
 <span class="text-xs text-violet-500 bg-violet-100 px-2 py-1 rounded-full">Last 7 days</span>
 </div>
 <div class="h-64">
 <canvas id="conventionBookingTrendChart"></canvas>
 </div>
 </div>

 <!-- Convention Revenue Chart -->
 <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-xl border border-purple-200 p-6">
 <div class="flex items-center justify-between mb-6">
 <h3 class="font-semibold text-purple-900"><i class="fas fa-chart-bar mr-2 text-purple-600"></i>Revenue Overview</h3>
 <span class="text-xs text-purple-500 bg-purple-100 px-2 py-1 rounded-full">Last 4 weeks</span>
 </div>
 <div class="h-64">
 <canvas id="conventionRevenueChart"></canvas>
 </div>
 </div>
 </div>
 </div>
 <!-- End Convention Dashboard -->

 <!-- ============ RESORT CHARTS - Only for Resort Mode ============ -->
 <div id="chartsSection" class="{{ $currentMode == 'convention' ? 'hidden' : '' }}">
 <!-- Charts Row -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <div class="bg-white rounded-xl border border-gray-200 p-6">
 <div class="flex items-center justify-between mb-6">
 <h3 class="font-semibold text-gray-900">Booking Trends</h3>
 <span class="text-xs text-gray-500">Last 7 days</span>
 </div>
 <div class="h-64">
 <canvas id="bookingTrendChart"></canvas>
 </div>
 </div>

 <div class="bg-white rounded-xl border border-gray-200 p-6">
 <div class="flex items-center justify-between mb-6">
 <h3 class="font-semibold text-gray-900">Revenue Overview</h3>
 <span class="text-xs text-gray-500">Last 4 weeks</span>
 </div>
 <div class="h-64">
 <canvas id="revenueChart"></canvas>
 </div>
 </div>
 </div>
 </div>
 <!-- End Charts Section (Resort Mode Only) -->

 <!-- ============ ROOM STATUS - Only for Resort Mode ============ -->
 <div id="roomStatusSection" class="{{ $currentMode == 'convention' ? 'hidden' : '' }}">
 <!-- Room Status Grid - Premium -->
 <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl overflow-hidden shadow-2xl">
 <div class="px-4 sm:px-6 py-4 border-b border-slate-700/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-lg">
 <i class="fas fa-door-open text-white"></i>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg">Room Status</h3>
 <p class="text-slate-400 text-xs">Click any room to book</p>
 </div>
 </div>
 <div class="relative">
 <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
 <input type="text" id="roomSearch" placeholder="Search room..." class="pl-9 pr-4 py-2 bg-slate-700/50 border border-slate-600 rounded-xl text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-44">
 </div>
 </div>
 <div class="p-4 sm:p-6">
 <div id="roomStatusGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
 @foreach($roomsWithStatus as $rs)
 <div class="room-card group relative rounded-2xl p-4 transition-all duration-300 cursor-pointer transform hover:scale-105 hover:z-10
 {{ $rs['status'] == 'occupied' 
 ? 'bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg shadow-rose-500/30' 
 : 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50' }}" 
 data-room="{{ strtolower($rs['room']->room_number) }}" 
 onclick="window.location='{{ route('admin.premium-booking.index') }}?room={{ $rs['room']->id }}'">
 <div class="absolute top-2 right-2">
 @if($rs['status'] == 'occupied')
 <span class="w-3 h-3 bg-white/30 rounded-full animate-pulse block"></span>
 @else
 <span class="w-3 h-3 bg-white rounded-full block"></span>
 @endif
 </div>
 <div class="text-center text-white">
 <div class="text-2xl sm:text-3xl font-black mb-1">{{ $rs['room']->room_number }}</div>
 <div class="text-xs opacity-80 truncate px-1" title="{{ $rs['room']->roomType->name ?? '' }}">{{ Str::limit($rs['room']->roomType->name ?? '', 18) }}</div>
 <div class="mt-3 flex items-center justify-center">
 @if($rs['status'] == 'occupied')
 <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold">
 <i class="fas fa-user-check mr-1.5"></i>Occupied
 </span>
 @else
 <span class="inline-flex items-center px-3 py-1 bg-white/30 backdrop-blur rounded-full text-xs font-bold">
 <i class="fas fa-check-circle mr-1.5"></i>Available
 </span>
 @endif
 </div>
 </div>

 <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity z-0"></div>

 @if($rs['status'] == 'occupied' && $rs['current_booking'])
 <!-- Hover Tooltip (below card) -->
 <div class="absolute top-full left-1/2 -translate-x-1/2 mt-3 w-60 bg-white rounded-xl shadow-2xl p-3.5 opacity-0 group-hover:opacity-100 transition-all duration-200 z-[100] pointer-events-none scale-95 group-hover:scale-100">
 <div class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-white rotate-45"></div>
 <div class="text-gray-800 text-xs relative">
 <p class="font-bold text-sm text-rose-600 mb-1 truncate">{{ $rs['current_booking']->customer_name }}</p>
 <p class="mb-0.5"><i class="fas fa-phone text-gray-400 mr-1 w-3"></i>{{ $rs['current_booking']->customer_phone }}</p>
 <p class="mb-0.5"><i class="fas fa-sign-in-alt text-green-500 mr-1 w-3"></i>In: {{ \Carbon\Carbon::parse($rs['current_booking']->check_in_date)->format('d M') }}</p>
 <p class="mb-0.5"><i class="fas fa-sign-out-alt text-orange-500 mr-1 w-3"></i>Out: {{ \Carbon\Carbon::parse($rs['current_booking']->check_out_date)->format('d M') }}</p>
 <p class="text-emerald-600 font-semibold mt-1"><i class="fas fa-calendar-check mr-1 w-3"></i>Available: {{ \Carbon\Carbon::parse($rs['available_from'])->format('d M Y') }}</p>
 </div>
 </div>
 @endif
 </div>
 @endforeach
 </div>
 <div class="mt-6 pt-4 border-t border-slate-700/50 flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-sm">
 <span class="flex items-center text-white">
 <span class="w-5 h-5 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg mr-2 shadow"></span>
 Available <span class="ml-1.5 px-2 py-0.5 bg-emerald-500/20 rounded-full text-emerald-400 font-bold">{{ collect($roomsWithStatus)->where('status', 'available')->count() }}</span>
 </span>
 <span class="flex items-center text-white">
 <span class="w-5 h-5 bg-gradient-to-br from-rose-500 to-pink-600 rounded-lg mr-2 shadow"></span>
 Occupied <span class="ml-1.5 px-2 py-0.5 bg-rose-500/20 rounded-full text-rose-400 font-bold">{{ collect($roomsWithStatus)->where('status', 'occupied')->count() }}</span>
 </span>
 </div>
 </div>
 </div>

 <!-- Recent Bookings - Responsive -->
 <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
 <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between">
 <h3 class="font-semibold text-gray-900"><i class="fas fa-clock mr-2 text-gray-400"></i>Recent Bookings</h3>
 <a href="{{ route('admin.bookings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
 </div>
 <!-- Mobile Card View -->
 <div class="block sm:hidden divide-y divide-gray-100">
 @forelse($recentBookings as $booking)
 <div class="p-4 hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
 <div class="flex items-center justify-between mb-2">
 <span class="text-sm font-bold text-gray-900">#{{ $booking->id }}</span>
 @if($booking->status == 'confirmed')
 <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Confirmed</span>
 @elseif($booking->status == 'pending')
 <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
 @elseif($booking->status == 'checked_in')
 <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Checked In</span>
 @else
 <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($booking->status) }}</span>
 @endif
 </div>
 <div class="text-sm font-medium text-gray-900">{{ $booking->customer_name }}</div>
 <div class="text-xs text-gray-500 mt-1">{{ $booking->customer_phone }}</div>
 <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
 <div class="text-xs text-gray-500">
 <i class="fas fa-bed mr-1"></i>{{ $booking->getAllRooms()->pluck('room_number')->implode(', ') ?: 'N/A' }}
 <span class="mx-2">•</span>
 <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M') }}
 </div>
 <div class="text-sm font-bold text-indigo-600">{{ number_format($booking->getCalculatedTotal()) }}</div>
 </div>
 </div>
 @empty
 <div class="p-8 text-center text-gray-500">No bookings found</div>
 @endforelse
 </div>
 <!-- Desktop Table View -->
 <div class="hidden sm:block overflow-x-auto">
 <table class="w-full min-w-[600px]">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-4 lg:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
 <th class="px-4 lg:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Guest</th>
 <th class="px-4 lg:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Room</th>
 <th class="px-4 lg:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">Check-in</th>
 <th class="px-4 lg:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
 <th class="px-4 lg:px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($recentBookings as $booking)
 <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
 <td class="px-4 lg:px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">#{{ $booking->id }}</td>
 <td class="px-4 lg:px-6 py-4">
 <div class="text-sm font-medium text-gray-900 truncate max-w-[150px]">{{ $booking->customer_name }}</div>
 <div class="text-xs text-gray-500">{{ $booking->customer_phone }}</div>
 </td>
 <td class="px-4 lg:px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $booking->getAllRooms()->pluck('room_number')->implode(', ') ?: 'N/A' }}</td>
 <td class="px-4 lg:px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
 <td class="px-4 lg:px-6 py-4 text-sm font-semibold text-gray-900 whitespace-nowrap">{{ number_format($booking->getCalculatedTotal()) }}</td>
 <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
 @if($booking->status == 'confirmed')
 <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Confirmed</span>
 @elseif($booking->status == 'pending')
 <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>
 @elseif($booking->status == 'checked_in')
 <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Checked In</span>
 @else
 <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ ucfirst($booking->status) }}</span>
 @endif
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-12 text-center text-gray-500">No bookings found</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
 <!-- End Room Status Section (Resort Mode Only) -->

 <!-- Search Section -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <div id="roomSearchSection" class="bg-white rounded-xl border border-gray-200 p-6 {{ $currentMode == 'convention' ? 'hidden' : '' }}">
 <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-search mr-2 text-gray-400"></i>Check Room Availability</h3>
 <form id="roomSearchForm" class="space-y-4">
 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-medium text-gray-600 mb-1">Check-in</label>
 <input type="date" id="roomCheckIn" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
 </div>
 <div>
 <label class="block text-xs font-medium text-gray-600 mb-1">Check-out</label>
 <input type="date" id="roomCheckOut" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
 </div>
 </div>
 <button type="submit" class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-medium hover:bg-gray-800 text-sm">
 <i class="fas fa-search mr-2"></i>Search Rooms
 </button>
 </form>
 <div id="roomResults" class="mt-4"></div>
 </div>

 <div id="hallSearchSection" class="bg-white rounded-xl border border-gray-200 p-6 {{ $currentMode == 'resort' ? 'hidden' : '' }}">
 <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-building mr-2 text-gray-400"></i>Check Hall Availability</h3>
 <form id="hallSearchForm" class="space-y-4">
 <div class="grid grid-cols-2 gap-4">
 <div>
 <label class="block text-xs font-medium text-gray-600 mb-1">Event Date</label>
 <input type="date" id="hallDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
 </div>
 <div>
 <label class="block text-xs font-medium text-gray-600 mb-1">Time Slot</label>
 <select id="hallTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
 <option value="morning">Morning</option>
 <option value="night">Night</option>
 <option value="full_day">Full Day</option>
 </select>
 </div>
 </div>
 <button type="submit" class="w-full bg-gray-900 text-white py-2.5 rounded-lg font-medium hover:bg-gray-800 text-sm">
 <i class="fas fa-search mr-2"></i>Search Halls
 </button>
 </form>
 <div id="hallResults" class="mt-4"></div>
 </div>
 </div>
</div>

<script>
// Dashboard Mode Switching
async function switchDashboardMode(mode) {
 try {
 // Update UI immediately for responsiveness
 const resortDashboard = document.getElementById('resortDashboard');
 const conventionDashboard = document.getElementById('conventionDashboard');
 const roomStatusSection = document.getElementById('roomStatusSection');
 const chartsSection = document.getElementById('chartsSection');
 const roomSearchSection = document.getElementById('roomSearchSection');
 const hallSearchSection = document.getElementById('hallSearchSection');
 const modeResortBtn = document.getElementById('modeResort');
 const modeConventionBtn = document.getElementById('modeConvention');
 const resortLinks = document.querySelectorAll('.resort-link');
 const conventionLinks = document.querySelectorAll('.convention-link');
 
 if (mode === 'resort') {
 resortDashboard?.classList.remove('hidden');
 conventionDashboard?.classList.add('hidden');
 roomStatusSection?.classList.remove('hidden');
 chartsSection?.classList.remove('hidden');
 roomSearchSection?.classList.remove('hidden');
 hallSearchSection?.classList.add('hidden');
 modeResortBtn?.classList.add('bg-white', 'shadow-lg', 'text-emerald-600');
 modeResortBtn?.classList.remove('text-slate-500', 'hover:text-slate-700');
 modeConventionBtn?.classList.remove('bg-white', 'shadow-lg', 'text-violet-600');
 modeConventionBtn?.classList.add('text-slate-500', 'hover:text-slate-700');
 resortLinks.forEach(el => el.classList.remove('hidden'));
 conventionLinks.forEach(el => el.classList.add('hidden'));
 } else {
 resortDashboard?.classList.add('hidden');
 conventionDashboard?.classList.remove('hidden');
 roomStatusSection?.classList.add('hidden');
 chartsSection?.classList.add('hidden');
 roomSearchSection?.classList.add('hidden');
 hallSearchSection?.classList.remove('hidden');
 modeConventionBtn?.classList.add('bg-white', 'shadow-lg', 'text-violet-600');
 modeConventionBtn?.classList.remove('text-slate-500', 'hover:text-slate-700');
 modeResortBtn?.classList.remove('bg-white', 'shadow-lg', 'text-emerald-600');
 modeResortBtn?.classList.add('text-slate-500', 'hover:text-slate-700');
 resortLinks.forEach(el => el.classList.add('hidden'));
 conventionLinks.forEach(el => el.classList.remove('hidden'));
 }
 
 // Save preference to server
 await fetch('{{ route("admin.dashboard.toggle-mode") }}', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': '{{ csrf_token() }}',
 'Accept': 'application/json'
 },
 body: JSON.stringify({ mode: mode })
 });
 } catch (error) {
 console.error('Error switching mode:', error);
 }
}

document.addEventListener('DOMContentLoaded', function() {
 // Room Search
 document.getElementById('roomSearch').addEventListener('input', function(e) {
 const query = e.target.value.toLowerCase();
 document.querySelectorAll('.room-card').forEach(card => {
 card.style.display = card.dataset.room.includes(query) ? '' : 'none';
 });
 });

 // Check if Chart.js is loaded
 if (typeof Chart === 'undefined') {
 console.error('Chart.js not loaded');
 return;
 }

 // Booking Trend Chart
 const bookingCanvas = document.getElementById('bookingTrendChart');
 if (bookingCanvas) {
 const bookingCtx = bookingCanvas.getContext('2d');
 const bookingGradient = bookingCtx.createLinearGradient(0, 0, 0, 250);
 bookingGradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
 bookingGradient.addColorStop(1, 'rgba(99, 102, 241, 0.02)');
 new Chart(bookingCtx, {
 type: 'line',
 data: {
 labels: {!! json_encode($chartData['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
 datasets: [{
 label: 'Bookings',
 data: {!! json_encode($chartData['bookings'] ?? [3, 5, 2, 8, 4, 7, 6]) !!},
 borderColor: '#6366f1',
 backgroundColor: bookingGradient,
 fill: true,
 tension: 0.4,
 borderWidth: 3,
 pointBackgroundColor: '#6366f1',
 pointBorderColor: '#fff',
 pointBorderWidth: 2,
 pointRadius: 5,
 pointHoverRadius: 7,
 }]
 },
 options: {
 responsive: true, 
 maintainAspectRatio: false,
 plugins: { legend: { display: false } },
 scales: { 
 y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { stepSize: 1 } }, 
 x: { grid: { display: false } } 
 }
 }
 });
 }

 // Revenue Chart
 const revenueCanvas = document.getElementById('revenueChart');
 if (revenueCanvas) {
 new Chart(revenueCanvas.getContext('2d'), {
 type: 'bar',
 data: {
 labels: {!! json_encode($chartData['revenueLabels'] ?? ['Week 1', 'Week 2', 'Week 3', 'Week 4']) !!},
 datasets: [{
 label: 'Revenue',
 data: {!! json_encode($chartData['revenue'] ?? [25000, 35000, 28000, 42000]) !!},
 backgroundColor: ['#6366f1', '#10b981', '#0ea5e9', '#f59e0b'],
 borderRadius: 8,
 borderSkipped: false,
 }]
 },
 options: {
 responsive: true, 
 maintainAspectRatio: false,
 plugins: { legend: { display: false } },
 scales: { 
 y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { callback: v => '' + v.toLocaleString() } }, 
 x: { grid: { display: false } } 
 }
 }
 });
 }

 // Convention Booking Trend Chart
 const conventionBookingCanvas = document.getElementById('conventionBookingTrendChart');
 if (conventionBookingCanvas) {
 const convCtx = conventionBookingCanvas.getContext('2d');
 const convGradient = convCtx.createLinearGradient(0, 0, 0, 250);
 convGradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)');
 convGradient.addColorStop(1, 'rgba(139, 92, 246, 0.02)');
 new Chart(convCtx, {
 type: 'line',
 data: {
 labels: {!! json_encode($chartData['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
 datasets: [{
 label: 'Convention Booking',
 data: {!! json_encode($chartData['conventionBookings'] ?? [0, 1, 0, 2, 1, 3, 2]) !!},
 borderColor: '#8b5cf6',
 backgroundColor: convGradient,
 fill: true,
 tension: 0.4,
 borderWidth: 3,
 pointBackgroundColor: '#8b5cf6',
 pointBorderColor: '#fff',
 pointBorderWidth: 2,
 pointRadius: 5,
 pointHoverRadius: 7,
 }]
 },
 options: {
 responsive: true, 
 maintainAspectRatio: false,
 plugins: { legend: { display: false } },
 scales: { 
 y: { beginAtZero: true, grid: { color: '#ede9fe' }, ticks: { stepSize: 1 } }, 
 x: { grid: { display: false } } 
 }
 }
 });
 }

 // Convention Revenue Chart
 const conventionRevenueCanvas = document.getElementById('conventionRevenueChart');
 if (conventionRevenueCanvas) {
 new Chart(conventionRevenueCanvas.getContext('2d'), {
 type: 'bar',
 data: {
 labels: {!! json_encode($chartData['revenueLabels'] ?? ['Week 1', 'Week 2', 'Week 3', 'Week 4']) !!},
 datasets: [{
 label: 'Convention Revenue',
 data: {!! json_encode($chartData['conventionRevenue'] ?? [15000, 25000, 18000, 32000]) !!},
 backgroundColor: ['#8b5cf6', '#a855f7', '#c084fc', '#d946ef'],
 borderRadius: 8,
 borderSkipped: false,
 }]
 },
 options: {
 responsive: true, 
 maintainAspectRatio: false,
 plugins: { legend: { display: false } },
 scales: { 
 y: { beginAtZero: true, grid: { color: '#fae8ff' }, ticks: { callback: v => '' + v.toLocaleString() } }, 
 x: { grid: { display: false } } 
 }
 }
 });
 }

 // Occupancy Chart
 const occupancyCanvas = document.getElementById('occupancyChart');
 if (occupancyCanvas) {
 new Chart(occupancyCanvas.getContext('2d'), {
 type: 'doughnut',
 data: {
 labels: ['Occupied', 'Available'],
 datasets: [{ 
 data: [{{ $stats['total_rooms'] - $stats['available_rooms'] }}, {{ $stats['available_rooms'] }}], 
 backgroundColor: ['#6366f1', '#e5e7eb'], 
 borderWidth: 0, 
 hoverBackgroundColor: ['#4f46e5', '#d1d5db'] 
 }]
 },
 options: { responsive: true, maintainAspectRatio: true, cutout: '75%', plugins: { legend: { display: false } } }
 });
 }

 // Room Search Form
 document.getElementById('roomSearchForm').addEventListener('submit', async (e) => {
 e.preventDefault();
 const resultsDiv = document.getElementById('roomResults');
 resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i></div>';
 try {
 const response = await fetch(`/admin/dashboard/search-rooms?checkIn=${document.getElementById('roomCheckIn').value}&checkOut=${document.getElementById('roomCheckOut').value}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
 const data = await response.json();
 const rooms = data.availableRooms || [];
 if (rooms.length === 0) {
 resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center text-sm text-red-600">No rooms available</div>';
 } else {
 let html = `<div class="bg-green-50 border border-green-200 rounded-lg p-3"><p class="text-sm text-green-700 font-medium mb-2">${rooms.length} rooms available</p><div class="flex flex-wrap gap-2">`;
 rooms.forEach(r => html += `<span class="px-2 py-1 bg-white rounded text-xs font-medium border">${r.room_number}</span>`);
 html += '</div><a href="/admin/premium-booking" class="mt-3 inline-block text-sm font-medium hover:underline">Create Booking →</a></div>';
 resultsDiv.innerHTML = html;
 }
 } catch { resultsDiv.innerHTML = '<div class="bg-red-50 rounded-lg p-3 text-center text-sm text-red-600">Error</div>'; }
 });

 // Hall Search Form
 document.getElementById('hallSearchForm').addEventListener('submit', async (e) => {
 e.preventDefault();
 const resultsDiv = document.getElementById('hallResults');
 resultsDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i></div>';
 try {
 const response = await fetch(`/admin/dashboard/search-halls?date=${document.getElementById('hallDate').value}&slot=${document.getElementById('hallTime').value}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
 const data = await response.json();
 const allHalls = @json($allHalls ?? []);
 const available = allHalls.filter(h => !(data.bookedHallIds || []).includes(h.id));
 if (available.length === 0) {
 resultsDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center text-sm text-red-600">No halls available</div>';
 } else {
 let html = `<div class="bg-green-50 border border-green-200 rounded-lg p-3"><p class="text-sm text-green-700 font-medium mb-2">${available.length} halls available</p>`;
 available.forEach(h => html += `<div class="flex justify-between py-2 border-t border-green-200 mt-2"><span class="text-sm font-medium">${h.name}</span><span class="text-xs text-gray-500">${Number(h.price_per_day).toLocaleString()}</span></div>`);
 html += '<a href="/admin/premium-convention" class="mt-3 inline-block text-sm font-medium hover:underline">Book Hall →</a></div>';
 resultsDiv.innerHTML = html;
 }
 } catch { resultsDiv.innerHTML = '<div class="bg-red-50 rounded-lg p-3 text-center text-sm text-red-600">Error</div>'; }
 });

 // Set default dates
 const today = new Date().toISOString().split('T')[0];
 document.getElementById('roomCheckIn').value = today;
 document.getElementById('hallDate').value = today;
 const tomorrow = new Date(); tomorrow.setDate(tomorrow.getDate() + 1);
 document.getElementById('roomCheckOut').value = tomorrow.toISOString().split('T')[0];
});
</script>
@endsection

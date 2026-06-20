@extends('layouts.admin')
@section('content')
<div class="space-y-6">
 <!-- Premium Header with Mode Toggle -->
 <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 shadow-2xl">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
 <div class="flex items-center">
 <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-amber-500/30">
 <i class="fas fa-calendar-day text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-2xl font-bold text-white">Today's Summary</h1>
 <p class="text-slate-400 text-sm mt-1">{{ \Carbon\Carbon::today()->format('d F Y') }} - {{ \Carbon\Carbon::today()->format('l') }}</p>
 </div>
 </div>
 
 @if($hasResortAccess && $hasConventionAccess)
 <!-- Mode Toggle -->
 <div class="flex items-center bg-slate-700/50 p-1.5 rounded-xl">
 <button type="button" id="modeResort" onclick="switchSummaryMode('resort')" 
 class="flex items-center px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 {{ $currentMode == 'resort' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg' : 'text-slate-400 hover:text-white' }}">
 <i class="fas fa-hotel mr-2"></i>Resort
 </button>
 <button type="button" id="modeConvention" onclick="switchSummaryMode('convention')"
 class="flex items-center px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 {{ $currentMode == 'convention' ? 'bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow-lg' : 'text-slate-400 hover:text-white' }}">
 <i class="fas fa-building-columns mr-2"></i>Convention
 </button>
 </div>
 @endif
 </div>
 </div>

 <!-- ============ RESORT SUMMARY ============ -->
 <div id="resortSummary" class="{{ $currentMode == 'convention' ? 'hidden' : '' }}">
 <!-- Resort Stats Cards -->
 <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
 <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 shadow-lg shadow-emerald-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-emerald-100 uppercase">Checked In</p>
 <p class="text-3xl font-bold text-white mt-1">{{ $resortStats['checkins_count'] }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-sign-in-alt text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 shadow-lg shadow-orange-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-orange-100 uppercase">Checked Out</p>
 <p class="text-3xl font-bold text-white mt-1">{{ $resortStats['checkouts_count'] }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-sign-out-alt text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 shadow-lg shadow-blue-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-blue-100 uppercase">Staying Now</p>
 <p class="text-3xl font-bold text-white mt-1">{{ $resortStats['staying_count'] }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-bed text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-5 shadow-lg shadow-teal-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-teal-100 uppercase">Revenue</p>
 <p class="text-2xl font-bold text-white mt-1">{{ number_format($resortStats['today_revenue']) }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-bangladeshi-taka-sign text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl p-5 shadow-lg shadow-rose-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-rose-100 uppercase">Remaining</p>
 <p class="text-2xl font-bold text-white mt-1">{{ number_format($resortStats['pending_payments']) }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-clock text-white text-lg"></i>
 </div>
 </div>
 </div>
 </div>

 <!-- Today's Checkins - Premium Card -->
 <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
 <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
 <i class="fas fa-sign-in-alt text-white"></i>
 </div>
 <div>
 <h2 class="text-lg font-bold text-white">Today's Checked In</h2>
 <p class="text-emerald-100 text-xs">{{ $todayCheckins->count() }} people Guest</p>
 </div>
 </div>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full">
 <thead class="bg-slate-50">
 <tr>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Mobile</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Room</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Checked Out</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Total</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Advance</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Remaining</th>
 <th class="px-4 py-3 text-center text-xs font-bold text-slate-600 uppercase">Status</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100">
 @forelse($todayCheckins as $index => $booking)
 <tr class="hover:bg-emerald-50/50 transition cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
 <td class="px-4 py-3 font-bold text-slate-600">{{ $index + 1 }}</td>
 <td class="px-4 py-3 font-semibold text-slate-800">{{ $booking->customer_name }}</td>
 <td class="px-4 py-3 text-slate-600">{{ $booking->customer_phone }}</td>
 <td class="px-4 py-3"><span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-bold">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</span></td>
 <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
 <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format($booking->getCalculatedTotal()) }}</td>
 <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ number_format($booking->advance_payment) }}</td>
 <td class="px-4 py-3 text-right font-semibold text-rose-600">{{ number_format($booking->getCalculatedRemaining()) }}</td>
 <td class="px-4 py-3 text-center">
 @if($booking->status == 'checked_in')
 <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Checked In</span>
 @elseif($booking->status == 'confirmed')
 <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Confirmed</span>
 @else
 <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">{{ $booking->status }}</span>
 @endif
 </td>
 </tr>
 @empty
 <tr><td colspan="9" class="px-6 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>No Checked In guests</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 <!-- Today's Checkouts -->
 <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
 <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
 <i class="fas fa-sign-out-alt text-white"></i>
 </div>
 <div>
 <h2 class="text-lg font-bold text-white">Today's Checked Out</h2>
 <p class="text-orange-100 text-xs">{{ $todayCheckouts->count() }} people Guest</p>
 </div>
 </div>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full">
 <thead class="bg-slate-50">
 <tr>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Mobile</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Room</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Checked In</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Total</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Advance</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Remaining</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100">
 @forelse($todayCheckouts as $index => $booking)
 <tr class="hover:bg-orange-50/50 transition cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
 <td class="px-4 py-3 font-bold text-slate-600">{{ $index + 1 }}</td>
 <td class="px-4 py-3 font-semibold text-slate-800">{{ $booking->customer_name }}</td>
 <td class="px-4 py-3 text-slate-600">{{ $booking->customer_phone }}</td>
 <td class="px-4 py-3"><span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-lg text-sm font-bold">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</span></td>
 <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
 <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format($booking->getCalculatedTotal()) }}</td>
 <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ number_format($booking->advance_payment) }}</td>
 <td class="px-4 py-3 text-right font-semibold text-rose-600">{{ number_format($booking->getCalculatedRemaining()) }}</td>
 </tr>
 @empty
 <tr><td colspan="8" class="px-6 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>No Checked Out guests</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 <!-- Currently Staying -->
 <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
 <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
 <div class="flex items-center justify-between">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
 <i class="fas fa-bed text-white"></i>
 </div>
 <div>
 <h2 class="text-lg font-bold text-white">Currently Staying Now</h2>
 <p class="text-blue-100 text-xs">{{ $currentlyStaying->count() }} people Guest</p>
 </div>
 </div>
 </div>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full">
 <thead class="bg-slate-50">
 <tr>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Mobile</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Room</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Checked Out</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Total</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Remaining</th>
 <th class="px-4 py-3 text-center text-xs font-bold text-slate-600 uppercase">Status</th>
 <th class="px-4 py-3 text-center text-xs font-bold text-slate-600 uppercase">Action</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100">
 @forelse($currentlyStaying as $index => $booking)
 <tr class="hover:bg-blue-50/50 transition">
 <td class="px-4 py-3 font-bold text-slate-600">{{ $index + 1 }}</td>
 <td class="px-4 py-3 font-semibold text-slate-800">{{ $booking->customer_name }}</td>
 <td class="px-4 py-3 text-slate-600">{{ $booking->customer_phone }}</td>
 <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-bold">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</span></td>
 <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
 <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format($booking->getCalculatedTotal()) }}</td>
 <td class="px-4 py-3 text-right font-semibold text-rose-600">{{ number_format($booking->getCalculatedRemaining()) }}</td>
 <td class="px-4 py-3 text-center">
 @if($booking->status == 'checked_in')
 <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Checked In</span>
 @else
 <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Confirmed</span>
 @endif
 </td>
 <td class="px-4 py-3 text-center">
 <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="inline-flex items-center gap-2">
 @csrf
 <select name="status" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
 <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
 <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>Checked In</option>
 <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
 </select>
 <button type="submit" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-3 py-1.5 rounded-lg text-xs hover:shadow-lg transition"><i class="fas fa-save"></i></button>
 </form>
 </td>
 </tr>
 @empty
 <tr><td colspan="9" class="px-6 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i> Currently Staying Now </td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
 <!-- End Resort Summary -->

 <!-- ============ CONVENTION SUMMARY ============ -->
 <div id="conventionSummary" class="{{ $currentMode == 'resort' ? 'hidden' : '' }}">
 <!-- Convention Stats Cards -->
 <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
 <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl p-5 shadow-lg shadow-violet-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-violet-100 uppercase">Total Event</p>
 <p class="text-3xl font-bold text-white mt-1">{{ $conventionStats['events_count'] }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-day text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 shadow-lg shadow-purple-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-purple-100 uppercase">Confirmed</p>
 <p class="text-3xl font-bold text-white mt-1">{{ $conventionStats['confirmed_count'] }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-check-circle text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-fuchsia-500 to-fuchsia-600 rounded-xl p-5 shadow-lg shadow-fuchsia-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-fuchsia-100 uppercase">Pending</p>
 <p class="text-3xl font-bold text-white mt-1">{{ $conventionStats['pending_count'] }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-clock text-white text-lg"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-5 shadow-lg shadow-pink-200">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-pink-100 uppercase">Revenue</p>
 <p class="text-2xl font-bold text-white mt-1">{{ number_format($conventionStats['today_revenue']) }}</p>
 </div>
 <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center">
 <i class="fas fa-bangladeshi-taka-sign text-white text-lg"></i>
 </div>
 </div>
 </div>
 </div>

 <!-- Today's Conventions -->
 <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
 <div class="bg-gradient-to-r from-violet-500 to-purple-500 px-6 py-4">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-3">
 <i class="fas fa-building-columns text-white"></i>
 </div>
 <div>
 <h2 class="text-lg font-bold text-white">Today's Convention</h2>
 <p class="text-violet-100 text-xs">{{ $todayConventions->count() }} Events</p>
 </div>
 </div>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full">
 <thead class="bg-slate-50">
 <tr>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">#</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Mobile</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Hall</th>
 <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Time</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Total</th>
 <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Advance</th>
 <th class="px-4 py-3 text-center text-xs font-bold text-slate-600 uppercase">Status</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100">
 @forelse($todayConventions as $index => $booking)
 <tr class="hover:bg-violet-50/50 transition cursor-pointer" onclick="window.location='{{ route('admin.convention-bookings.show', $booking) }}'">
 <td class="px-4 py-3 font-bold text-slate-600">{{ $index + 1 }}</td>
 <td class="px-4 py-3 font-semibold text-slate-800">{{ $booking->customer_name }}</td>
 <td class="px-4 py-3 text-slate-600">{{ $booking->customer_phone }}</td>
 <td class="px-4 py-3"><span class="px-2 py-1 bg-violet-100 text-violet-700 rounded-lg text-sm font-bold">{{ $booking->conventionHall->name ?? 'N/A' }}</span></td>
 <td class="px-4 py-3 text-slate-600">
 @if($booking->time_slot == 'morning') Morning
 @elseif($booking->time_slot == 'night') Nights
 @elseif($booking->time_slot == 'full_day') Full Day
 @else {{ $booking->time_slot }}
 @endif
 </td>
 <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format($booking->total_amount) }}</td>
 <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ number_format($booking->advance_payment ?? 0) }}</td>
 <td class="px-4 py-3 text-center">
 @if($booking->status == 'confirmed')
 <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Confirmed</span>
 @elseif($booking->status == 'pending')
 <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Pending</span>
 @else
 <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">{{ $booking->status }}</span>
 @endif
 </td>
 </tr>
 @empty
 <tr><td colspan="8" class="px-6 py-10 text-center text-slate-400"><i class="fas fa-inbox text-4xl mb-2 block"></i>No conventions today</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>
 </div>
 <!-- End Convention Summary -->
</div>

<script>
function switchSummaryMode(mode) {
 const resortSummary = document.getElementById('resortSummary');
 const conventionSummary = document.getElementById('conventionSummary');
 const modeResortBtn = document.getElementById('modeResort');
 const modeConventionBtn = document.getElementById('modeConvention');
 
 if (mode === 'resort') {
 resortSummary?.classList.remove('hidden');
 conventionSummary?.classList.add('hidden');
 modeResortBtn?.classList.add('bg-gradient-to-r', 'from-emerald-500', 'to-teal-500', 'text-white', 'shadow-lg');
 modeResortBtn?.classList.remove('text-slate-400', 'hover:text-white');
 modeConventionBtn?.classList.remove('bg-gradient-to-r', 'from-violet-500', 'to-purple-500', 'text-white', 'shadow-lg');
 modeConventionBtn?.classList.add('text-slate-400', 'hover:text-white');
 } else {
 resortSummary?.classList.add('hidden');
 conventionSummary?.classList.remove('hidden');
 modeConventionBtn?.classList.add('bg-gradient-to-r', 'from-violet-500', 'to-purple-500', 'text-white', 'shadow-lg');
 modeConventionBtn?.classList.remove('text-slate-400', 'hover:text-white');
 modeResortBtn?.classList.remove('bg-gradient-to-r', 'from-emerald-500', 'to-teal-500', 'text-white', 'shadow-lg');
 modeResortBtn?.classList.add('text-slate-400', 'hover:text-white');
 }
 
 // Save preference to server
 fetch('{{ route("admin.dashboard.toggle-mode") }}', {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': '{{ csrf_token() }}',
 'Accept': 'application/json'
 },
 body: JSON.stringify({ mode: mode })
 }).catch(err => console.error('Error:', err));
}
</script>
@endsection

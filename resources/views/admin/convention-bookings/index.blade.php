@extends('layouts.admin')

@section('title', 'Convention Booking')
@section('header', 'Convention Hall Booking Management')

@section('content')
<!-- Premium Header -->
<div class="bg-gradient-to-r from-violet-600 via-purple-600 to-violet-700 rounded-2xl shadow-xl p-6 mb-6">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
 <div class="text-white">
 <h1 class="text-2xl lg:text-3xl font-bold flex items-center">
 <i class="fas fa-building-columns mr-3 text-violet-200"></i>
 Convention Hall Booking
 </h1>
 <p class="text-violet-100 mt-2 text-sm lg:text-base">Total Booking: <span class="font-bold text-white">{{ $paginatedGroups->total() }}</span> </p>
 </div>
 <a href="{{ route('admin.premium-convention.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-violet-700 rounded-xl hover:bg-violet-50 transition font-bold shadow-lg hover:shadow-xl text-sm lg:text-base">
 <i class="fas fa-plus-circle mr-2"></i>
 New Hall Booking
 </a>
 </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
 @php
 $today = \Carbon\Carbon::today();
 $todayBookings = \App\Models\ConventionBooking::whereDate('event_date', $today)->count();
 $confirmedBookings = \App\Models\ConventionBooking::where('status', 'confirmed')->count();
 $pendingBookings = \App\Models\ConventionBooking::where('status', 'pending')->count();
 $totalRevenue = \App\Models\ConventionBooking::sum('total_amount');
 @endphp
 
 <div class="bg-white rounded-xl p-4 shadow-lg border-l-4 border-violet-500">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs text-gray-500 uppercase tracking-wide">Today's Events</p>
 <p class="text-2xl font-bold text-violet-600">{{ $todayBookings }}</p>
 </div>
 <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center">
 <i class="fas fa-calendar-day text-violet-600"></i>
 </div>
 </div>
 </div>
 
 <div class="bg-white rounded-xl p-4 shadow-lg border-l-4 border-emerald-500">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs text-gray-500 uppercase tracking-wide">Confirmed</p>
 <p class="text-2xl font-bold text-emerald-600">{{ $confirmedBookings }}</p>
 </div>
 <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
 <i class="fas fa-check-circle text-emerald-600"></i>
 </div>
 </div>
 </div>
 
 <div class="bg-white rounded-xl p-4 shadow-lg border-l-4 border-amber-500">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs text-gray-500 uppercase tracking-wide">Pending</p>
 <p class="text-2xl font-bold text-amber-600">{{ $pendingBookings }}</p>
 </div>
 <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
 <i class="fas fa-clock text-amber-600"></i>
 </div>
 </div>
 </div>
 
 <div class="bg-white rounded-xl p-4 shadow-lg border-l-4 border-blue-500">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs text-gray-500 uppercase tracking-wide">Total Revenue</p>
 <p class="text-xl font-bold text-blue-600">{{ number_format($totalRevenue, 0) }}</p>
 </div>
 <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
 <i class="fas fa-money-bill text-blue-600"></i>
 </div>
 </div>
 </div>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
 <!-- Table Header -->
 <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
 <h2 class="text-lg font-bold text-slate-700 flex items-center">
 <i class="fas fa-list mr-2 text-violet-500"></i>
 All Booking List
 </h2>
 </div>
 
 <div class="overflow-x-auto">
 <table class="w-full">
 <thead class="bg-gradient-to-r from-violet-50 to-purple-50">
 <tr>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-hashtag mr-1"></i>ID
 </th>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-user mr-1"></i>Customer
 </th>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-building mr-1"></i>Hall
 </th>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-calendar mr-1"></i>Event
 </th>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-users mr-1"></i>Guest
 </th>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-money-bill mr-1"></i>Amount
 </th>
 <th class="px-4 py-4 text-left text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-info-circle mr-1"></i>Status
 </th>
 <th class="px-4 py-4 text-center text-xs font-bold text-violet-700 uppercase tracking-wider">
 <i class="fas fa-cog mr-1"></i>Action
 </th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-100">
 @forelse($paginatedGroups as $group)
 @php
 $first = $group['first'];
 @endphp
 <tr class="hover:bg-violet-50/50 transition">
 <td class="px-4 py-4">
 <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-100 text-violet-700 font-bold text-sm">
 #{{ $first->id }}
 </span>
 </td>
 <td class="px-4 py-4">
 <div class="font-semibold text-gray-800">{{ $first->customer_name }}</div>
 <div class="text-xs text-gray-500 flex items-center mt-1">
 <i class="fas fa-phone mr-1"></i>{{ $first->customer_phone }}
 </div>
 @if($first->organization_name)
 <div class="text-xs text-violet-600 flex items-center mt-1">
 <i class="fas fa-building mr-1"></i>{{ $first->organization_name }}
 </div>
 @endif
 </td>
 <td class="px-4 py-4">
 <div class="font-semibold text-gray-700">
 @foreach($group['hall_names'] as $index => $hallName)
 {{ $hallName }}{{ $index < count($group['hall_names']) - 1 ? ' + ' : '' }}
 @endforeach
 </div>
 <div class="text-xs text-gray-500">
 @if($first->time_slot == 'morning')
 🌅 Morning
 @elseif($first->time_slot == 'night')
 🌙 Nights
 @else
 🌞 Full Day
 @endif
 </div>
 </td>
 <td class="px-4 py-4">
 <div class="text-sm font-medium text-gray-800">
 {{ $group['event_date']->format('d M, Y') }}
 </div>
 <div class="text-xs mt-1">
 <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-medium">
 {{ ucfirst($group['event_type']) }}
 </span>
 </div>
 </td>
 <td class="px-4 py-4">
 <div class="flex items-center">
 <i class="fas fa-users text-gray-400 mr-2"></i>
 <span class="font-semibold text-gray-700">{{ $group['guest_count'] }}</span>
 </div>
 </td>
 <td class="px-4 py-4">
 <div class="font-bold text-violet-600">{{ number_format($group['total_amount'], 0) }}</div>
 @php
 $paid = $group['bookings']->pluck('payments')->flatten()->sum('amount') ?? 0;
 $due = $group['total_amount'] - $paid;
 @endphp
 @if($due > 0)
 <div class="text-xs text-red-500 mt-1">Remaining: {{ number_format($due, 0) }}</div>
 @else
 <div class="text-xs text-emerald-500 mt-1">✅ Paid</div>
 @endif
 </td>
 <td class="px-4 py-4">
 @if($group['status'] == 'confirmed')
 <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
 <i class="fas fa-check-circle mr-1"></i>Confirmed
 </span>
 @elseif($group['status'] == 'pending')
 <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
 <i class="fas fa-clock mr-1"></i>Pending
 </span>
 @elseif($group['status'] == 'completed')
 <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
 <i class="fas fa-flag-checkered mr-1"></i>Completed
 </span>
 @else
 <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
 <i class="fas fa-times-circle mr-1"></i>Cancelled
 </span>
 @endif
 </td>
 <td class="px-4 py-4">
 <div class="flex items-center justify-center gap-2">
 <a href="{{ route('admin.convention-bookings.show', $first) }}" 
 class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 hover:bg-violet-200 flex items-center justify-center transition" 
 title="Details">
 <i class="fas fa-eye text-sm"></i>
 </a>
 <a href="{{ route('admin.convention-bookings.edit', $first) }}" 
 class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center transition" 
 title="Edit">
 <i class="fas fa-edit text-sm"></i>
 </a>
 <form action="{{ route('admin.convention-bookings.destroy', $first) }}" method="POST" class="inline" onsubmit="return confirm('Booking #{{ $first->id }} Do you want to delete?')">
 @csrf
 @method('DELETE')
 <button type="submit" 
 class="w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition" 
 title="Delete">
 <i class="fas fa-trash text-sm"></i>
 </button>
 </form>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="8" class="px-6 py-16 text-center">
 <div class="flex flex-col items-center">
 <div class="w-20 h-20 rounded-full bg-violet-100 flex items-center justify-center mb-4">
 <i class="fas fa-calendar-times text-3xl text-violet-400"></i>
 </div>
 <p class="text-gray-500 text-lg font-semibold mb-2">No bookings found</p>
 <p class="text-gray-400 text-sm mb-4">New Convention Hall Booking </p>
 <a href="{{ route('admin.premium-convention.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 text-white rounded-xl hover:from-violet-700 hover:to-purple-700 transition font-semibold shadow-lg">
 <i class="fas fa-plus-circle mr-2"></i>
 New Hall Booking
 </a>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
</div>

<!-- Pagination -->
@if($paginatedGroups->hasPages())
<div class="mt-6 flex justify-center">
 {{ $paginatedGroups->links() }}
</div>
@endif
@endsection

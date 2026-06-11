@extends('layouts.admin')

@section('title', 'Bookings')
@section('header', 'Bookings Management')

@section('content')
<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
 <form method="GET" action="{{ route('admin.bookings.index') }}" class="space-y-4">
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
 <!-- Search -->
 <div>
 <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Search</label>
 <div class="flex gap-2">
 <select name="type" class="px-2 sm:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 flex-shrink-0">
 <option value="name" {{ request('type') == 'name' ? 'selected' : '' }}>Name</option>
 <option value="phone" {{ request('type') == 'phone' ? 'selected' : '' }}>Phone</option>
 <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
 </select>
 <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="flex-1 min-w-0 px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 </div>
 </div>

 <!-- Status Filter -->
 <div>
 <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Booking Status</label>
 <select name="status" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
 <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
 <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
 <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Check-In</option>
 <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Check-Out</option>
 <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
 </select>
 </div>

 <!-- Payment Status Filter -->
 <div>
 <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Payment Status</label>
 <select name="payment_status" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>All</option>
 <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
 <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
 <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
 <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
 </select>
 </div>

 <!-- Discount Approval Status Filter -->
 <div>
 <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Discount Status</label>
 <select name="discount_status" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <option value="all" {{ request('discount_status') == 'all' ? 'selected' : '' }}>All</option>
 <option value="has_discount" {{ request('discount_status') == 'has_discount' ? 'selected' : '' }}>Has Discount</option>
 <option value="pending" {{ request('discount_status') == 'pending' ? 'selected' : '' }}>Pending</option>
 <option value="approved" {{ request('discount_status') == 'approved' ? 'selected' : '' }}>Approved</option>
 <option value="rejected" {{ request('discount_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
 </select>
 </div>

 <!-- Quick Date Filters -->
 <div>
 <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Date Filter</label>
 <select id="dateFilter" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <option value="">Custom</option>
 <option value="today">Today</option>
 <option value="yesterday">Yesterday</option>
 <option value="this_week">This Week</option>
 <option value="this_month">This Month</option>
 <option value="last_month">Last Month</option>
 </select>
 </div>
 </div>

 <!-- Date Range Filters - Collapsible on mobile -->
 <details class="sm:hidden">
 <summary class="text-sm font-semibold text-indigo-600 cursor-pointer py-2">📅 View Date Range</summary>
 <div class="grid grid-cols-1 gap-3 pt-2">
 <div class="space-y-1">
 <label class="block text-xs font-semibold text-gray-700">Check-In</label>
 <div class="grid grid-cols-2 gap-2">
 <input type="date" name="check_in_from_m" value="{{ request('check_in_from') }}" class="px-2 py-2 text-sm border border-gray-300 rounded-lg">
 <input type="date" name="check_in_to_m" value="{{ request('check_in_to') }}" class="px-2 py-2 text-sm border border-gray-300 rounded-lg">
 </div>
 </div>
 <div class="space-y-1">
 <label class="block text-xs font-semibold text-gray-700">Check-Out</label>
 <div class="grid grid-cols-2 gap-2">
 <input type="date" name="check_out_from_m" value="{{ request('check_out_from') }}" class="px-2 py-2 text-sm border border-gray-300 rounded-lg">
 <input type="date" name="check_out_to_m" value="{{ request('check_out_to') }}" class="px-2 py-2 text-sm border border-gray-300 rounded-lg">
 </div>
 </div>
 </div>
 </details>
 
 <!-- Desktop Date Range -->
 <div class="hidden sm:grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
 <div class="space-y-2">
 <label class="block text-sm font-semibold text-gray-700">Check-In Date</label>
 <div class="grid grid-cols-2 gap-2">
 <input type="date" name="check_in_from" value="{{ request('check_in_from') }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <input type="date" name="check_in_to" value="{{ request('check_in_to') }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 </div>
 </div>
 <div class="space-y-2">
 <label class="block text-sm font-semibold text-gray-700">Check-Out Date</label>
 <div class="grid grid-cols-2 gap-2">
 <input type="date" name="check_out_from" value="{{ request('check_out_from') }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <input type="date" name="check_out_to" value="{{ request('check_out_to') }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 </div>
 </div>
 <div class="space-y-2">
 <label class="block text-sm font-semibold text-gray-700">Booking Date</label>
 <div class="grid grid-cols-2 gap-2">
 <input type="date" name="booking_from" value="{{ request('booking_from') }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 <input type="date" name="booking_to" value="{{ request('booking_to') }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
 </div>
 </div>
 </div>

 <!-- Filter Buttons -->
 <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
 <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-4 sm:px-6 py-2.5 rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition shadow-md text-sm font-medium">
 <i class="fas fa-filter mr-2"></i>Filter
 </button>
 <a href="{{ route('admin.bookings.index') }}" class="bg-gray-500 text-white px-4 sm:px-6 py-2.5 rounded-lg hover:bg-gray-600 transition text-center text-sm font-medium">
 <i class="fas fa-redo mr-2"></i>Reset
 </a>
 </div>
 </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <!-- Mobile Card View -->
 <div class="sm:hidden divide-y divide-gray-100">
 @forelse($bookings as $booking)
 <div class="p-4 hover:bg-gray-50" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
 <div class="flex items-center justify-between mb-2">
 <span class="text-sm font-bold text-indigo-600">#{{ $booking->id }}</span>
 <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
 @if($booking->status == 'confirmed') bg-blue-100 text-blue-800
 @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
 @elseif($booking->status == 'checked_in') bg-green-100 text-green-800
 @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
 @else bg-red-100 text-red-800
 @endif">
 @if($booking->status == 'confirmed') Confirmed
 @elseif($booking->status == 'pending') Pending
 @elseif($booking->status == 'checked_in') Check-In
 @elseif($booking->status == 'checked_out') Check-Out
 @else Cancelled
 @endif
 </span>
 </div>
 <div class="font-semibold text-gray-800">{{ $booking->customer_name }}</div>
 <div class="text-xs text-gray-500 mt-1"><i class="fas fa-phone mr-1"></i>{{ $booking->customer_phone }}</div>
 @php $allRooms = $booking->getAllRooms(); @endphp
 <div class="text-xs text-gray-600 mt-2">
 <i class="fas fa-bed mr-1"></i>
 @if($allRooms->count() > 1)
 {{ $allRooms->count() }} Rooms ({{ $allRooms->pluck('room_number')->implode(', ') }})
 @elseif($allRooms->count() == 1)
 {{ $allRooms->first()->room_number }}
 @else
 N/A
 @endif
 </div>
 <div class="flex items-center justify-between mt-2">
 <div class="text-[10px] text-gray-500">
  @if($booking->discount_reference)
   Ref: {{ Str::limit($booking->discount_reference, 15) }}
  @elseif($booking->reference_name)
   Ref: {{ Str::limit($booking->reference_name, 15) }}
  @endif
 </div>
 <div class="text-[10px] text-gray-500">
  @php
   $mCreatorName = $booking->createdBy?->name ?? 'System';
  @endphp
  By: {{ Str::limit($mCreatorName, 12) }}
 </div>
 </div>
 @if(($booking->discount_amount ?? 0) > 0 || ($booking->discount_type === 'percentage' && ($booking->discount_percentage ?? 0) > 0))
 <div class="mt-1">
  @if($booking->discount_status === 'approved')
   <span class="text-[10px] font-bold text-emerald-600">Approved{{ $booking->discountApprovedBy ? ' by ' . Str::limit($booking->discountApprovedBy->name, 10) : '' }}</span>
  @elseif($booking->discount_status === 'pending')
   <span class="text-[10px] font-bold text-amber-600">Pending{{ $booking->discountRequestedBy ? ' by ' . Str::limit($booking->discountRequestedBy->name, 10) : '' }}</span>
  @elseif($booking->discount_status === 'rejected')
   <span class="text-[10px] font-bold text-red-600">Rejected{{ $booking->discountApprovedBy ? ' by ' . Str::limit($booking->discountApprovedBy->name, 10) : '' }}</span>
  @endif
 </div>
 @endif
 <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
 <div class="text-xs text-gray-500">
 {{ $booking->check_in_date->format('d M') }} - {{ $booking->check_out_date->format('d M Y') }}
 </div>
 @php $calculatedTotal = $booking->getCalculatedTotal(); @endphp
 <div class="text-sm font-bold text-gray-800">{{ number_format($calculatedTotal) }}</div>
 </div>
 <div class="flex gap-3 mt-3">
 <a href="{{ route('admin.bookings.show', $booking) }}" class="flex-1 text-center py-2 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-medium">
 <i class="fas fa-eye mr-1"></i>View
 </a>
 <a href="{{ route('admin.bookings.edit', $booking) }}" class="flex-1 text-center py-2 bg-gray-50 text-gray-600 rounded-lg text-xs font-medium">
 <i class="fas fa-edit mr-1"></i>Edit
 </a>
 </div>
 </div>
 @empty
 <div class="px-6 py-8 text-center text-gray-500">
 <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
 <div>No bookings found</div>
 </div>
 @endforelse
 </div>
 
 <!-- Desktop Table View -->
 <div class="hidden sm:block overflow-x-auto">
 <table class="w-full min-w-[900px]">
 <thead class="bg-gradient-to-r from-indigo-50 to-indigo-100">
 <tr>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">ID</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Customer</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Room</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Check-In</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Check-Out</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Total Amount</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Reference</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Created By</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Updated By</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Status</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Discount Status</th>
 <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase whitespace-nowrap">Action</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($bookings as $booking)
 <tr class="hover:bg-indigo-50/50 transition">
 <td class="px-3 lg:px-4 py-3 font-semibold text-gray-700 whitespace-nowrap">#{{ $booking->id }}</td>
 <td class="px-3 lg:px-4 py-3">
 <div class="font-semibold text-gray-800 truncate max-w-[120px]">{{ $booking->customer_name }}</div>
 <div class="text-xs text-gray-500"><i class="fas fa-phone mr-1"></i>{{ $booking->customer_phone }}</div>
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 @php $allRooms = $booking->getAllRooms(); @endphp
 @if($allRooms->count() > 1)
 <div class="font-medium text-gray-800">{{ $allRooms->count() }} Rooms</div>
 <div class="text-xs text-gray-500 truncate max-w-[100px]">{{ $allRooms->pluck('room_number')->implode(', ') }}</div>
 @elseif($allRooms->count() == 1)
 <div class="font-medium text-gray-800">{{ $allRooms->first()->roomType->name ?? 'Room' }}</div>
 <div class="text-xs text-gray-500">{{ $allRooms->first()->room_number }}</div>
 @else
 <div class="text-gray-500">No room</div>
 @endif
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 <div class="text-gray-700 text-sm">{{ $booking->check_in_date->format('d M Y') }}</div>
 @if($booking->check_in_time)
 <div class="text-xs text-gray-500">{{ $booking->check_in_time }}</div>
 @endif
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 <div class="text-gray-700 text-sm">{{ $booking->check_out_date->format('d M Y') }}</div>
 @if($booking->check_out_time)
 <div class="text-xs text-gray-500">{{ $booking->check_out_time }}</div>
 @endif
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 @php 
 $calculatedTotal = $booking->getCalculatedTotal();
 $calculatedRemaining = $booking->getCalculatedRemaining();
 @endphp
 <div class="font-bold text-gray-800">{{ number_format($calculatedTotal) }}</div>
 @if($booking->advance_payment > 0)
 <div class="text-xs text-indigo-600">Advance: {{ number_format($booking->advance_payment) }}</div>
 @endif
 @if($calculatedRemaining > 0)
 <div class="text-xs text-red-600">Due: {{ number_format($calculatedRemaining) }}</div>
 @endif
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 @if($booking->discount_reference)
 <div class="font-medium text-gray-800 text-sm truncate max-w-[100px]" title="{{ $booking->discount_reference }}">{{ $booking->discount_reference }}</div>
 @elseif($booking->reference_name)
 <div class="font-medium text-gray-800 text-sm truncate max-w-[100px]">{{ $booking->reference_name }}</div>
 @if($booking->reference_phone)
 <div class="text-xs text-gray-500">{{ $booking->reference_phone }}</div>
 @endif
 @else
 <span class="text-gray-400">-</span>
 @endif
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 @php
 $creatorName = $booking->createdBy?->name ?? ($booking->created_by_id ? 'User #' . $booking->created_by_id : 'System');
 @endphp
 <div class="font-medium text-gray-800 text-sm truncate max-w-[80px]" title="{{ $creatorName }}">{{ $creatorName }}</div>
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 @php
 $updaterName = $booking->updatedBy?->name ?? ($booking->updated_by_id ? 'User #' . $booking->updated_by_id : '-');
 @endphp
 <div class="font-medium text-gray-800 text-sm truncate max-w-[80px]" title="{{ $updaterName }}">{{ $updaterName }}</div>
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 @if($booking->status == 'confirmed') bg-blue-100 text-blue-800
 @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
 @elseif($booking->status == 'checked_in') bg-green-100 text-green-800
 @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
 @else bg-red-100 text-red-800
 @endif">
 @if($booking->status == 'confirmed') Confirmed
 @elseif($booking->status == 'pending') Pending
 @elseif($booking->status == 'checked_in') Check-In
 @elseif($booking->status == 'checked_out') Check-Out
 @else Cancelled
 @endif
 </span>
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 @if(($booking->discount_amount ?? 0) > 0 || ($booking->discount_type === 'percentage' && ($booking->discount_percentage ?? 0) > 0))
 @if($booking->discount_status === 'approved')
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Approved</span>
 @if($booking->discountApprovedBy)
 <div class="text-[10px] text-emerald-600 mt-0.5">by {{ $booking->discountApprovedBy->name }}</div>
 @endif
 @elseif($booking->discount_status === 'pending')
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Pending</span>
 @if($booking->discountRequestedBy)
 <div class="text-[10px] text-amber-600 mt-0.5">by {{ $booking->discountRequestedBy->name }}</div>
 @endif
 @elseif($booking->discount_status === 'rejected')
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Rejected</span>
 @if($booking->discountApprovedBy)
 <div class="text-[10px] text-red-600 mt-0.5">by {{ $booking->discountApprovedBy->name }}</div>
 @endif
 @else
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">-</span>
 @endif
 @else
 <span class="text-gray-400 text-xs">-</span>
 @endif
 </td>
 <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
 <div class="flex gap-2">
 <a href="{{ route('admin.bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-800 transition" title="View">
 <i class="fas fa-eye"></i>
 </a>
 <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-indigo-600 hover:text-indigo-800 transition" title="Edit">
 <i class="fas fa-edit"></i>
 </a>
 <button type="button" onclick="openEmailModal({{ $booking->id }}, '{{ $booking->customer_email ?? '' }}', 'invoice')" class="text-emerald-600 hover:text-emerald-800 transition" title="Send Invoice Email">
 <i class="fas fa-file-invoice"></i>
 </button>
 <button type="button" onclick="openEmailModal({{ $booking->id }}, '{{ $booking->customer_email ?? '' }}', 'reservation')" class="text-amber-600 hover:text-amber-800 transition" title="Send Reservation Email">
 <i class="fas fa-envelope"></i>
 </button>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="12" class="px-6 py-8 text-center text-gray-500">
 <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
 <div>No bookings found</div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
</div>

<!-- Pagination -->
<div class="mt-6">
 {{ $bookings->appends(request()->query())->links() }}
</div>

<script>
// Quick date filter functionality
document.getElementById('dateFilter').addEventListener('change', function(e) {
 const today = new Date();
 let startDate, endDate;
 
 switch(e.target.value) {
 case 'today':
 startDate = endDate = today.toISOString().split('T')[0];
 break;
 case 'yesterday':
 const yesterday = new Date(today);
 yesterday.setDate(yesterday.getDate() - 1);
 startDate = endDate = yesterday.toISOString().split('T')[0];
 break;
 case 'this_week':
 const weekStart = new Date(today);
 weekStart.setDate(today.getDate() - today.getDay());
 startDate = weekStart.toISOString().split('T')[0];
 endDate = today.toISOString().split('T')[0];
 break;
 case 'this_month':
 startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
 endDate = today.toISOString().split('T')[0];
 break;
 case 'last_month':
 const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
 startDate = lastMonth.toISOString().split('T')[0];
 const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
 endDate = lastMonthEnd.toISOString().split('T')[0];
 break;
 }
 
 if (startDate) {
 document.querySelector('input[name="booking_from"]').value = startDate;
 document.querySelector('input[name="booking_to"]').value = endDate;
 }
});

// Email Modal Logic
let currentEmailBookingId = null;
let currentEmailType = null;

function openEmailModal(bookingId, customerEmail, type) {
    currentEmailBookingId = bookingId;
    currentEmailType = type;
    const modal = document.getElementById('emailModal');
    const emailInput = document.getElementById('emailRecipient');
    const title = document.getElementById('emailModalTitle');
    const icon = document.getElementById('emailModalIcon');

    emailInput.value = customerEmail || '';

    if (type === 'invoice') {
        title.textContent = 'Send Invoice Email';
        icon.className = 'fas fa-file-invoice text-emerald-600 text-2xl';
    } else {
        title.textContent = 'Send Reservation Email';
        icon.className = 'fas fa-envelope text-amber-600 text-2xl';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEmailModal() {
    const modal = document.getElementById('emailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentEmailBookingId = null;
    currentEmailType = null;
}

async function sendEmail() {
    const email = document.getElementById('emailRecipient').value;
    if (!email) {
        alert('Please enter an email address');
        return;
    }

    const btn = document.getElementById('sendEmailBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    btn.disabled = true;

    const route = currentEmailType === 'invoice'
        ? `/admin/bookings/${currentEmailBookingId}/send-invoice-email`
        : `/admin/bookings/${currentEmailBookingId}/send-reservation-email`;

    try {
        const response = await fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: email }),
        });

        const data = await response.json();
        if (data.success) {
            alert(data.message);
            closeEmailModal();
        } else {
            alert(data.message || 'Failed to send email');
        }
    } catch (e) {
        alert('Error sending email: ' + e.message);
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}
</script>

<!-- Email Send Modal -->
<div id="emailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <i id="emailModalIcon" class="fas fa-envelope text-amber-600 text-2xl"></i>
                <h3 id="emailModalTitle" class="text-lg font-bold text-gray-800">Send Email</h3>
            </div>
            <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Recipient Email</label>
            <input type="email" id="emailRecipient" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="customer@example.com">
            <p class="text-xs text-gray-500 mt-1">You can edit the email address before sending.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeEmailModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Cancel</button>
            <button id="sendEmailBtn" onclick="sendEmail()" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-paper-plane mr-2"></i>Send
            </button>
        </div>
    </div>
</div>
@endsection

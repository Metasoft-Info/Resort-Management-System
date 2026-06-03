@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="flex items-center justify-between mb-6">
 <div>
 <h1 class="text-3xl font-bold text-gray-800">{{ $customer->customer_name }}</h1>
 <p class="text-gray-600 mt-1">Customer Details | {{ $customer->customer_phone }}</p>
 </div>
 <a href="{{ route('admin.customers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
 <i class="fas fa-arrow-left mr-2"></i>Back
 </a>
 </div>

 <!-- Customer Info Card -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user mr-2 text-primary-600"></i>Customer Information</h2>
 <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
 <div>
 <label class="text-sm text-gray-500">Name</label>
 <p class="font-semibold text-gray-800">{{ $customer->customer_name }}</p>
 </div>
 <div>
 <label class="text-sm text-gray-500">Phone</label>
 <p class="font-semibold text-primary-600">
 <a href="tel:{{ $customer->customer_phone }}">{{ $customer->customer_phone }}</a>
 </p>
 </div>
 <div>
 <label class="text-sm text-gray-500">Email</label>
 <p class="font-semibold text-gray-800">{{ $customer->customer_email ?? '-' }}</p>
 </div>
 <div>
 <label class="text-sm text-gray-500">NID</label>
 <p class="font-semibold text-gray-800">{{ $customer->customer_nid ?? '-' }}</p>
 </div>
 <div>
 <label class="text-sm text-gray-500">Company</label>
 <p class="font-semibold text-gray-800">{{ $customer->company_name ?? '-' }}</p>
 </div>
 <div class="md:col-span-3">
 <label class="text-sm text-gray-500">Address</label>
 <p class="font-semibold text-gray-800">{{ $customer->customer_address ?? '-' }}</p>
 </div>
 </div>
 </div>

 <!-- Stats -->
 <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
 <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-200">
 <p class="text-gray-600 text-xs">Room Bookings</p>
 <p class="text-2xl font-bold text-blue-700">{{ $stats['total_room_bookings'] }}</p>
 </div>
 <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-200">
 <p class="text-gray-600 text-xs">Hall Bookings</p>
 <p class="text-2xl font-bold text-purple-700">{{ $stats['total_convention_bookings'] }}</p>
 </div>
 <div class="bg-green-50 rounded-lg p-4 text-center border border-green-200">
 <p class="text-gray-600 text-xs">Total Spent</p>
 <p class="text-xl font-bold text-green-700">{{ number_format($stats['total_room_spent'] + $stats['total_convention_spent'], 0) }}</p>
 </div>
 <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200">
 <p class="text-gray-600 text-xs">Total Paid</p>
 <p class="text-xl font-bold text-primary-700">{{ number_format($stats['total_room_paid'] + $stats['total_convention_paid'], 0) }}</p>
 </div>
 <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200">
 <p class="text-gray-600 text-xs">Total Due</p>
 <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_room_due'] + $stats['total_convention_due'], 0) }}</p>
 </div>
 <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
 <p class="text-gray-600 text-xs">First Booking</p>
 <p class="text-sm font-bold text-gray-700">{{ $stats['first_booking'] ? \Carbon\Carbon::parse($stats['first_booking'])->format('d M Y') : '-' }}</p>
 </div>
 </div>

 <!-- Room Bookings -->
 <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
 <div class="flex items-center justify-between mb-4">
 <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-bed mr-2 text-blue-600"></i>Room Booking History</h2>
 <a href="{{ route('admin.bookings.create') }}?phone={{ $customer->customer_phone }}&name={{ urlencode($customer->customer_name) }}" 
 class="bg-primary-600 text-white px-3 py-1 rounded text-sm hover:bg-primary-700">
 <i class="fas fa-plus mr-1"></i>New Booking
 </a>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full text-sm">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Date</th>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Room</th>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Check-In</th>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Check-Out</th>
 <th class="px-3 py-2 text-right text-xs font-bold text-gray-600">Total</th>
 <th class="px-3 py-2 text-right text-xs font-bold text-gray-600">Paid</th>
 <th class="px-3 py-2 text-right text-xs font-bold text-gray-600">Due</th>
 <th class="px-3 py-2 text-center text-xs font-bold text-gray-600">Status</th>
 <th class="px-3 py-2 text-center text-xs font-bold text-gray-600">Action</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($roomBookings as $booking)
 <tr class="hover:bg-gray-50">
 <td class="px-3 py-2">{{ $booking->created_at->format('d/m/Y') }}</td>
 <td class="px-3 py-2 font-semibold text-primary-700">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</td>
 <td class="px-3 py-2">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
 <td class="px-3 py-2">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
 <td class="px-3 py-2 text-right font-semibold">{{ number_format($booking->total_amount, 0) }}</td>
 <td class="px-3 py-2 text-right text-green-600">{{ number_format($booking->advance_payment, 0) }}</td>
 <td class="px-3 py-2 text-right text-red-600">{{ number_format($booking->remaining_payment, 0) }}</td>
 <td class="px-3 py-2 text-center">
 <span class="px-2 py-1 rounded-full text-xs font-semibold
 @if($booking->status == 'checked_out') bg-gray-100 text-gray-800
 @elseif($booking->status == 'checked_in') bg-green-100 text-green-800
 @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
 @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
 @else bg-yellow-100 text-yellow-800 @endif">
 {{ $booking->status }}
 </span>
 </td>
 <td class="px-3 py-2 text-center">
 <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-800">
 <i class="fas fa-eye"></i>
 </a>
 </td>
 </tr>
 @empty
 <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">No room bookings</td></tr>
 @endforelse
 </tbody>
 @if($roomBookings->count() > 0)
 <tfoot class="bg-gray-100 font-bold">
 <tr>
 <td colspan="4" class="px-3 py-2 text-right">Total:</td>
 <td class="px-3 py-2 text-right">{{ number_format($stats['total_room_spent'], 0) }}</td>
 <td class="px-3 py-2 text-right text-green-600">{{ number_format($stats['total_room_paid'], 0) }}</td>
 <td class="px-3 py-2 text-right text-red-600">{{ number_format($stats['total_room_due'], 0) }}</td>
 <td colspan="2"></td>
 </tr>
 </tfoot>
 @endif
 </table>
 </div>
 </div>

 <!-- Convention Bookings -->
 <div class="bg-white rounded-xl shadow-lg p-6">
 <div class="flex items-center justify-between mb-4">
 <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-building mr-2 text-purple-600"></i>Convention Booking History</h2>
 <a href="{{ route('admin.convention-bookings.create') }}?phone={{ $customer->customer_phone }}&name={{ urlencode($customer->customer_name) }}" 
 class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700">
 <i class="fas fa-plus mr-1"></i>New Booking
 </a>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full text-sm">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Date</th>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Hall</th>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Event</th>
 <th class="px-3 py-2 text-left text-xs font-bold text-gray-600">Time</th>
 <th class="px-3 py-2 text-right text-xs font-bold text-gray-600">Total</th>
 <th class="px-3 py-2 text-right text-xs font-bold text-gray-600">Paid</th>
 <th class="px-3 py-2 text-right text-xs font-bold text-gray-600">Due</th>
 <th class="px-3 py-2 text-center text-xs font-bold text-gray-600">Status</th>
 <th class="px-3 py-2 text-center text-xs font-bold text-gray-600">Action</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($conventionBookings as $booking)
 <tr class="hover:bg-gray-50">
 <td class="px-3 py-2">{{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</td>
 <td class="px-3 py-2 font-semibold text-purple-700">{{ $booking->conventionHall->name ?? 'N/A' }}</td>
 <td class="px-3 py-2">{{ $booking->event_type ?? '-' }}</td>
 <td class="px-3 py-2">{{ $booking->time_slot ?? '-' }}</td>
 <td class="px-3 py-2 text-right font-semibold">{{ number_format($booking->total_amount, 0) }}</td>
 <td class="px-3 py-2 text-right text-green-600">{{ number_format($booking->advance_payment, 0) }}</td>
 <td class="px-3 py-2 text-right text-red-600">{{ number_format($booking->remaining_payment, 0) }}</td>
 <td class="px-3 py-2 text-center">
 <span class="px-2 py-1 rounded-full text-xs font-semibold
 @if($booking->status == 'completed') bg-gray-100 text-gray-800
 @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800
 @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
 @else bg-yellow-100 text-yellow-800 @endif">
 {{ $booking->status }}
 </span>
 </td>
 <td class="px-3 py-2 text-center">
 <a href="{{ route('admin.convention-bookings.show', $booking) }}" class="text-purple-600 hover:text-purple-800">
 <i class="fas fa-eye"></i>
 </a>
 </td>
 </tr>
 @empty
 <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">No convention bookings</td></tr>
 @endforelse
 </tbody>
 @if($conventionBookings->count() > 0)
 <tfoot class="bg-gray-100 font-bold">
 <tr>
 <td colspan="4" class="px-3 py-2 text-right">Total:</td>
 <td class="px-3 py-2 text-right">{{ number_format($stats['total_convention_spent'], 0) }}</td>
 <td class="px-3 py-2 text-right text-green-600">{{ number_format($stats['total_convention_paid'], 0) }}</td>
 <td class="px-3 py-2 text-right text-red-600">{{ number_format($stats['total_convention_due'], 0) }}</td>
 <td colspan="2"></td>
 </tr>
 </tfoot>
 @endif
 </table>
 </div>
 </div>
</div>
@endsection

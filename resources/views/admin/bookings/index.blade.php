@extends('layouts.admin')

@section('title', 'Bookings')
@section('header', 'Bookings Management')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.bookings.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
        + New Booking
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
                <tr>
                    <td class="px-6 py-4">#{{ $booking->id }}</td>
                    <td class="px-6 py-4">
                        <div>{{ $booking->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $booking->customer_phone }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $booking->room->name }}</td>
                    <td class="px-6 py-4">{{ $booking->check_in_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">{{ $booking->check_out_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">৳{{ number_format($booking->total_amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'checked_in') bg-blue-100 text-blue-800
                            @elseif($booking->status == 'checked_out') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline mr-2">View</a>
                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-blue-600 hover:underline">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No bookings found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $bookings->links() }}
</div>
@endsection

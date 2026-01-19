@extends('layouts.admin')

@section('title', 'Convention Bookings')
@section('header', 'Convention Bookings Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-gray-700">Total Bookings: <span class="text-primary-600">{{ $bookings->total() }}</span></h3>
    </div>
    <a href="{{ route('admin.convention-bookings.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-600 text-white rounded-xl hover:from-primary-700 hover:to-accent-700 transition font-semibold shadow-lg hover:shadow-xl">
        <i class="fas fa-plus mr-2"></i>New Booking
    </a>
</div>

<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-gradient-to-r from-primary-50 to-accent-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-hashtag mr-2 text-primary-600"></i>ID
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-user mr-2 text-primary-600"></i>Customer
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-building mr-2 text-primary-600"></i>Hall
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-calendar mr-2 text-primary-600"></i>Event Date
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-tag mr-2 text-primary-600"></i>Event Type
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-users mr-2 text-primary-600"></i>Guests
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-bangladeshi-taka-sign mr-2 text-primary-600"></i>Total
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Status
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-cog mr-2 text-primary-600"></i>Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-primary-600">#{{ $booking->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">{{ $booking->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $booking->customer_phone }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $booking->conventionHall->name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ \Carbon\Carbon::parse($booking->event_date)->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-xs font-bold">
                            {{ $booking->event_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $booking->number_of_guests }}</td>
                    <td class="px-6 py-4 font-bold text-gray-700">৳{{ number_format($booking->total_amount, 0) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-bold rounded-full inline-flex items-center
                            @if($booking->status == 'confirmed') bg-green-100 text-green-700
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($booking->status == 'completed') bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700
                            @endif">
                            @if($booking->status == 'confirmed')
                                <i class="fas fa-check-circle mr-1"></i>
                            @elseif($booking->status == 'pending')
                                <i class="fas fa-clock mr-1"></i>
                            @elseif($booking->status == 'completed')
                                <i class="fas fa-flag-checkered mr-1"></i>
                            @else
                                <i class="fas fa-times-circle mr-1"></i>
                            @endif
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.convention-bookings.edit', $booking) }}" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs font-semibold inline-flex items-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.convention-bookings.destroy', $booking) }}" method="POST" class="inline" onsubmit="return confirm('Delete booking #{{ $booking->id }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs font-semibold inline-flex items-center">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="text-gray-400 text-6xl mb-3">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <p class="text-gray-500 text-lg font-semibold">No convention bookings found</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $bookings->links() }}
</div>
@endsection

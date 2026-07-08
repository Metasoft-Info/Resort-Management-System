@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'Combined Report',
        'subtitle' => 'Room bookings, advance bookings and outstanding together'
    ])
    @include('admin.reports.partials.shared-styles')

    @if(!request('print'))
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.combined') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Type</label>
                    <select name="room_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All</option>
                        @foreach($roomTypes as $rt)
                            <option value="{{ $rt->id }}" {{ request('room_type_id') == $rt->id ? 'selected' : '' }}>{{ $rt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.reports.combined') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    @endif

    @if(!request('print'))
    <div class="flex gap-2 mb-6 print:hidden">
        <button onclick="window.print()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
            <i class="fas fa-print mr-2"></i>Print
        </button>
        <a href="{{ route('admin.reports.room-bookings', request()->query()) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Room Bookings Report
        </a>
        <a href="{{ route('admin.reports.advance-bookings', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Advance Report
        </a>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6 print:grid-cols-6 print:gap-2 print:text-xs">
        <div class="bg-blue-50 rounded-lg p-4 text-center border border-blue-200">
            <p class="text-gray-600 text-xs">All Rooms</p>
            <p class="text-xl font-bold text-blue-700">{{ $roomBookingsCount }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 text-center border border-green-200">
            <p class="text-gray-600 text-xs">Advance</p>
            <p class="text-xl font-bold text-green-600">{{ $advanceCount }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200">
            <p class="text-gray-600 text-xs">Unpaid Checked-In</p>
            <p class="text-xl font-bold text-red-600">{{ $unpaidCount }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 text-center border border-purple-200">
            <p class="text-gray-600 text-xs">Total Bookings</p>
            <p class="text-xl font-bold text-purple-700">{{ $grandTotalBookings }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200">
            <p class="text-gray-600 text-xs">Revenue</p>
            <p class="text-xl font-bold text-primary-700">BDT {{ number_format($grandTotalRevenue) }}</p>
        </div>
        <div class="bg-orange-50 rounded-lg p-4 text-center border border-orange-200">
            <p class="text-gray-600 text-xs">Outstanding</p>
            <p class="text-xl font-bold text-orange-600">BDT {{ number_format($grandTotalRemaining) }}</p>
        </div>
    </div>

    <!-- Section 1: All Room Bookings -->
    <div class="bg-white rounded-lg shadow mb-6 print:shadow-none print:rounded-none">
        <div class="px-4 py-3 bg-primary-50 border-b border-primary-200 flex items-center justify-between">
            <h2 class="font-bold text-primary-800"><i class="fas fa-bed mr-2"></i>All Room Bookings (Detailed)</h2>
            <span class="text-sm text-gray-600">{{ count($roomBookings) }} bookings</span>
        </div>
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-gray-100">
                    <th class="border border-gray-300 px-2 py-2 text-xs">Date</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Phone</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Name</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Company</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room Type</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Rent</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Discount</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Extra</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Total</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Advance</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Due</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">In</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Out</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-center">Night</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Payment</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Status</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs print:hidden">Action</th>
                </tr></thead>
                <tbody>
                    @forelse($roomBookings as $b)
                    @php
                        $remaining = $b->getCalculatedRemaining();
                        $nights = max(1, \Carbon\Carbon::parse($b->check_in_date)->diffInDays(\Carbon\Carbon::parse($b->check_out_date)));
                        $roomRent = $b->getCalculatedTotal();
                        $roomNumbers = $b->bookingRooms->count() > 0
                            ? $b->bookingRooms->map(fn($br) => $br->room->room_number ?? 'N/A')->implode(', ')
                            : ($b->room->room_number ?? 'N/A');
                        $roomTypeNames = $b->bookingRooms->count() > 0
                            ? $b->bookingRooms->map(fn($br) => $br->room?->roomType?->name)->filter()->unique()->implode(', ')
                            : ($b->room?->roomType?->name ?? 'N/A');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->created_at)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->customer_phone }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->customer_name }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->company_name ?? '-' }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $roomNumbers }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $roomTypeNames }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($roomRent) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->discount_amount ?? 0) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->extra_charges ?? 0) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->advance_payment) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right font-bold {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($remaining) }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_out_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center">{{ $nights }}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($b->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($b->payment_status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : ($b->status == 'checked_in' ? 'bg-green-100 text-green-700' : ($b->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1 print:hidden whitespace-nowrap">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="18" class="border border-gray-300 px-4 py-8 text-center text-gray-500">No bookings found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Advance Bookings -->
    @if($advanceBookings->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6 print:shadow-none print:rounded-none">
        <div class="px-4 py-3 bg-green-50 border-b border-green-200 flex items-center justify-between">
            <h2 class="font-bold text-green-800"><i class="fas fa-calendar-check mr-2"></i>Advance Bookings (Future)</h2>
            <span class="text-sm text-green-600">{{ count($advanceBookings) }} bookings</span>
        </div>
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-green-50">
                    <th class="border border-gray-300 px-2 py-2 text-xs">Date</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Phone</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Name</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Company</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room Type</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Rent</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Discount</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Extra</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Total</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Advance</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Due</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">In</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Out</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-center">Night</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Payment</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Status</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs print:hidden">Action</th>
                </tr></thead>
                <tbody>
                    @forelse($advanceBookings as $b)
                    @php
                        $due = $b->getCalculatedRemaining();
                        $nights = max(1, \Carbon\Carbon::parse($b->check_in_date)->diffInDays(\Carbon\Carbon::parse($b->check_out_date)));
                        $roomRent = $b->getCalculatedTotal();
                        $roomNumbers = $b->bookingRooms->count() > 0
                            ? $b->bookingRooms->map(fn($br) => $br->room->room_number ?? 'N/A')->implode(', ')
                            : ($b->room->room_number ?? 'N/A');
                        $roomTypeNames = $b->bookingRooms->count() > 0
                            ? $b->bookingRooms->map(fn($br) => $br->room?->roomType?->name)->filter()->unique()->implode(', ')
                            : ($b->room?->roomType?->name ?? 'N/A');
                    @endphp
                    <tr class="hover:bg-green-50">
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->created_at)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->customer_phone }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->customer_name }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->company_name ?? '-' }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $roomNumbers }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $roomTypeNames }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($roomRent) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->discount_amount ?? 0) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->extra_charges ?? 0) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right text-green-600">{{ number_format($b->advance_payment) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right font-bold {{ $due > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($due) }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_out_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center">{{ $nights }}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($b->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($b->payment_status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : ($b->status == 'checked_in' ? 'bg-green-100 text-green-700' : ($b->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1 print:hidden whitespace-nowrap">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="18" class="border border-gray-300 px-4 py-8 text-center text-gray-500">No advance bookings found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Section 3: Unpaid Checked-in -->
    @if($unpaidBookings->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6 print:shadow-none print:rounded-none">
        <div class="px-4 py-3 bg-red-50 border-b border-red-200 flex items-center justify-between">
            <h2 class="font-bold text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i>Checked-in Guests with Outstanding Balance</h2>
            <span class="text-sm text-red-600">{{ count($unpaidBookings) }} bookings</span>
        </div>
        <div class="report-table-container">
            <table class="report-table text-sm border border-gray-300">
                <thead><tr class="bg-red-50">
                    <th class="border border-gray-300 px-2 py-2 text-xs">Date</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Phone</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Name</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Company</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Room Type</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Rent</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Discount</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Extra</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Total</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Advance</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-right">Due</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">In</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Out</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs text-center">Night</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Payment</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs">Status</th>
                    <th class="border border-gray-300 px-2 py-2 text-xs print:hidden">Action</th>
                </tr></thead>
                <tbody>
                    @forelse($unpaidBookings as $b)
                    @php
                        $due = $b->getCalculatedRemaining();
                        $nights = max(1, \Carbon\Carbon::parse($b->check_in_date)->diffInDays(\Carbon\Carbon::parse($b->check_out_date)));
                        $roomRent = $b->getCalculatedTotal();
                        $roomNumbers = $b->bookingRooms->count() > 0
                            ? $b->bookingRooms->map(fn($br) => $br->room->room_number ?? 'N/A')->implode(', ')
                            : ($b->room->room_number ?? 'N/A');
                        $roomTypeNames = $b->bookingRooms->count() > 0
                            ? $b->bookingRooms->map(fn($br) => $br->room?->roomType?->name)->filter()->unique()->implode(', ')
                            : ($b->room?->roomType?->name ?? 'N/A');
                    @endphp
                    <tr class="hover:bg-red-50">
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->created_at)->format('d-m-Y') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $b->customer_phone }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->customer_name }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $b->company_name ?? '-' }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ $roomNumbers }}</td>
                        <td class="border border-gray-300 px-2 py-1">{{ $roomTypeNames }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($roomRent) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->discount_amount ?? 0) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->extra_charges ?? 0) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->getGrandTotal()) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($b->advance_payment) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-right font-bold text-red-600">{{ number_format($due) }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->check_out_date)->format('d-m') }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center">{{ $nights }}</td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->payment_status == 'paid' ? 'bg-green-100 text-green-700' : ($b->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($b->payment_status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            <span class="px-2 py-1 rounded text-xs {{ $b->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : ($b->status == 'checked_in' ? 'bg-green-100 text-green-700' : ($b->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1 print:hidden whitespace-nowrap">
                            <a href="{{ route('admin.bookings.show', $b) }}" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="18" class="border border-gray-300 px-4 py-8 text-center text-gray-500">No bookings found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(!request('print'))
    <div class="mt-6 print:hidden">
        <a href="{{ route('admin.reports.room-bookings') }}" class="text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>Room Bookings Report
        </a>
    </div>
    @endif

    @include('admin.reports.partials.shared-footer')
</div>

<style>
@media print {
    @page { size: A4 landscape; margin: 8mm; }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body {
        font-size: 10px !important;
    }

    .print\:hidden {
        display: none !important;
    }

    nav, header, aside, footer {
        display: none !important;
    }

    .lg\:ml-64 {
        margin-left: 0 !important;
    }

    .p-6 {
        padding: 2mm !important;
    }

    .grid.grid-cols-2.md\:grid-cols-6 {
        display: grid !important;
        grid-template-columns: repeat(6, 1fr) !important;
        gap: 2mm !important;
        margin-bottom: 3mm !important;
    }

    .grid.grid-cols-2.md\:grid-cols-6 > div {
        padding: 2mm !important;
        border-radius: 2px !important;
    }

    .grid.grid-cols-2.md\:grid-cols-6 .text-xl {
        font-size: 11px !important;
        line-height: 1.1 !important;
    }

    .grid.grid-cols-2.md\:grid-cols-6 .text-xs {
        font-size: 8px !important;
    }

    .report-table {
        width: 100% !important;
        font-size: 9px !important;
        border-collapse: collapse !important;
    }

    .report-table th,
    .report-table td {
        padding: 3px 5px !important;
        border: 1px solid #666 !important;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
    }

    .report-table .rounded,
    .report-table .rounded.text-xs,
    .report-table span[class*="bg-"] {
        background: transparent !important;
        color: #111827 !important;
        border: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        font-size: 8px !important;
        font-weight: 600 !important;
    }

    .report-table th.print\:hidden,
    .report-table td.print\:hidden {
        display: none !important;
    }

    tr {
        page-break-inside: avoid !important;
    }

    thead {
        display: table-header-group !important;
    }

    tfoot {
        display: table-footer-group !important;
    }

    h2 {
        font-size: 11px !important;
    }
}
</style>
@endsection
